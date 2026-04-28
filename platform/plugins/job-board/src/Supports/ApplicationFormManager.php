<?php

namespace Botble\JobBoard\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\JobBoard\Models\Job;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ApplicationFormManager
{
    public const TYPE_SHORT_ANSWER = 'short_answer';
    public const TYPE_PARAGRAPH = 'paragraph';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_PHONE = 'phone';
    public const TYPE_EMAIL = 'email';
    public const TYPE_FILE_UPLOAD = 'file_upload';
    public const TYPE_CV_UPLOAD = 'cv_upload';
    public const TYPE_YES_NO = 'yes_no';

    public static function supportedTypes(): array
    {
        return [
            self::TYPE_SHORT_ANSWER => [
                'label' => __('Short answer'),
                'supports_options' => false,
                'accept' => null,
            ],
            self::TYPE_PARAGRAPH => [
                'label' => __('Paragraph'),
                'supports_options' => false,
                'accept' => null,
            ],
            self::TYPE_MULTIPLE_CHOICE => [
                'label' => __('Multiple choice'),
                'supports_options' => true,
                'accept' => null,
            ],
            self::TYPE_CHECKBOX => [
                'label' => __('Checkbox'),
                'supports_options' => true,
                'accept' => null,
            ],
            self::TYPE_PHONE => [
                'label' => __('Phone'),
                'supports_options' => false,
                'accept' => null,
            ],
            self::TYPE_EMAIL => [
                'label' => __('Email'),
                'supports_options' => false,
                'accept' => null,
            ],
            self::TYPE_FILE_UPLOAD => [
                'label' => __('File upload'),
                'supports_options' => false,
                'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.webp',
            ],
            self::TYPE_CV_UPLOAD => [
                'label' => __('CV upload'),
                'supports_options' => false,
                'accept' => '.pdf,.doc,.docx',
            ],
            self::TYPE_YES_NO => [
                'label' => __('Yes/No'),
                'supports_options' => false,
                'accept' => null,
            ],
        ];
    }

    public static function supportedTypeOptions(): array
    {
        return collect(self::supportedTypes())
            ->mapWithKeys(fn (array $type, string $key) => [$key => $type['label']])
            ->all();
    }

    public static function supportsOptions(string $type): bool
    {
        return (bool) Arr::get(self::supportedTypes(), $type . '.supports_options');
    }

    public static function isFileType(string $type): bool
    {
        return in_array($type, [self::TYPE_FILE_UPLOAD, self::TYPE_CV_UPLOAD], true);
    }

    public static function questionsForJob(?Job $job): array
    {
        if (! $job || $job->application_mode !== 'custom') {
            return [];
        }

        return self::normalizeQuestions((array) Arr::get($job->application_form_schema, 'questions', []));
    }

    public static function normalizeQuestions(array $questions): array
    {
        $supportedTypes = self::supportedTypes();
        $normalized = [];
        $usedKeys = [];

        foreach (array_values($questions) as $index => $question) {
            $type = Arr::get($question, 'type');

            if (! isset($supportedTypes[$type])) {
                continue;
            }

            $label = trim((string) Arr::get($question, 'label'));

            if ($label === '') {
                continue;
            }

            $candidateKey = Str::slug((string) Arr::get($question, 'key', $label), '_');
            $key = $candidateKey !== '' ? $candidateKey : 'field_' . ($index + 1);

            while (in_array($key, $usedKeys, true)) {
                $key .= '_' . ($index + 1);
            }

            $usedKeys[] = $key;

            $options = [];

            if (self::supportsOptions($type)) {
                $rawOptions = Arr::get($question, 'options', []);

                if (is_string($rawOptions)) {
                    $rawOptions = preg_split('/\r\n|\r|\n|,/', $rawOptions) ?: [];
                }

                $options = collect((array) $rawOptions)
                    ->map(fn ($option) => trim((string) $option))
                    ->filter()
                    ->unique()
                    ->values()
                    ->take(12)
                    ->all();

                if (count($options) < 2) {
                    continue;
                }
            }

            $normalized[] = [
                'key' => $key,
                'label' => $label,
                'type' => $type,
                'required' => (bool) Arr::get($question, 'required'),
                'placeholder' => trim((string) Arr::get($question, 'placeholder')),
                'help_text' => trim((string) Arr::get($question, 'help_text')),
                'options' => $options,
                'accept' => Arr::get($supportedTypes, $type . '.accept'),
            ];
        }

        return $normalized;
    }

    public static function validationRules(Job $job): array
    {
        $rules = [];
        $attributes = [];

        foreach (self::questionsForJob($job) as $question) {
            $key = $question['key'];
            $label = $question['label'];
            $attributes['custom_answers.' . $key] = $label;
            $attributes['custom_answer_files.' . $key] = $label;

            switch ($question['type']) {
                case self::TYPE_SHORT_ANSWER:
                    $rules['custom_answers.' . $key] = self::requiredableRules($question['required'], ['string', 'max:255']);
                    break;
                case self::TYPE_PARAGRAPH:
                    $rules['custom_answers.' . $key] = self::requiredableRules($question['required'], ['string', 'max:2000']);
                    break;
                case self::TYPE_EMAIL:
                    $rules['custom_answers.' . $key] = self::requiredableRules($question['required'], ['email', 'max:255']);
                    break;
                case self::TYPE_PHONE:
                    $rules['custom_answers.' . $key] = self::requiredableRules($question['required'], [BaseHelper::getPhoneValidationRule()]);
                    break;
                case self::TYPE_YES_NO:
                    $rules['custom_answers.' . $key] = self::requiredableRules($question['required'], ['in:yes,no']);
                    break;
                case self::TYPE_MULTIPLE_CHOICE:
                    $rules['custom_answers.' . $key] = self::requiredableRules($question['required'], ['in:' . implode(',', $question['options'])]);
                    break;
                case self::TYPE_CHECKBOX:
                    $rules['custom_answers.' . $key] = $question['required'] ? ['required', 'array', 'min:1'] : ['nullable', 'array'];
                    $rules['custom_answers.' . $key . '.*'] = ['string', 'in:' . implode(',', $question['options'])];
                    break;
                case self::TYPE_FILE_UPLOAD:
                    $rules['custom_answer_files.' . $key] = $question['required']
                        ? ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp']
                        : ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp'];
                    break;
                case self::TYPE_CV_UPLOAD:
                    $rules['custom_answer_files.' . $key] = $question['required']
                        ? ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx']
                        : ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx'];
                    break;
            }
        }

        return [$rules, $attributes];
    }

    public static function emptyAnswer(mixed $value): bool
    {
        if (is_array($value)) {
            return count(array_filter($value, fn ($item) => $item !== null && $item !== '')) === 0;
        }

        return $value === null || $value === '';
    }

    protected static function requiredableRules(bool $required, array $rules): array
    {
        return $required ? ['required', ...$rules] : ['nullable', ...$rules];
    }
}
