<div class="col-12 jobs-listing">
    @php
        $jobTypeLabel = $job->jobTypes
            ->pluck('name')
            ->filter()
            ->map(fn ($jobType) => $jobType === 'Intern' ? 'Internship' : $jobType)
            ->first(fn ($jobType) => in_array($jobType, ['Full Time', 'Part Time', 'Contract', 'Freelance', 'Internship'], true));
        $tagLabels = $job->categories->pluck('name')->filter()->take(2)->values();
        $isRemoteJob = (bool) $job->is_remote;

        if ($isRemoteJob && $tagLabels->count() < 2) {
            $tagLabels->push(__('Remote'));
        }
    @endphp
    <div class="card-grid-2 hover-up jobrango-job-card jobrango-job-card--company">
        @if($job->is_featured)
            <span class="flash"></span>
        @endif
        <div class="jobrango-job-card__content">
            <div class="jobrango-job-card__company-row">
                <div class="jobrango-job-card__company">
                    @include(Theme::getThemeNamespace('partials.job-company-badge'), [
                        'job' => $job,
                        'companyName' => $company->name,
                        'companyUrl' => $company->url,
                        'logo' => $company->logo_thumb,
                    ])
                    <a class="jobrango-job-card__company-name" href="{{ $company->url }}">{{ $company->name }}</a>
                </div>
                @if ($jobTypeLabel)
                    <span class="jobrango-job-card__type">{{ $jobTypeLabel }}</span>
                @endif
            </div>
            <div class="jobrango-job-card__body">
                <h4 class="jobrango-job-card__title">
                    <a href="{{ $job->url }}">{{ $job->name }}</a>
                </h4>
                <div class="jobrango-job-card__meta">
                    @if ($job->display_location)
                        <span>{{ $job->display_location }}</span>
                    @endif
                    <span>{{ $job->created_at->diffForHumans() }}</span>
                </div>
                @if($tagLabels->isNotEmpty())
                    <div class="job-card-taxonomy">
                        @foreach($tagLabels as $tagLabel)
                            <span class="btn btn-grey-small mr-5 mb-2">{{ $tagLabel }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
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
            </div>
        </div>
    </div>
</div>
