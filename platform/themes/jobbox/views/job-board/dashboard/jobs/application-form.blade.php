@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@php
    $builderQuestions = old('questions', $questions ?? []);
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

            <div class="jobrango-application-settings mt-4">
                <h4>{{ __('Screening & Completion Rules') }}</h4>
                <label class="cb-container">
                    <input type="checkbox" name="auto_highlight" value="1" @checked(old('auto_highlight', $applicationSettings['auto_highlight']))>
                    <span class="text-small">{{ __('Auto-highlight strong applicants based on the answers they submit for this job.') }}</span>
                    <span class="checkmark"></span>
                </label>
                <label class="cb-container">
                    <input type="checkbox" name="mark_incomplete_required" value="1" @checked(old('mark_incomplete_required', $applicationSettings['mark_incomplete_required']))>
                    <span class="text-small">{{ __('Mark candidates as incomplete if they skip required custom questions.') }}</span>
                    <span class="checkmark"></span>
                </label>
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
            const supportedOptionTypes = ['multiple_choice', 'checkbox'];
            const initialQuestions = @json(array_values($builderQuestions));

            const questionName = (index, field) => `questions[${index}][${field}]`;

            const toggleBuilderMode = () => {
                const selectedMode = document.querySelector('input[name="application_mode"]:checked')?.value;
                builder.classList.toggle('is-disabled', selectedMode !== 'custom');
            };

            const toggleEmptyState = () => {
                emptyState.classList.toggle('d-none', questionList.children.length !== 0);
            };

            const refreshIndices = () => {
                Array.from(questionList.children).forEach((item, index) => {
                    item.querySelectorAll('[data-field]').forEach((field) => {
                        const fieldName = field.getAttribute('data-field');
                        field.setAttribute('name', questionName(index, fieldName));
                    });
                });
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
                });

                item.querySelector('[data-field="type"]').addEventListener('change', function () {
                    toggleOptions(item);
                });

                item.querySelector('[data-field="label"]').addEventListener('input', function () {
                    updateTitle(item);
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
            };

            initialQuestions.forEach((question) => addQuestion(question));

            if (! initialQuestions.length) {
                toggleEmptyState();
            }

            addQuestionButton.addEventListener('click', function () {
                addQuestion();
            });

            modeInputs.forEach((input) => {
                input.addEventListener('change', toggleBuilderMode);
            });

            toggleBuilderMode();
        });
    </script>
@endpush
