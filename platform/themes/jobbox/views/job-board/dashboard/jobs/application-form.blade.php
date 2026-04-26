@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@section('content')
    <div class="jobrango-employer-panel-card">
        <div class="jobrango-employer-panel-card__header">
            <div>
                <h3>{{ __('Customize Application Form') }}</h3>
                <p>{{ __('Job saved successfully for :job. Choose the candidate application experience before you move on.', ['job' => $job->name]) }}</p>
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
                    <input type="radio" name="application_mode" value="basic" @checked(($job->application_mode ?: 'basic') === 'basic')>
                    <span class="jobrango-choice-card__content">
                        <strong>{{ __('Basic application') }}</strong>
                        <small>{{ __('Use the default JobRango application form with the standard candidate fields.') }}</small>
                    </span>
                </label>
                <label class="jobrango-choice-card">
                    <input type="radio" name="application_mode" value="custom" @checked($job->application_mode === 'custom')>
                    <span class="jobrango-choice-card__content">
                        <strong>{{ __('Custom application') }}</strong>
                        <small>{{ __('Reserve this job for a richer form experience with custom screening questions.') }}</small>
                    </span>
                </label>
            </div>

            <div class="jobrango-application-settings mt-4">
                <h4>{{ __('Screening & Completion Rules') }}</h4>
                <label class="cb-container">
                    <input type="checkbox" name="auto_highlight" value="1" @checked($applicationSettings['auto_highlight'])>
                    <span class="text-small">{{ __('Auto-highlight applicants based on configured answers when the full custom builder is available.') }}</span>
                    <span class="checkmark"></span>
                </label>
                <label class="cb-container">
                    <input type="checkbox" name="mark_incomplete_required" value="1" @checked($applicationSettings['mark_incomplete_required'])>
                    <span class="text-small">{{ __('Required questions should mark incomplete applications.') }}</span>
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="jobrango-application-placeholder mt-4">
                <div>
                    <span class="jobrango-overview__eyebrow">{{ __('Current implementation') }}</span>
                    <h4>{{ __('Custom builder foundation is ready, but question authoring is not live yet.') }}</h4>
                    <p>{{ __('This release stores the job-level application mode and screening settings, and reserves structured schema fields for a future full builder. Candidates still use the default JobRango form today.') }}</p>
                </div>
                <div class="jobrango-application-placeholder__types">
                    @foreach ($supportedQuestionTypes as $supportedQuestionType)
                        <span>{{ $supportedQuestionType }}</span>
                    @endforeach
                </div>
            </div>

            <div class="jobrango-employer-form-actions">
                <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Save Application Settings') }}</button>
                <a class="btn btn-border hover-up" href="{{ route('public.account.jobs.index') }}">{{ __('Back to Jobs') }}</a>
                <a class="btn btn-border hover-up" href="{{ route('public.account.applicants.index', ['job_id' => $job->id]) }}">{{ __('View Applicants') }}</a>
            </div>
        </form>
    </div>
@endsection
