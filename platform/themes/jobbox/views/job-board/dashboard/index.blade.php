@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@section('content')
    <div class="jobrango-employer-stats">
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Jobs') }}</span>
            <strong>{{ number_format($totalJobs) }}</strong>
            <p>{{ __('Live and draft roles across your employer account.') }}</p>
        </article>
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Companies') }}</span>
            <strong>{{ number_format($totalCompanies) }}</strong>
            <p>{{ __('Company profiles attached to this workspace.') }}</p>
        </article>
        <article class="jobrango-employer-stat-card">
            <span>{{ __('Applicants') }}</span>
            <strong>{{ number_format($totalApplicants) }}</strong>
            <p>{{ __('Applications received across all tracked jobs.') }}</p>
        </article>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-6">
            <div class="jobrango-employer-panel-card">
                <div class="jobrango-employer-panel-card__header">
                    <div>
                        <h3>{{ __('Newest Applicants') }}</h3>
                        <p>{{ __('A quick look at recent candidate activity.') }}</p>
                    </div>
                    <a href="{{ route('public.account.applicants.index') }}">{{ __('All applicants') }}</a>
                </div>

                @if ($newApplicants->isNotEmpty())
                    <div class="jobrango-employer-list">
                        @foreach ($newApplicants as $applicant)
                            <article class="jobrango-employer-list__item">
                                <div>
                                    @php
                                        $jobName = $applicant->job?->name ?: __('Unavailable job');
                                    @endphp
                                    <strong>{{ $applicant->full_name }}</strong>
                                    <p>{{ $jobName }}</p>
                                    <span>{{ $applicant->display_id }} &bull; {{ $applicant->created_at->diffForHumans() }}</span>
                                </div>
                                <a href="{{ route('public.account.applicants.edit', $applicant->id) }}">{{ __('Review') }}</a>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="jobrango-empty-state jobrango-empty-state--compact">
                        <h4>{{ __('No applicants yet') }}</h4>
                        <p>{{ __('New applications will show up here once candidates start responding to your jobs.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="jobrango-employer-panel-card">
                <div class="jobrango-employer-panel-card__header">
                    <div>
                        <h3>{{ __('Recent Activity') }}</h3>
                        <p>{{ __('Posting, updates, and renewals tied to your account.') }}</p>
                    </div>
                </div>

                @if ($activities->isNotEmpty())
                    <div class="jobrango-employer-list">
                        @foreach ($activities as $activity)
                            <article class="jobrango-employer-list__item">
                                <div>
                                    <strong>{!! BaseHelper::clean($activity->getDescription(false)) !!}</strong>
                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="jobrango-empty-state jobrango-empty-state--compact">
                        <h4>{{ __('No activity yet') }}</h4>
                        <p>{{ __('Once you post or update jobs, the latest actions will be visible here.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="jobrango-employer-panel-card mt-4">
        <div class="jobrango-employer-panel-card__header">
            <div>
                <h3>{{ __('Jobs Reaching Expiry Soon') }}</h3>
                <p>{{ __('Renew or review these roles before they close out.') }}</p>
            </div>
            <a href="{{ route('public.account.jobs.index') }}">{{ __('Manage jobs') }}</a>
        </div>

        @if ($expiredJobs->isNotEmpty())
            <div class="table-responsive">
                <table class="table jobrango-employer-table">
                    <thead>
                        <tr>
                            <th>{{ __('Job') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Expiry') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Applicants') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expiredJobs as $job)
                            <tr>
                                <td>
                                    <strong>{{ $job->name }}</strong>
                                    <span>{{ $job->display_id }}</span>
                                </td>
                                <td>{{ $job->company?->name ?: __('No company selected') }}</td>
                                <td>{{ BaseHelper::formatDate($job->expire_date) }}</td>
                                <td>{!! $job->status->toHtml() !!}</td>
                                <td>{{ number_format($job->applicants_count) }}</td>
                                <td>
                                    <div class="jobrango-inline-actions">
                                        <a href="{{ route('public.account.jobs.edit', $job->id) }}">{{ __('Edit') }}</a>
                                        <a href="{{ route('public.account.applicants.index', ['job_id' => $job->id]) }}">{{ __('Applicants') }}</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="jobrango-empty-state jobrango-empty-state--compact">
                <h4>{{ __('Nothing expiring soon') }}</h4>
                <p>{{ __('Your active roles are not close to expiry right now.') }}</p>
            </div>
        @endif
    </div>
@endsection
