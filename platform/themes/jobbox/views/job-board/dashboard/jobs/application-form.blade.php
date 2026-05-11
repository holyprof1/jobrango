@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@php
    $builderQuestions = old('questions', $questions ?? []);
    $savedScreeningRules = $screeningRules ?? [];
    $questionIndexMap = collect($questions ?? [])->pluck('key')->flip();
    $builderScreeningRules = old('screening_rules');

    if (! is_array($builderScreeningRules)) {
        $builderScreeningRules = collect($savedScreeningRules)
            ->map(function ($rule) use ($questionIndexMap) {
                $questionIndex = $questionIndexMap->get($rule['question_key']);

                if ($questionIndex === null) {
                    return null;
                }

                return [
                    'question_index' => $questionIndex,
                    'operator' => $rule['operator'] ?? 'equals',
                    'value' => $rule['value'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
@endphp

@section('content')
    <div class="jobrango-employer-panel-card">
        <div class="jobrango-employer-panel-card__header">
            <div>
                <h3>{{ __('Customize Application Form') }}</h3>
                <p>{{ __('Shape how candidates apply for :job and collect the exact information you need from the start.', ['job' => $job->name]) }}</p>
            </div>
            <a href="{{ route('public.account.jobs.edit', $job->id) }}">{{ __('Back to Job Edit') }}</a>
        </div>

        <div class="jobrango-step-indicator">
            <span class="is-complete">{{ __('1. Job Saved') }}</span>
            <span class="is-active">{{ __('2. Customize Application Form') }}</span>
            <span>{{ __('3. Review Applicants') }}</span>
        </div>

        <form method="POST" action="{{ route('public.account.jobs.application-form.update', $job->id) }}">
            @csrf

            <div class="jobrango-application-mode-grid">
                <label class="jobrango-choice-card">
                    <input type="radio" name="application_mode" value="basic" @checked(old('application_mode', $job->application_mode ?: 'basic') === 'basic')>
                    <span class="jobrango-choice-card__content">
                        <strong>{{ __('Basic application') }}</strong>
                        <small>{{ __('Keep the standard JobRango apply flow with the usual candidate details.') }}</small>
                    </span>
                </label>
                <label class="jobrango-choice-card">
                    <input type="radio" name="application_mode" value="custom" @checked(old('application_mode', $job->application_mode) === 'custom')>
                    <span class="jobrango-choice-card__content">
                        <strong>{{ __('Custom application') }}</strong>
                        <small>{{ __('Build a role-specific form with screening questions, choices, uploads, and extra requirements.') }}</small>
                    </span>
                </label>
            </div>

            <div class="jobrango-application-placeholder mt-4">
                <div>
                    <span class="jobrango-overview__eyebrow">{{ __('Candidate View') }}</span>
                    <h4>{{ __('Applicants always start with the essentials') }}</h4>
                    <p>{{ __('The public apply page always keeps the standard contact fields below. Switch to custom mode to add your own screening questions underneath them for this role.') }}</p>
                </div>
                <div class="jobrango-application-placeholder__types">
                    <span>{{ __('First name') }}</span>
                    <span>{{ __('Last name') }}</span>
                    <span>{{ __('Email') }}</span>
                    <span>{{ __('Phone') }}</span>
                    <span>{{ __('Message') }}</span>
                    <span>{{ __('Resume') }}</span>
                    <span>{{ __('Cover letter') }}</span>
                    <span data-custom-question-count>{{ __('Custom questions: :count', ['count' => count($builderQuestions)]) }}</span>
                </div>
            </div>

            <div class="jobrango-application-settings mt-4">
                <h4>{{ __('Screening & Completion Rules') }}</h4>
                <label class="cb-container">
                    <input type="checkbox" name="mark_incomplete_required" value="1" @checked(old('mark_incomplete_required', $applicationSettings['mark_incomplete_required']))>
                    <span class="text-small">{{ __('Mark candidates as incomplete if they skip required custom questions.') }}</span>
                    <span class="checkmark"></span>
                </label>
                <div class="row g-3 mt-2">
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('Automatic screening action') }}</label>
                        <select class="form-control" name="screening_action" data-screening-action>
                            @foreach (\Botble\JobBoard\Supports\ApplicantScreeningManager::screeningActionOptions() as $value => $label)
                                <option value="{{ $value }}" @selected(old('screening_action', $applicationSettings['screening_action']) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small>{{ __('Use this to automatically highlight strong matches or hide auto-removed applicants from the default active list.') }}</small>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('Rule logic') }}</label>
                        <select class="form-control" name="screening_logic" data-screening-logic>
                            @foreach (\Botble\JobBoard\Supports\ApplicantScreeningManager::logicOptions() as $value => $label)
                                <option value="{{ $value }}" @selected(old('screening_logic', $applicationSettings['screening_logic']) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small>{{ __('Choose whether every rule must match, or if any single rule is enough.') }}</small>
                    </div>
                </div>
            </div>

            <div class="jobrango-application-builder mt-4" data-screening-builder>
                <div class="jobrango-application-builder__header">
                    <div>
                        <span class="jobrango-overview__eyebrow">{{ __('Auto Screening Rules') }}</span>
                        <h4>{{ __('Tell the system who to flag or remove') }}</h4>
                        <p>{{ __('Example: if a candidate answers "No" to relocation and "No" to remote work, you can combine those with AND or OR and decide what happens automatically.') }}</p>
                    </div>
                    <button class="btn btn-border hover-up" type="button" data-add-screening-rule>{{ __('Add Rule') }}</button>
                </div>

                <div class="jobrango-application-builder__list" data-screening-rule-list></div>

                <div class="jobrango-empty-state jobrango-empty-state--compact d-none" data-empty-screening-builder>
                    <h4>{{ __('No screening rules yet') }}</h4>
                    <p>{{ __('Add rules after you define your custom questions so the employer dashboard can highlight or auto-remove applicants based on their answers.') }}</p>
                </div>
            </div>

            <div class="jobrango-application-builder mt-4" data-application-builder>
                <div class="jobrango-application-builder__header">
                    <div>
                        <span class="jobrango-overview__eyebrow">{{ __('Custom Questions') }}</span>
                        <h4>{{ __('Build your application form') }}</h4>
                        <p>{{ __('Core fields like candidate name, email, phone, message, and resume still remain available. Add any extra screening questions below.') }}</p>
                    </div>
                    <button class="btn btn-border hover-up" type="button" data-add-question>{{ __('Add Question') }}</button>
                </div>

                <div class="jobrango-application-builder__list" data-question-list></div>

                <div class="jobrango-empty-state jobrango-empty-state--compact d-none" data-empty-builder>
                    <h4>{{ __('No custom questions yet') }}</h4>
                    <p>{{ __('Add the first question to start shaping this role\'s application form.') }}</p>
                </div>
            </div>

            <div class="jobrango-employer-form-actions">
                <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Save Application Settings') }}</button>
                <a class="btn btn-border hover-up" href="{{ route('public.account.jobs.index') }}">{{ __('Back to Jobs') }}</a>
                <a class="btn btn-border hover-up" href="{{ route('public.account.applicants.index', ['job_id' => $job->id]) }}">{{ __('View Applicants') }}</a>
            </div>
        </form>
    </div>

    <template id="jobrango-question-template">
        <article class="jobrango-question-card" data-question-item>
            <div class="jobrango-question-card__header">
                <div>
                    <span class="jobrango-question-card__badge">{{ __('Question') }}</span>
                    <strong data-question-title>{{ __('New question') }}</strong>
                </div>
                <button class="btn btn-link p-0 text-danger" type="button" data-remove-question>{{ __('Remove') }}</button>
            </div>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">{{ __('Question label') }}</label>
                    <input class="form-control" type="text" data-field="label" maxlength="255" placeholder="{{ __('Example: Why are you a great fit for this role?') }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">{{ __('Field type') }}</label>
                    <select class="form-control" data-field="type">
                        @foreach ($supportedQuestionTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">{{ __('Required') }}</label>
                    <div class="jobrango-question-card__toggle">
                        <label class="cb-container">
                            <input type="checkbox" value="1" data-field="required">
                            <span class="text-small">{{ __('Candidate must answer this') }}</span>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('Placeholder') }}</label>
                    <input class="form-control" type="text" data-field="placeholder" maxlength="255" placeholder="{{ __('Optional input hint') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('Help text') }}</label>
                    <input class="form-control" type="text" data-field="help_text" maxlength="500" placeholder="{{ __('Optional instruction shown below the field') }}">
                </div>
                <div class="col-12 d-none" data-options-wrap>
                    <label class="form-label">{{ __('Options') }}</label>
                    <textarea class="form-control" rows="5" data-field="options" placeholder="{{ __('Enter one option per line') }}"></textarea>
                    <small>{{ __('Use at least two options for multiple choice and checkbox questions.') }}</small>
                </div>
            </div>
        </article>
    </template>

    <template id="jobrango-screening-rule-template">
        <article class="jobrango-question-card" data-screening-rule-item>
            <div class="jobrango-question-card__header">
                <div>
                    <span class="jobrango-question-card__badge">{{ __('Screening rule') }}</span>
                    <strong>{{ __('Answer-based filter') }}</strong>
                </div>
                <button class="btn btn-link p-0 text-danger" type="button" data-remove-screening-rule>{{ __('Remove') }}</button>
            </div>
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">{{ __('Question') }}</label>
                    <select class="form-control" data-rule-field="question_index"></select>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('Condition') }}</label>
                    <select class="form-control" data-rule-field="operator">
                        @foreach (\Botble\JobBoard\Supports\ApplicantScreeningManager::operatorOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4" data-rule-value-wrap>
                    <label class="form-label">{{ __('Value') }}</label>
                    <input class="form-control" type="text" data-rule-value-text placeholder="{{ __('Example: yes, remote, weekend') }}">
                    <select class="form-control d-none" data-rule-value-select></select>
                </div>
            </div>
        </article>
    </template>
@endsection

@push('footer')
    <script>
        window.addEventListener('load', function () {
            const builder = document.querySelector('[data-application-builder]');

            if (! builder) {
                return;
            }

            const modeInputs = document.querySelectorAll('input[name="application_mode"]');
            const questionList = builder.querySelector('[data-question-list]');
            const emptyState = builder.querySelector('[data-empty-builder]');
            const template = document.getElementById('jobrango-question-template');
            const addQuestionButton = builder.querySelector('[data-add-question]');
            const customQuestionCount = document.querySelector('[data-custom-question-count]');
            const supportedOptionTypes = ['multiple_choice', 'checkbox'];
            const initialQuestions = @json(array_values($builderQuestions));
            const screeningBuilder = document.querySelector('[data-screening-builder]');
            const screeningTemplate = document.getElementById('jobrango-screening-rule-template');
            const screeningRuleList = screeningBuilder?.querySelector('[data-screening-rule-list]');
            const screeningEmptyState = screeningBuilder?.querySelector('[data-empty-screening-builder]');
            const addScreeningRuleButton = screeningBuilder?.querySelector('[data-add-screening-rule]');
            const screeningActionField = document.querySelector('[data-screening-action]');
            const screeningInitialRules = @json(array_values($builderScreeningRules));
            const operatorsWithoutValue = ['answered', 'not_answered'];
            const selectQuestionText = @json(__('Select a custom question'));
            const selectOptionText = @json(__('Select an option'));

            const questionName = (index, field) => `questions[${index}][${field}]`;
            const screeningRuleName = (index, field) => `screening_rules[${index}][${field}]`;

            const toggleBuilderMode = () => {
                const selectedMode = document.querySelector('input[name="application_mode"]:checked')?.value;
                builder.classList.toggle('is-disabled', selectedMode !== 'custom');
                addQuestionButton.disabled = selectedMode !== 'custom';

                if (! customQuestionCount) {
                    return;
                }

                if (selectedMode !== 'custom') {
                    customQuestionCount.textContent = '{{ __('Custom questions: off in basic mode') }}';
                    toggleScreeningBuilderMode();

                    return;
                }

                customQuestionCount.textContent = `{{ __('Custom questions') }}: ${questionList.children.length}`;

                toggleScreeningBuilderMode();
            };

            const toggleEmptyState = () => {
                emptyState.classList.toggle('d-none', questionList.children.length !== 0);
            };

            const toggleScreeningEmptyState = () => {
                if (! screeningEmptyState || ! screeningRuleList) {
                    return;
                }

                screeningEmptyState.classList.toggle('d-none', screeningRuleList.children.length !== 0);
            };

            const refreshIndices = () => {
                Array.from(questionList.children).forEach((item, index) => {
                    item.querySelectorAll('[data-field]').forEach((field) => {
                        const fieldName = field.getAttribute('data-field');
                        field.setAttribute('name', questionName(index, fieldName));
                    });
                });
            };

            const collectQuestionDefinitions = () => {
                return Array.from(questionList.children)
                    .map((item, index) => {
                        const label = item.querySelector('[data-field="label"]').value.trim();
                        const type = item.querySelector('[data-field="type"]').value;
                        const optionsText = item.querySelector('[data-field="options"]').value || '';
                        const options = optionsText
                            .split(/\r\n|\r|\n|,/)
                            .map((option) => option.trim())
                            .filter(Boolean);

                        return {
                            index: String(index),
                            label,
                            type,
                            options: type === 'yes_no' ? ['yes', 'no'] : options,
                        };
                    })
                    .filter((question) => question.label);
            };

            const populateScreeningQuestionSelect = (select, selectedValue = '') => {
                const questions = collectQuestionDefinitions();
                const fallbackOption = `<option value="">${selectQuestionText}</option>`;

                select.innerHTML = fallbackOption + questions
                    .map((question) => `<option value="${question.index}">${question.label}</option>`)
                    .join('');

                if (selectedValue !== '' && questions.some((question) => question.index === String(selectedValue))) {
                    select.value = String(selectedValue);
                }
            };

            const toggleScreeningRuleValue = (item) => {
                const questionSelect = item.querySelector('[data-rule-field="question_index"]');
                const operatorSelect = item.querySelector('[data-rule-field="operator"]');
                const textInput = item.querySelector('[data-rule-value-text]');
                const selectInput = item.querySelector('[data-rule-value-select]');
                const valueWrap = item.querySelector('[data-rule-value-wrap]');
                const selectedQuestion = collectQuestionDefinitions().find((question) => question.index === questionSelect.value);

                if (operatorsWithoutValue.includes(operatorSelect.value)) {
                    valueWrap.classList.add('d-none');
                    textInput.disabled = true;
                    selectInput.disabled = true;

                    return;
                }

                valueWrap.classList.remove('d-none');

                if (selectedQuestion && selectedQuestion.options.length) {
                    selectInput.innerHTML = `<option value="">${selectOptionText}</option>` + selectedQuestion.options
                        .map((option) => `<option value="${option.replace(/"/g, '&quot;')}">${option}</option>`)
                        .join('');

                    const selectedValue = selectInput.getAttribute('data-selected-value') || textInput.value || '';
                    if (selectedValue) {
                        selectInput.value = selectedValue;
                    }

                    selectInput.classList.remove('d-none');
                    selectInput.disabled = false;
                    textInput.classList.add('d-none');
                    textInput.disabled = true;
                    textInput.value = selectInput.value;
                } else {
                    selectInput.classList.add('d-none');
                    selectInput.disabled = true;
                    textInput.classList.remove('d-none');
                    textInput.disabled = false;
                }
            };

            const refreshScreeningRuleIndices = () => {
                if (! screeningRuleList) {
                    return;
                }

                Array.from(screeningRuleList.children).forEach((item, index) => {
                    item.querySelector('[data-rule-field="question_index"]').setAttribute('name', screeningRuleName(index, 'question_index'));
                    item.querySelector('[data-rule-field="operator"]').setAttribute('name', screeningRuleName(index, 'operator'));
                    item.querySelector('[data-rule-value-text]').setAttribute('name', screeningRuleName(index, 'value'));
                    item.querySelector('[data-rule-value-select]').removeAttribute('name');
                });
            };

            const toggleScreeningBuilderMode = () => {
                if (! screeningBuilder || ! addScreeningRuleButton || ! screeningActionField) {
                    return;
                }

                const selectedMode = document.querySelector('input[name="application_mode"]:checked')?.value;
                const hasQuestions = collectQuestionDefinitions().length > 0;
                const enabled = selectedMode === 'custom' && screeningActionField.value !== 'none';

                screeningBuilder.classList.toggle('is-disabled', ! enabled);
                addScreeningRuleButton.disabled = ! enabled || ! hasQuestions;
            };

            const refreshScreeningQuestionOptions = () => {
                if (! screeningRuleList) {
                    return;
                }

                Array.from(screeningRuleList.children).forEach((item) => {
                    const questionSelect = item.querySelector('[data-rule-field="question_index"]');
                    const previousValue = questionSelect.value;
                    populateScreeningQuestionSelect(questionSelect, previousValue);
                    toggleScreeningRuleValue(item);
                });

                toggleScreeningBuilderMode();
            };

            const toggleOptions = (item) => {
                const type = item.querySelector('[data-field="type"]').value;
                const optionsWrap = item.querySelector('[data-options-wrap]');
                optionsWrap.classList.toggle('d-none', ! supportedOptionTypes.includes(type));
            };

            const updateTitle = (item) => {
                const label = item.querySelector('[data-field="label"]').value.trim();
                item.querySelector('[data-question-title]').textContent = label || '{{ __('New question') }}';
            };

            const bindQuestionItem = (item) => {
                item.querySelector('[data-remove-question]').addEventListener('click', function () {
                    item.remove();
                    refreshIndices();
                    toggleEmptyState();
                    toggleBuilderMode();
                    refreshScreeningQuestionOptions();
                });

                item.querySelector('[data-field="type"]').addEventListener('change', function () {
                    toggleOptions(item);
                    refreshScreeningQuestionOptions();
                });

                item.querySelector('[data-field="label"]').addEventListener('input', function () {
                    updateTitle(item);
                    refreshScreeningQuestionOptions();
                });

                item.querySelector('[data-field="options"]').addEventListener('input', function () {
                    refreshScreeningQuestionOptions();
                });
            };

            const bindScreeningRuleItem = (item) => {
                item.querySelector('[data-remove-screening-rule]').addEventListener('click', function () {
                    item.remove();
                    refreshScreeningRuleIndices();
                    toggleScreeningEmptyState();
                    toggleScreeningBuilderMode();
                });

                item.querySelector('[data-rule-field="question_index"]').addEventListener('change', function () {
                    toggleScreeningRuleValue(item);
                });

                item.querySelector('[data-rule-field="operator"]').addEventListener('change', function () {
                    toggleScreeningRuleValue(item);
                });

                item.querySelector('[data-rule-value-select]').addEventListener('change', function () {
                    item.querySelector('[data-rule-value-text]').value = this.value;
                    this.setAttribute('data-selected-value', this.value);
                });
            };

            const addQuestion = (question = {}) => {
                const fragment = template.content.cloneNode(true);
                const item = fragment.querySelector('[data-question-item]');

                item.querySelector('[data-field="label"]').value = question.label || '';
                item.querySelector('[data-field="type"]').value = question.type || 'short_answer';
                item.querySelector('[data-field="placeholder"]').value = question.placeholder || '';
                item.querySelector('[data-field="help_text"]').value = question.help_text || '';
                item.querySelector('[data-field="required"]').checked = Boolean(question.required);
                item.querySelector('[data-field="options"]').value = Array.isArray(question.options) ? question.options.join('\n') : (question.options || '');

                bindQuestionItem(item);
                questionList.appendChild(fragment);

                const appendedItem = questionList.lastElementChild;
                toggleOptions(appendedItem);
                updateTitle(appendedItem);
                refreshIndices();
                toggleEmptyState();
                toggleBuilderMode();
                refreshScreeningQuestionOptions();
            };

            const addScreeningRule = (rule = {}) => {
                if (! screeningTemplate || ! screeningRuleList) {
                    return;
                }

                const fragment = screeningTemplate.content.cloneNode(true);
                const item = fragment.querySelector('[data-screening-rule-item]');
                const questionSelect = item.querySelector('[data-rule-field="question_index"]');
                const operatorSelect = item.querySelector('[data-rule-field="operator"]');
                const textInput = item.querySelector('[data-rule-value-text]');
                const selectInput = item.querySelector('[data-rule-value-select]');

                populateScreeningQuestionSelect(questionSelect, rule.question_index ?? '');
                operatorSelect.value = rule.operator || 'equals';
                textInput.value = rule.value || '';
                selectInput.setAttribute('data-selected-value', rule.value || '');

                bindScreeningRuleItem(item);
                screeningRuleList.appendChild(fragment);

                const appendedItem = screeningRuleList.lastElementChild;
                toggleScreeningRuleValue(appendedItem);
                refreshScreeningRuleIndices();
                toggleScreeningEmptyState();
                toggleScreeningBuilderMode();
            };

            initialQuestions.forEach((question) => addQuestion(question));

            if (! initialQuestions.length) {
                toggleEmptyState();
            }

            screeningInitialRules.forEach((rule) => addScreeningRule(rule));

            if (! screeningInitialRules.length) {
                toggleScreeningEmptyState();
            }

            addQuestionButton.addEventListener('click', function () {
                addQuestion();
            });

            if (addScreeningRuleButton) {
                addScreeningRuleButton.addEventListener('click', function () {
                    addScreeningRule();
                });
            }

            modeInputs.forEach((input) => {
                input.addEventListener('change', toggleBuilderMode);
            });

            if (screeningActionField) {
                screeningActionField.addEventListener('change', toggleScreeningBuilderMode);
            }

            toggleBuilderMode();
            refreshScreeningQuestionOptions();
        });
    </script>
@endpush
