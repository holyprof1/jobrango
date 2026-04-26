@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

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
                    <strong>{{ $selectedJob->company->name ?: __('No company selected') }}</strong>
                    <p>{{ $selectedJob->location ?: __('Location not specified') }}</p>
                </article>
                <article>
                    <span>{{ __('Applicants') }}</span>
                    <strong>{{ number_format($selectedJob->applicants_count) }}</strong>
                    <p>{{ __('New applicants: :count', ['count' => number_format($selectedJob->new_applicants_count)]) }}</p>
                </article>
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
                                <div>{!! $application->status->toHtml() !!}</div>
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
                    <h4>{{ __('No applicants for this job yet') }}</h4>
                    <p>{{ __('When candidates apply to this role, they will appear here instead of getting mixed into unrelated jobs.') }}</p>
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
                                    <p>{{ $job->company->name ?: __('No company selected') }}</p>
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
@endsection
