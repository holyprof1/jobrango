@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div class="jobrango-overview">
        <div class="jobrango-overview__intro">
            <div>
                <span class="jobrango-overview__eyebrow">{{ __('Overview') }}</span>
                <h2>{{ __('Your job search at a glance') }}</h2>
                <p>{{ __('Track applications, keep saved roles close, and spot the next profile updates that will strengthen your account.') }}</p>
            </div>
        </div>

        <div class="jobrango-overview__stats">
            <article class="jobrango-stat-card">
                <span>{{ __('Applied Jobs') }}</span>
                <strong>{{ number_format($stats['applied_jobs']) }}</strong>
                <p>{{ __('Applications already submitted from this account.') }}</p>
            </article>
            <article class="jobrango-stat-card">
                <span>{{ __('Saved Jobs') }}</span>
                <strong>{{ number_format($stats['saved_jobs']) }}</strong>
                <p>{{ __('Roles you bookmarked for a closer look.') }}</p>
            </article>
            <article class="jobrango-stat-card">
                <span>{{ __('Profile Completion') }}</span>
                <strong>{{ $stats['profile_completion'] }}%</strong>
                <p>{{ __('A stronger profile helps employers trust what they see.') }}</p>
            </article>
            <article class="jobrango-stat-card">
                <span>{{ __('Recent Applications') }}</span>
                <strong>{{ number_format($stats['recent_applications']) }}</strong>
                <p>{{ __('Applications shown in the latest activity list below.') }}</p>
            </article>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="jobrango-panel">
                    <div class="jobrango-panel__header">
                        <div>
                            <h3>{{ __('Recent Applications') }}</h3>
                            <p>{{ __('Your latest submitted roles, newest first.') }}</p>
                        </div>
                        <a href="{{ route('public.account.jobs.applied-jobs') }}">{{ __('View all') }}</a>
                    </div>

                    @if ($recentApplications->isNotEmpty())
                        <div class="jobrango-activity-list">
                            @foreach ($recentApplications as $application)
                                <article class="jobrango-activity-card">
                                    <div class="jobrango-activity-card__logo">
                                        <img src="{{ $application->job->company->logo_thumb }}" alt="{{ $application->job->company->name }}">
                                    </div>
                                    <div class="jobrango-activity-card__copy">
                                        <h4>
                                            @if ($application->job->url)
                                                <a href="{{ $application->job->url }}">{{ $application->job->name }}</a>
                                            @else
                                                {{ $application->job->name }}
                                            @endif
                                        </h4>
                                        <p>{{ $application->job->company->name }} · {{ $application->job->full_address ?: __('Location not specified') }}</p>
                                        <span>{{ $application->created_at->diffForHumans() }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="jobrango-empty-state">
                            <h4>{{ __('No applications yet') }}</h4>
                            <p>{{ __('Start browsing current roles and apply to a few strong matches to build momentum here.') }}</p>
                            <a class="btn btn-default btn-shadow hover-up" href="{{ route('public.jobs') }}">{{ __('Browse Jobs') }}</a>
                        </div>
                    @endif
                </div>

                <div class="jobrango-panel mt-4">
                    <div class="jobrango-panel__header">
                        <div>
                            <h3>{{ __('Saved Jobs') }}</h3>
                            <p>{{ __('Roles you kept for later review.') }}</p>
                        </div>
                        <a href="{{ route('public.account.jobs.saved') }}">{{ __('Open saved jobs') }}</a>
                    </div>

                    @if ($savedJobs->isNotEmpty())
                        <div class="jobrango-job-list">
                            @foreach ($savedJobs as $job)
                                <article class="jobrango-job-list__item">
                                    <div class="jobrango-job-list__logo">
                                        <img src="{{ $job->company->logo_thumb }}" alt="{{ $job->company->name }}">
                                    </div>
                                    <div class="jobrango-job-list__copy">
                                        <h4><a href="{{ $job->url }}">{{ $job->name }}</a></h4>
                                        <p>{{ $job->company->name }} · {{ $job->full_address ?: __('Location not specified') }}</p>
                                    </div>
                                    <div class="jobrango-job-list__meta">
                                        <span>{{ $job->salary_text }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @elseif ($recommendedJobs->isNotEmpty())
                        <div class="jobrango-empty-state">
                            <h4>{{ __('No saved jobs yet') }}</h4>
                            <p>{{ __('These currently open roles are a good place to start.') }}</p>
                            <div class="jobrango-job-list mt-3">
                                @foreach ($recommendedJobs as $job)
                                    <article class="jobrango-job-list__item">
                                        <div class="jobrango-job-list__logo">
                                            <img src="{{ $job->company->logo_thumb }}" alt="{{ $job->company->name }}">
                                        </div>
                                        <div class="jobrango-job-list__copy">
                                            <h4><a href="{{ $job->url }}">{{ $job->name }}</a></h4>
                                            <p>{{ $job->company->name }} · {{ $job->full_address ?: __('Location not specified') }}</p>
                                        </div>
                                        <div class="jobrango-job-list__meta">
                                            <a href="{{ $job->url }}">{{ __('View Job') }}</a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="jobrango-panel">
                    <div class="jobrango-panel__header">
                        <div>
                            <h3>{{ __('Profile Tips') }}</h3>
                            <p>{{ __('Small updates that make the account stronger.') }}</p>
                        </div>
                    </div>

                    @if ($profileTips->isNotEmpty())
                        <ul class="jobrango-tip-list">
                            @foreach ($profileTips as $tip)
                                <li>{{ $tip }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="jobrango-empty-state jobrango-empty-state--compact">
                            <h4>{{ __('Profile looking strong') }}</h4>
                            <p>{{ __('Your key profile sections are already filled in. Keep applications moving and refresh details when needed.') }}</p>
                        </div>
                    @endif
                </div>

                <div class="jobrango-panel mt-4">
                    <div class="jobrango-panel__header">
                        <div>
                            <h3>{{ __('Quick Actions') }}</h3>
                            <p>{{ __('Jump straight into the next useful step.') }}</p>
                        </div>
                    </div>

                    <div class="jobrango-action-list">
                        <a href="{{ route('public.account.settings') }}">{{ __('Edit profile details') }}</a>
                        <a href="{{ route('public.account.jobs.saved') }}">{{ __('Review saved jobs') }}</a>
                        <a href="{{ route('public.account.jobs.applied-jobs') }}">{{ __('Check applications') }}</a>
                        <a href="{{ route('public.account.security') }}">{{ __('Update password') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
