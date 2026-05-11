@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@php
    $initialAnswerFilters = $applicantFilters['answer_filters'] ?? [];
    $answerFilterQuestionDefinitions = collect($applicationQuestions ?? [])->map(function ($question) {
        return [
            'key' => $question['key'],
            'label' => $question['label'],
            'type' => $question['type'],
            'options' => $question['type'] === 'yes_no' ? ['yes', 'no'] : ($question['options'] ?? []),
        ];
    })->values()->all();
@endphp

@section('content')
    <div class="jobrango-employer-panel-card">
        <div class="jobrango-employer-panel-card__header">
            <div>
                <h3>{{ $selectedJob ? __('Applicants for :job', ['job' => $selectedJob->name]) : __('Applicants by Job') }}</h3>
                <p>{{ __('Keep applications grouped by role so every review session has clear context.') }}</p>
            </div>
            @if ($selectedJob)
                <a href="{{ route('public.account.applicants.index') }}">{{ __('Back to All Jobs') }}</a>
            @endif
        </div>

        @if ($selectedJob)
            <div class="jobrango-applicant-job-summary">
                <article>
                    <span>{{ __('Job') }}</span>
                    <strong>{{ $selectedJob->name }}</strong>
                    <p>{{ $selectedJob->display_id }}</p>
                </article>
                <article>
                    <span>{{ __('Company') }}</span>
                    <strong>{{ $selectedJob->company?->name ?: __('No company selected') }}</strong>
                    <p>{{ $selectedJob->display_location ?: __('Location not specified') }}</p>
                </article>
                <article>
                    <span>{{ __('Applicants') }}</span>
                    <strong>{{ number_format($selectedJob->applicants_count) }}</strong>
                    <p>{{ __('New applicants: :count', ['count' => number_format($selectedJob->new_applicants_count)]) }}</p>
                </article>
            </div>

            <div class="jobrango-applicant-job-summary mt-4">
                <article>
                    <span>{{ __('Active list') }}</span>
                    <strong>{{ number_format($applicationStats['active'] ?? 0) }}</strong>
                    <p>{{ __('Applicants still visible in the default review list') }}</p>
                </article>
                <article>
                    <span>{{ __('Highlighted') }}</span>
                    <strong>{{ number_format($applicationStats['highlighted'] ?? 0) }}</strong>
                    <p>{{ __('Strong matches found by the employer rules') }}</p>
                </article>
                <article>
                    <span>{{ __('Auto-removed') }}</span>
                    <strong>{{ number_format($applicationStats['disqualified'] ?? 0) }}</strong>
                    <p>{{ __('Applicants matched a remove rule and are hidden from active view') }}</p>
                </article>
            </div>

            <div class="jobrango-application-builder mt-4" data-answer-filter-builder>
                <div class="jobrango-application-builder__header">
                    <div>
                        <span class="jobrango-overview__eyebrow">{{ __('Applicant Filters') }}</span>
                        <h4>{{ __('Filter by answer, status, and screening result') }}</h4>
                        <p>{{ __('Combine multiple answer rules with AND or OR so employers can review only the candidates that match the exact criteria for this job.') }}</p>
                    </div>
                    <button class="btn btn-border hover-up" type="button" data-add-answer-filter>{{ __('Add Filter Rule') }}</button>
                </div>

                <form method="GET" action="{{ route('public.account.applicants.index') }}">
                    <input type="hidden" name="job_id" value="{{ $selectedJob->id }}">

                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('Search') }}</label>
                            <input class="form-control" type="text" name="search" value="{{ $applicantFilters['search'] }}" placeholder="{{ __('Applicant name, email, or phone') }}">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">{{ __('Review status') }}</label>
                            <select class="form-control" name="status">
                                <option value="">{{ __('Any status') }}</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($applicantFilters['status'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">{{ __('Screening scope') }}</label>
                            <select class="form-control" name="screening_scope">
                                @foreach ($screeningScopeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($applicantFilters['screening_scope'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">{{ __('Rule logic') }}</label>
                            <select class="form-control" name="filter_logic">
                                @foreach ($filterLogicOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($applicantFilters['filter_logic'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="jobrango-application-builder__list mt-4" data-answer-filter-list></div>

                    <div class="jobrango-empty-state jobrango-empty-state--compact d-none mt-3" data-empty-answer-filter-list>
                        <h4>{{ __('No answer filters yet') }}</h4>
                        <p>{{ __('Add one or more answer rules if you want to narrow the applicant list by custom form responses.') }}</p>
                    </div>

                    <div class="jobrango-employer-form-actions">
                        <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Apply Filters') }}</button>
                        <a class="btn btn-border hover-up" href="{{ route('public.account.applicants.index', ['job_id' => $selectedJob->id]) }}">{{ __('Reset Filters') }}</a>
                    </div>
                </form>
            </div>

            @if ($applications && $applications->isNotEmpty())
                <div class="jobrango-applicant-grid">
                    @foreach ($applications as $application)
                        <article class="jobrango-applicant-card">
                            <div class="jobrango-applicant-card__header">
                                <div>
                                    <span>{{ $application->display_id }}</span>
                                    <h4>{{ $application->full_name }}</h4>
                                    <p>{{ $application->email }}</p>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <div>{!! $application->status->toHtml() !!}</div>
                                    <div>{!! \Botble\Base\Facades\BaseHelper::renderBadge($application->screening_status_label, $application->screening_status_color) !!}</div>
                                </div>
                            </div>
                            <div class="jobrango-applicant-card__meta">
                                @if ($application->phone)
                                    <span>{{ $application->phone }}</span>
                                @endif
                                <span>{{ $application->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="jobrango-employer-job-card__footer">
                                <a href="{{ route('public.account.applicants.edit', $application->id) }}">{{ __('Review Applicant') }}</a>
                                @if ($application->resume)
                                    <a href="{{ route('public.account.applicants.download-cv', $application->id) }}">{{ __('Download CV') }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-4">
                    {!! $applications->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
                </div>
            @else
                <div class="jobrango-empty-state">
                    <h4>{{ __('No applicants match this view yet') }}</h4>
                    <p>{{ __('When candidates apply to this role, or once your filters are broader, they will appear here with their answer-based screening badges.') }}</p>
                </div>
            @endif
        @else
            @if ($jobs->isNotEmpty())
                <div class="jobrango-applicant-job-grid">
                    @foreach ($jobs as $job)
                        <article class="jobrango-employer-job-card">
                            <div class="jobrango-employer-job-card__header">
                                <div>
                                    <span>{{ $job->display_id }}</span>
                                    <h3>{{ $job->name }}</h3>
                                    <p>{{ $job->company?->name ?: __('No company selected') }}</p>
                                </div>
                                <div class="jobrango-employer-job-card__badges">
                                    {!! $job->status->toHtml() !!}
                                </div>
                            </div>
                            <div class="jobrango-employer-job-card__meta">
                                <span>{{ __('Applicants: :count', ['count' => number_format($job->applicants_count)]) }}</span>
                                <span>{{ __('New applicants: :count', ['count' => number_format($job->new_applicants_count)]) }}</span>
                            </div>
                            <div class="jobrango-employer-job-card__footer">
                                <a href="{{ route('public.account.applicants.index', ['job_id' => $job->id]) }}">{{ __('View Applicants') }}</a>
                                <a href="{{ route('public.account.jobs.edit', $job->id) }}">{{ __('Edit') }}</a>
                                <a href="{{ $job->url }}" target="_blank">{{ __('View Job') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-4">
                    {!! $jobs->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
                </div>
            @else
                <div class="jobrango-empty-state">
                    <h4>{{ __('No jobs with applicants yet') }}</h4>
                    <p>{{ __('Post a job first, then return here to review applicants grouped by the role they applied for.') }}</p>
                    <a class="btn btn-default btn-shadow hover-up" href="{{ route('public.account.jobs.create') }}">{{ __('Post a Job') }}</a>
                </div>
            @endif
        @endif
    </div>

    <template id="jobrango-answer-filter-template">
        <article class="jobrango-question-card" data-answer-filter-item>
            <div class="jobrango-question-card__header">
                <div>
                    <span class="jobrango-question-card__badge">{{ __('Filter rule') }}</span>
                    <strong>{{ __('Applicant answer match') }}</strong>
                </div>
                <button class="btn btn-link p-0 text-danger" type="button" data-remove-answer-filter>{{ __('Remove') }}</button>
            </div>
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">{{ __('Question') }}</label>
                    <select class="form-control" data-answer-filter-field="question_key"></select>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('Condition') }}</label>
                    <select class="form-control" data-answer-filter-field="operator">
                        @foreach ($filterOperatorOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4" data-answer-filter-value-wrap>
                    <label class="form-label">{{ __('Value') }}</label>
                    <input class="form-control" type="text" data-answer-filter-value-text placeholder="{{ __('Example: yes, remote, weekend') }}">
                    <select class="form-control d-none" data-answer-filter-value-select></select>
                </div>
            </div>
        </article>
    </template>
@endsection

@if ($selectedJob)
    @push('footer')
        <script>
            window.addEventListener('load', function () {
                const builder = document.querySelector('[data-answer-filter-builder]');

                if (! builder) {
                    return;
                }

                const questionDefinitions = @json($answerFilterQuestionDefinitions);
                const initialFilters = @json(array_values($initialAnswerFilters));
                const template = document.getElementById('jobrango-answer-filter-template');
                const list = builder.querySelector('[data-answer-filter-list]');
                const emptyState = builder.querySelector('[data-empty-answer-filter-list]');
                const addButton = builder.querySelector('[data-add-answer-filter]');
                const operatorsWithoutValue = ['answered', 'not_answered'];
                const selectQuestionText = @json(__('Select a custom question'));
                const selectOptionText = @json(__('Select an option'));

                const filterName = (index, field) => `answer_filters[${index}][${field}]`;

                const populateQuestionSelect = (select, selectedValue = '') => {
                    select.innerHTML = `<option value="">${selectQuestionText}</option>` + questionDefinitions
                        .map((question) => `<option value="${question.key}">${question.label}</option>`)
                        .join('');

                    if (selectedValue) {
                        select.value = selectedValue;
                    }
                };

                const refreshIndices = () => {
                    Array.from(list.children).forEach((item, index) => {
                        item.querySelector('[data-answer-filter-field="question_key"]').setAttribute('name', filterName(index, 'question_key'));
                        item.querySelector('[data-answer-filter-field="operator"]').setAttribute('name', filterName(index, 'operator'));
                        item.querySelector('[data-answer-filter-value-text]').setAttribute('name', filterName(index, 'value'));
                    });
                };

                const toggleEmptyState = () => {
                    emptyState.classList.toggle('d-none', list.children.length !== 0);
                };

                const toggleValueInput = (item) => {
                    const questionKey = item.querySelector('[data-answer-filter-field="question_key"]').value;
                    const operator = item.querySelector('[data-answer-filter-field="operator"]').value;
                    const valueWrap = item.querySelector('[data-answer-filter-value-wrap]');
                    const textInput = item.querySelector('[data-answer-filter-value-text]');
                    const selectInput = item.querySelector('[data-answer-filter-value-select]');
                    const question = questionDefinitions.find((entry) => entry.key === questionKey);

                    if (operatorsWithoutValue.includes(operator)) {
                        valueWrap.classList.add('d-none');
                        textInput.disabled = true;
                        selectInput.disabled = true;

                        return;
                    }

                    valueWrap.classList.remove('d-none');

                    if (question && question.options && question.options.length) {
                        selectInput.innerHTML = `<option value="">${selectOptionText}</option>` + question.options
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

                const bindItem = (item) => {
                    item.querySelector('[data-remove-answer-filter]').addEventListener('click', function () {
                        item.remove();
                        refreshIndices();
                        toggleEmptyState();
                    });

                    item.querySelector('[data-answer-filter-field="question_key"]').addEventListener('change', function () {
                        toggleValueInput(item);
                    });

                    item.querySelector('[data-answer-filter-field="operator"]').addEventListener('change', function () {
                        toggleValueInput(item);
                    });

                    item.querySelector('[data-answer-filter-value-select]').addEventListener('change', function () {
                        item.querySelector('[data-answer-filter-value-text]').value = this.value;
                        this.setAttribute('data-selected-value', this.value);
                    });
                };

                const addFilter = (filter = {}) => {
                    const fragment = template.content.cloneNode(true);
                    const item = fragment.querySelector('[data-answer-filter-item]');

                    populateQuestionSelect(item.querySelector('[data-answer-filter-field="question_key"]'), filter.question_key || '');
                    item.querySelector('[data-answer-filter-field="operator"]').value = filter.operator || 'equals';
                    item.querySelector('[data-answer-filter-value-text]').value = filter.value || '';
                    item.querySelector('[data-answer-filter-value-select]').setAttribute('data-selected-value', filter.value || '');

                    bindItem(item);
                    list.appendChild(fragment);

                    const appendedItem = list.lastElementChild;
                    toggleValueInput(appendedItem);
                    refreshIndices();
                    toggleEmptyState();
                };

                initialFilters.forEach((filter) => addFilter(filter));

                if (! initialFilters.length) {
                    toggleEmptyState();
                }

                addButton.addEventListener('click', function () {
                    addFilter();
                });

                addButton.disabled = questionDefinitions.length === 0;
            });
        </script>
    @endpush
@endif
