<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 jobs-item job-grid">
    @php
        $companyName = trim((string) ($job->company_name ?: $job->company?->name ?: ''));
        $companyName = $companyName && $companyName !== $job->name ? $companyName : __('Hiring Team');
        $jobTypeLabel = $job->jobTypes
            ->pluck('name')
            ->filter()
            ->map(fn ($jobType) => $jobType === 'Intern' ? 'Internship' : $jobType)
            ->first(fn ($jobType) => in_array($jobType, ['Full Time', 'Part Time', 'Contract', 'Freelance', 'Internship'], true));
        $tagLabels = $job->categories->pluck('name')->filter()->take(2)->values();
        $isRemoteJob = \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower((string) $job->location), 'remote');

        if ($isRemoteJob && $tagLabels->count() < 2) {
            $tagLabels->push(__('Remote'));
        }
    @endphp
    <article class="card-grid-2 hover-up jobrango-job-card @if ($job->is_featured) featured-job-item @endif">
        <a class="jobrango-job-card__overlay" href="{{ $job->url }}" aria-label="{{ __('View :job', ['job' => $job->name]) }}"></a>
        @if($job->is_featured)
            <span class="flash"></span>
        @endif
        <div class="jobrango-job-card__content">
            <div class="jobrango-job-card__company-row">
                <div class="jobrango-job-card__company">
                @include(Theme::getThemeNamespace('partials.job-company-badge'), [
                    'job' => $job,
                    'companyName' => $companyName,
                    'companyUrl' => $job->company_url,
                    'logo' => $job->company_logo_thumb,
                ])
                    @if ($job->company_url)
                        <a class="jobrango-job-card__company-name" href="{{ $job->company_url }}">
                            {{ $companyName }}
                        </a>
                    @else
                        <span class="jobrango-job-card__company-name">{{ $companyName }}</span>
                    @endif
                </div>
                @if ($jobTypeLabel)
                    <span class="jobrango-job-card__type">{{ $jobTypeLabel }}</span>
                @endif
            </div>
            <div class="jobrango-job-card__body">
                <h6 class="jobrango-job-card__title">
                    <a href="{{ $job->url }}" title="{{ $job->name }}">{{ $job->name }}</a>
                </h6>
                <div class="jobrango-job-card__meta">
                    @if ($job->location)
                        <span>{{ $job->location }}</span>
                    @endif
                    <span>{{ $job->created_at->diffForHumans() }}</span>
                </div>
                @if ($tagLabels->isNotEmpty())
                    <div class="job-card-taxonomy">
                        @foreach($tagLabels as $tagLabel)
                            <span class="btn btn-grey-small mr-5 mb-2">{{ $tagLabel }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="card-2-bottom jobrango-job-card__footer">
                <div class="salary-information">
                    {!! Theme::partial('salary', compact('job')) !!}
                </div>
                <div class="jobrango-job-card__actions">
                    <a href="{{ $job->url }}" class="btn btn-apply-now">{{ __('View Job') }}</a>
                    @if ($job->canShowApplyJob())
                        <a href="{{ $job->url }}#job-apply" class="btn btn-border">{{ __('Apply Now') }}</a>
                    @endif
                    @unless ($job->isJobOpen())
                        <span class="jobrango-job-card__status">{{ __('Closed') }}</span>
                    @endunless
                </div>
            </div>
        </div>
    </article>
</div>
