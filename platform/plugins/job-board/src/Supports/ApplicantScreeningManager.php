<?php

namespace Botble\JobBoard\Supports;

use Botble\JobBoard\Models\Job;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ApplicantScreeningManager
{
    public const ACTION_NONE = 'none';
    public const ACTION_HIGHLIGHT = 'highlight';
    public const ACTION_DISQUALIFY = 'disqualify';

    public const LOGIC_AND = 'and';
    public const LOGIC_OR = 'or';

    public const STATUS_NEUTRAL = 'neutral';
    public const STATUS_HIGHLIGHTED = 'highlighted';
    public const STATUS_DISQUALIFIED = 'disqualified';
    public const STATUS_INCOMPLETE = 'incomplete';

    public const OPERATOR_EQUALS = 'equals';
    public const OPERATOR_NOT_EQUALS = 'not_equals';
    public const OPERATOR_CONTAINS = 'contains';
    public const OPERATOR_NOT_CONTAINS = 'not_contains';
    public const OPERATOR_ANSWERED = 'answered';
    public const OPERATOR_NOT_ANSWERED = 'not_answered';

    public static function screeningActionOptions(): array
    {
        return [
            self::ACTION_NONE => __('No automatic action'),
            self::ACTION_HIGHLIGHT => __('Highlight matching applicants'),
            self::ACTION_DISQUALIFY => __('Auto-remove matching applicants'),
        ];
    }

    public static function logicOptions(): array
    {
        return [
            self::LOGIC_AND => __('Match all rules (AND)'),
            self::LOGIC_OR => __('Match any rule (OR)'),
        ];
    }

    public static function operatorOptions(): array
    {
        return [
            self::OPERATOR_EQUALS => __('Equals'),
            self::OPERATOR_NOT_EQUALS => __('Does not equal'),
            self::OPERATOR_CONTAINS => __('Contains'),
            self::OPERATOR_NOT_CONTAINS => __('Does not contain'),
            self::OPERATOR_ANSWERED => __('Was answered'),
            self::OPERATOR_NOT_ANSWERED => __('Was not answered'),
        ];
    }

    public static function screeningStatusOptions(bool $includeAll = true): array
    {
        $options = [];

        if ($includeAll) {
            $options['all'] = __('All applicants');
            $options['active'] = __('Active applicants');
        }

        return $options + [
            self::STATUS_HIGHLIGHTED => __('Highlighted'),
            self::STATUS_DISQUALIFIED => __('Auto-removed'),
            self::STATUS_INCOMPLETE => __('Incomplete'),
            self::STATUS_NEUTRAL => __('Needs review'),
        ];
    }

    public static function screeningStatusLabel(?string $status): string
    {
        return match ($status ?: self::STATUS_NEUTRAL) {
            self::STATUS_HIGHLIGHTED => __('Highlighted'),
            self::STATUS_DISQUALIFIED => __('Auto-removed'),
            self::STATUS_INCOMPLETE => __('Incomplete'),
            default => __('Needs review'),
        };
    }

    public static function screeningStatusColor(?string $status): string
    {
        return match ($status ?: self::STATUS_NEUTRAL) {
            self::STATUS_HIGHLIGHTED => 'success',
            self::STATUS_DISQUALIFIED => 'danger',
            self::STATUS_INCOMPLETE => 'warning',
            default => 'secondary',
        };
    }

    public static function normalizeStoredRules(array $rules, array $questions): array
    {
        $questionsByKey = collect($questions)->keyBy('key');

        return collect($rules)
            ->map(function ($rule) use ($questionsByKey) {
                $questionKey = (string) Arr::get($rule, 'question_key');
                $question = $questionsByKey->get($questionKey);

                if (! $question) {
                    return null;
                }

                return self::normalizeRule($rule, $question);
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function normalizeRulesFromBuilder(array $rules, array $questions): array
    {
        $questionsByIndex = collect(array_values($questions));

        return collect($rules)
            ->map(function ($rule) use ($questionsByIndex) {
                $questionIndex = Arr::get($rule, 'question_index');

                if ($questionIndex === null || $questionIndex === '') {
                    return null;
                }

                $question = $questionsByIndex->get((int) $questionIndex);

                if (! $question) {
                    return null;
                }

                return self::normalizeRule($rule, $question);
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function evaluateApplication(Job $job, ?array $answers): array
    {
        $settings = self::settings($job);
        $questions = ApplicationFormManager::questionsForJob($job);
        $answers = $answers ?: [];

        $missingRequired = [];

        if ($settings['mark_incomplete_required']) {
            foreach ($questions as $question) {
                if (! $question['required']) {
                    continue;
                }

                $value = Arr::get($answers, $question['key'] . '.value');

                if (ApplicationFormManager::emptyAnswer($value)) {
                    $missingRequired[] = [
                        'question_key' => $question['key'],
                        'question_label' => $question['label'],
                        'operator' => self::OPERATOR_NOT_ANSWERED,
                        'expected' => null,
                        'actual' => $value,
                    ];
                }
            }
        }

        if ($missingRequired) {
            return [
                'screening_status' => self::STATUS_INCOMPLETE,
                'screening_summary' => [
                    'type' => self::STATUS_INCOMPLETE,
                    'logic' => self::LOGIC_AND,
                    'action' => self::ACTION_NONE,
                    'matched' => true,
                    'reasons' => $missingRequired,
                ],
            ];
        }

        if ($settings['screening_action'] === self::ACTION_NONE || $settings['screening_rules'] === []) {
            return [
                'screening_status' => self::STATUS_NEUTRAL,
                'screening_summary' => null,
            ];
        }

        $evaluation = self::evaluateRules($answers, $settings['screening_rules'], $settings['screening_logic']);

        if (! $evaluation['matched']) {
            return [
                'screening_status' => self::STATUS_NEUTRAL,
                'screening_summary' => null,
            ];
        }

        return [
            'screening_status' => $settings['screening_action'] === self::ACTION_DISQUALIFY
                ? self::STATUS_DISQUALIFIED
                : self::STATUS_HIGHLIGHTED,
            'screening_summary' => [
                'type' => 'rule_match',
                'logic' => $settings['screening_logic'],
                'action' => $settings['screening_action'],
                'matched' => true,
                'reasons' => $evaluation['reasons'],
            ],
        ];
    }

    public static function evaluateRules(array $answers, array $rules, string $logic = self::LOGIC_AND): array
    {
        $logic = in_array($logic, [self::LOGIC_AND, self::LOGIC_OR], true) ? $logic : self::LOGIC_AND;

        if ($rules === []) {
            return ['matched' => false, 'reasons' => []];
        }

        $evaluations = collect($rules)->map(function (array $rule) use ($answers) {
            $actual = Arr::get($answers, $rule['question_key'] . '.value');
            $matched = self::matchesRule($actual, $rule['operator'], $rule['value']);

            return [
                'matched' => $matched,
                'reason' => [
                    'question_key' => $rule['question_key'],
                    'question_label' => $rule['question_label'],
                    'operator' => $rule['operator'],
                    'expected' => $rule['value'],
                    'actual' => $actual,
                ],
            ];
        });

        $matched = $logic === self::LOGIC_AND
            ? $evaluations->every(fn (array $evaluation) => $evaluation['matched'])
            : $evaluations->contains(fn (array $evaluation) => $evaluation['matched']);

        return [
            'matched' => $matched,
            'reasons' => $matched
                ? $evaluations->where('matched', true)->pluck('reason')->values()->all()
                : [],
        ];
    }

    public static function matchesRule(mixed $actual, string $operator, mixed $expected): bool
    {
        $expectedValue = self::normalizeComparableValue($expected);

        return match ($operator) {
            self::OPERATOR_ANSWERED => ! ApplicationFormManager::emptyAnswer($actual),
            self::OPERATOR_NOT_ANSWERED => ApplicationFormManager::emptyAnswer($actual),
            self::OPERATOR_EQUALS => self::valueEquals($actual, $expectedValue),
            self::OPERATOR_NOT_EQUALS => ! ApplicationFormManager::emptyAnswer($actual) && ! self::valueEquals($actual, $expectedValue),
            self::OPERATOR_CONTAINS => self::valueContains($actual, $expectedValue),
            self::OPERATOR_NOT_CONTAINS => ! ApplicationFormManager::emptyAnswer($actual) && ! self::valueContains($actual, $expectedValue),
            default => false,
        };
    }

    public static function settings(Job $job): array
    {
        $settings = array_merge([
            'auto_highlight' => false,
            'mark_incomplete_required' => true,
            'screening_action' => self::ACTION_NONE,
            'screening_logic' => self::LOGIC_AND,
            'screening_rules' => [],
        ], (array) $job->application_form_settings);

        if (
            $settings['screening_action'] === self::ACTION_NONE &&
            ! empty($settings['auto_highlight']) &&
            ! empty($settings['screening_rules'])
        ) {
            $settings['screening_action'] = self::ACTION_HIGHLIGHT;
        }

        $settings['screening_logic'] = in_array($settings['screening_logic'], [self::LOGIC_AND, self::LOGIC_OR], true)
            ? $settings['screening_logic']
            : self::LOGIC_AND;

        $settings['screening_action'] = in_array($settings['screening_action'], array_keys(self::screeningActionOptions()), true)
            ? $settings['screening_action']
            : self::ACTION_NONE;

        $settings['screening_rules'] = self::normalizeStoredRules(
            (array) $settings['screening_rules'],
            ApplicationFormManager::questionsForJob($job)
        );

        return $settings;
    }

    protected static function normalizeRule(array $rule, array $question): ?array
    {
        $operator = (string) Arr::get($rule, 'operator');

        if (! array_key_exists($operator, self::operatorOptions())) {
            return null;
        }

        $value = Arr::get($rule, 'value');

        if (! in_array($operator, [self::OPERATOR_ANSWERED, self::OPERATOR_NOT_ANSWERED], true)) {
            if (is_array($value)) {
                $value = implode(', ', array_filter(array_map('trim', $value)));
            }

            $value = trim((string) $value);

            if ($value === '') {
                return null;
            }
        } else {
            $value = null;
        }

        return [
            'question_key' => $question['key'],
            'question_label' => $question['label'],
            'question_type' => $question['type'],
            'question_options' => self::questionOptions($question),
            'operator' => $operator,
            'value' => $value,
        ];
    }

    protected static function questionOptions(array $question): array
    {
        if ($question['type'] === ApplicationFormManager::TYPE_YES_NO) {
            return ['yes', 'no'];
        }

        return (array) Arr::get($question, 'options', []);
    }

    protected static function valueEquals(mixed $actual, string $expected): bool
    {
        if (ApplicationFormManager::emptyAnswer($actual)) {
            return false;
        }

        if (is_array($actual)) {
            return collect($actual)
                ->map(fn ($item) => self::normalizeComparableValue($item))
                ->contains($expected);
        }

        return self::normalizeComparableValue($actual) === $expected;
    }

    protected static function valueContains(mixed $actual, string $expected): bool
    {
        if (ApplicationFormManager::emptyAnswer($actual) || $expected === '') {
            return false;
        }

        if (is_array($actual)) {
            return collect($actual)
                ->map(fn ($item) => self::normalizeComparableValue($item))
                ->contains(fn ($item) => Str::contains($item, $expected));
        }

        return Str::contains(self::normalizeComparableValue($actual), $expected);
    }

    protected static function normalizeComparableValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        return Str::lower(trim((string) $value));
    }
}
