<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 jobs-item job-grid">
    @php
        $companyName = $job->company_name ?: $job->company?->name ?: $job->name;
        $categoryLabels = $job->categories->pluck('name')->filter()->take(2);
        $isRemoteJob = \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower((string) $job->location), 'remote');
        $jobTypeLabel = $job->jobTypes->pluck('name')->filter()->first();
    @endphp
    <article class="card-grid-2 hover-up jobrango-job-card @if ($job->is_featured) featured-job-item @endif">
        <a class="jobrango-job-card__overlay" href="{{ $job->url }}" aria-label="{{ __('View :job', ['job' => $job->name]) }}"></a>
        <div class="card-grid-2-image-left">
            @if($job->is_featured)
                <span class="flash"></span>
            @endif
            <div class="image-box">
                @include(Theme::getThemeNamespace('partials.job-company-badge'), [
                    'job' => $job,
                    'companyName' => $companyName,
                    'companyUrl' => $job->company_url,
                    'logo' => $job->company_logo_thumb,
                ])
            </div>
            <div class="right-info">
                <a class="name-job" href="{{ $job->company_url ?: 'javascript:void(0);' }}">
                    {{ $companyName }} {!! $job->company && $job->company->badge ? $job->company->badge : '' !!}
                </a>
            </div>
        </div>
        <div class="card-block-info jobrango-job-card__body">
            <div class="jobrango-job-card__header">
                <h6 class="jobrango-job-card__title">
                    <a href="{{ $job->url }}" title="{{ $job->name }}">{{ $job->name }}</a>
                </h6>
                @if ($jobTypeLabel)
                    <span class="jobrango-job-card__type">{{ $jobTypeLabel }}</span>
                @endif
            </div>
            <div class="jobrango-job-card__meta">
                @if ($job->location)
                    <span>{{ $job->location }}</span>
                @endif
                <span>{{ $job->created_at->diffForHumans() }}</span>
            </div>
            <div class="job-card-taxonomy">
                @foreach($categoryLabels as $categoryLabel)
                    <span class="btn btn-grey-small mr-5 mb-2">{{ $categoryLabel }}</span>
                @endforeach
                @if($isRemoteJob)
                    <span class="btn btn-grey-small mr-5 mb-2">{{ __('Remote') }}</span>
                @endif
            </div>
            <div class="card-2-bottom jobrango-job-card__footer">
                <div class="salary-information">
                    {!! Theme::partial('salary', compact('job')) !!}
                </div>
                <div class="jobrango-job-card__actions">
                    <a href="{{ $job->url }}" class="btn btn-apply-now">{{ __('View Job') }}</a>
                    @unless ($job->isJobOpen())
                        <span class="jobrango-job-card__status">{{ __('Closed') }}</span>
                    @endunless
                </div>
            </div>
        </div>
    </article>
</div>
