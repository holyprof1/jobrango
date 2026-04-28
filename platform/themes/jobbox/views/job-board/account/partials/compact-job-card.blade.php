@php
    $companyName = trim((string) ($job->company?->name ?: $job->company_name ?: ''));
    $companyName = $companyName ?: __('Hiring Team');
    $location = $job->full_address ?: $job->location ?: __('Location not specified');
    $jobTypeLabel = $job->relationLoaded('jobTypes')
        ? $job->jobTypes
            ->pluck('name')
            ->filter()
            ->map(fn ($jobType) => $jobType === 'Intern' ? 'Internship' : $jobType)
            ->first(fn ($jobType) => in_array($jobType, ['Full Time', 'Part Time', 'Contract', 'Freelance', 'Internship'], true))
        : null;
    $tagLabels = $job->relationLoaded('categories')
        ? $job->categories->pluck('name')->filter()->take(2)->values()
        : collect();
    $primaryActionLabel = $primaryActionLabel ?? __('View Job');
    $primaryActionUrl = $primaryActionUrl ?? $job->url;
    $secondaryActionLabel = $secondaryActionLabel ?? ($job->canShowApplyJob() ? __('Apply Now') : null);
    $secondaryActionUrl = $secondaryActionUrl ?? ($job->canShowApplyJob() ? $job->url . '#job-apply' : null);
    $showRemoveAction = $showRemoveAction ?? false;
@endphp

<article class="jobrango-job-card jobrango-job-card--account">
    <div class="jobrango-job-card__content">
        <div class="jobrango-job-card__company-row">
            @if ($job->company_url)
                <a class="jobrango-job-card__company-name" href="{{ $job->company_url }}">{{ $companyName }}</a>
            @else
                <span class="jobrango-job-card__company-name">{{ $companyName }}</span>
            @endif
            @if ($jobTypeLabel)
                <span class="jobrango-job-card__type">{{ $jobTypeLabel }}</span>
            @endif
        </div>

        <div class="jobrango-job-card__body">
            <h4 class="jobrango-job-card__title">
                <a href="{{ $job->url }}">{{ $job->name }}</a>
            </h4>

            <div class="jobrango-job-card__meta">
                <span>{{ $location }}</span>
                <span>{{ $job->created_at->diffForHumans() }}</span>
            </div>

            @if ($tagLabels->isNotEmpty())
                <div class="job-card-taxonomy">
                    @foreach ($tagLabels as $tagLabel)
                        <span class="btn btn-grey-small">{{ $tagLabel }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="jobrango-job-card__footer">
        <div class="salary-information">
            {{ $job->salary_text }}
        </div>

        <div class="jobrango-job-card__actions">
            <a href="{{ $primaryActionUrl }}" class="btn btn-apply-now">{{ $primaryActionLabel }}</a>
            @if ($secondaryActionLabel && $secondaryActionUrl)
                <a href="{{ $secondaryActionUrl }}" class="btn btn-border">{{ $secondaryActionLabel }}</a>
            @endif
        </div>
    </div>

    @if ($showRemoveAction)
        <form class="jobrango-job-card__utility" action="{{ route('public.account.jobs.saved.action') }}" method="POST">
            @csrf
            <input type="hidden" name="job_id" value="{{ $job->id }}">
            <button type="submit" onclick="return confirm('{{ __('Remove this saved job?') }}');">{{ __('Remove') }}</button>
        </form>
    @endif
</article>
