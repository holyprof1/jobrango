@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@section('content')
    <div class="jobrango-employer-stats">
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Total Jobs') }}</span>
            <strong>{{ number_format($stats['total_jobs']) }}</strong>
            <p>{{ __('Everything created from this employer workspace.') }}</p>
        </article>
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Published') }}</span>
            <strong>{{ number_format($stats['published_jobs']) }}</strong>
            <p>{{ __('Roles visible to candidates right now.') }}</p>
        </article>
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Pending Approval') }}</span>
            <strong>{{ number_format($stats['pending_jobs']) }}</strong>
            <p>{{ __('Jobs waiting for moderation before going live.') }}</p>
        </article>
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Applicants') }}</span>
            <strong>{{ number_format($stats['total_applicants']) }}</strong>
            <p>{{ __('Applications received across all roles.') }}</p>
        </article>
    </div>

    <div class="jobrango-employer-toolbar">
        <form method="GET" action="{{ route('public.account.jobs.index') }}" class="jobrango-employer-toolbar__filters">
            <input class="form-control" type="text" name="q" value="{{ $search }}" placeholder="{{ __('Search job title or ID') }}">
            <select class="form-control" name="status">
                <option value="">{{ __('All statuses') }}</option>
                @foreach (\Botble\JobBoard\Enums\JobStatusEnum::labels() as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Filter') }}</button>
        </form>
        <a class="btn btn-border hover-up" href="{{ route('public.account.jobs.create') }}">{{ __('Post a Job') }}</a>
    </div>

    @if ($jobs->isNotEmpty())
        <div class="jobrango-employer-job-grid">
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
                            {!! $job->moderation_status->toHtml() !!}
                        </div>
                    </div>

                    <div class="jobrango-employer-job-card__meta">
                        <span>{{ $job->display_location ?: __('Location not specified') }}</span>
                        <span>{{ __('Applicants: :count', ['count' => number_format($job->applicants_count)]) }}</span>
                        <span>{{ __('New: :count', ['count' => number_format($job->new_applicants_count)]) }}</span>
                    </div>

                    <div class="jobrango-employer-job-card__footer">
                        <a href="{{ $job->url }}" target="_blank">{{ __('View Job') }}</a>
                        <a href="{{ route('public.account.jobs.edit', $job->id) }}">{{ __('Edit') }}</a>
                        <a href="{{ route('public.account.applicants.index', ['job_id' => $job->id]) }}">{{ __('Applicants') }}</a>
                        <a href="{{ route('public.account.jobs.application-form.edit', $job->id) }}">{{ __('Application Form') }}</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-4">
            {!! $jobs->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
        </div>
    @else
        <div class="jobrango-empty-state">
            <h4>{{ __('No jobs created yet') }}</h4>
            <p>{{ __('Start with one clear job post, then configure the application experience after saving it.') }}</p>
            <a class="btn btn-default btn-shadow hover-up" href="{{ route('public.account.jobs.create') }}">{{ __('Post Your First Job') }}</a>
        </div>
    @endif
@endsection
