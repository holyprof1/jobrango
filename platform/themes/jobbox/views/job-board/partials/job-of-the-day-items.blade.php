@php
    $jobCollection = method_exists($jobs, 'getCollection') ? $jobs->getCollection() : $jobs;
    $jobCollection->loadMissing(['categories', 'jobTypes', 'company', 'metadata']);
@endphp

@if (count($jobs) > 0)
    {!! Theme::partial('loading') !!}

    @foreach ($jobs as $job)
        @php
            $categoryLabels = $job->categories->pluck('name')->filter()->take(2);
            $isRemoteJob = \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower((string) $job->location), 'remote');
        @endphp
        @if ($style === 'style-1')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                <div @class(['card-grid-2 hover-up items', 'featured-job-item' => $job->is_featured])>
                    <div class="card-grid-2-image-left job-item">
                        @if ($job->is_featured)
                            <span class="flash"></span>
                        @endif
                        <div class="image-box">
                            @include(Theme::getThemeNamespace('partials.job-company-badge'), [
                                'job' => $job,
                                'companyName' => $job->company_name ?: $job->company?->name ?: $job->name,
                                'companyUrl' => $job->company_url,
                                'logo' => $job->company_logo_thumb,
                            ])
                        </div>
                        <div class="right-info">
                            @if (! $job->hide_company)
                                <a class="name-job" title="{{ $job->company_name }}" href="{{ $job->company_url }}">{{ $job->company_name }}</a>
                            @endif
                            <span class="location-small">{{ $job->location }}</span>
                        </div>
                    </div>
                    <div class="card-block-info">
                        <div class="h6 fw-bold text-truncate">
                            <a href="{{ $job->url }}" title="{{ $job->name }}">{{ $job->name }}</a>
                        </div>
                        <div class="mt-5">
                            @if($job->jobTypes->isNotEmpty())
                                <span class="card-briefcase">
                                    @foreach($job->jobTypes as $jobType)
                                        {{ $jobType->name }}@if (!$loop->last), @endif
                                    @endforeach
                                </span>
                            @endif
                            <span class="card-time">{{ $job->created_at->diffForHumans() }}</span></div>
                        <div class="mt-15 job-card-taxonomy">
                            @foreach($categoryLabels as $categoryLabel)
                                <span class="btn btn-grey-small mr-5 mb-2">{{ $categoryLabel }}</span>
                            @endforeach
                            @if($isRemoteJob)
                                <span class="btn btn-grey-small mr-5 mb-2">{{ __('Remote') }}</span>
                            @endif
                        </div>
                        <div class="card-2-bottom mt-15">
                            <div class="row">
                                <div class="col-12 salary-information">
                                    {!! Theme::partial('salary', compact('job')) !!}
                                </div>
                                <div class="col-12 mt-3">
                                    {!! Theme::partial('apply-button', compact('job')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($style === 'style-3')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card-grid-2 grid-bd-16 hover-up item-grid @if ($job->is_featured) featured-job-item @endif">
                    <div class="card-grid-2-image-left job-item job-item--compact">
                        @if ($job->jobTypes->isNotEmpty())
                            <span class="lbl-hot bg-green">
                                @if($job->jobTypes)
                                    @foreach($job->jobTypes as $jobType)
                                        <span>{{ $jobType->name }}</span>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </span>
                        @endif
                        <div class="image-box">
                            @include(Theme::getThemeNamespace('partials.job-company-badge'), [
                                'job' => $job,
                                'companyName' => $job->company_name ?: $job->company?->name ?: $job->name,
                                'companyUrl' => $job->company_url,
                                'logo' => $job->company_logo_thumb,
                            ])
                        </div>
                        <div class="right-info">
                            @if (! $job->hide_company)
                                <a class="name-job" title="{{ $job->company_name }}" href="{{ $job->company_url }}">{{ $job->company_name }}</a>
                            @endif
                            <span class="location-small">{{ $job->location }}</span>
                        </div>
                    </div>
                    <div class="card-block-info">
                        <div class="h6 font-bold">
                            <a href="{{ $job->url }}" class="name-job" title="{{ $job->name }}">{{ $job->name }}</a>
                        </div>
                        <div class="mt-5">
                            <span class="card-time">{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-15 job-card-taxonomy">
                            @foreach($categoryLabels as $categoryLabel)
                                <span class="btn btn-grey-small mr-5 mb-2">{{ $categoryLabel }}</span>
                            @endforeach
                            @if($isRemoteJob)
                                <span class="btn btn-grey-small mr-5 mb-2">{{ __('Remote') }}</span>
                            @endif
                        </div>
                        <div class="card-2-bottom mt-20">
                            <div class="row">
                                <div class="col-12">
                                    {!! Theme::partial('salary', compact('job')) !!}
                                </div>
                                <div class="col-12 mt-3">
                                    {!! Theme::partial('apply-button', compact('job')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
             @php($jobs->loadMissing('metadata'))
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card-grid-2 grid-bd-16 hover-up item-grid @if ($job->is_featured) featured-job-item @endif">
                    <div class="card-grid-2-image-left job-item job-item--compact">
                        @if ($job->jobTypes->isNotEmpty())
                            <span class="lbl-hot bg-green">
                                @foreach($job->jobTypes as $jobType)
                                    <span>{{ $jobType->name }}</span>@if (!$loop->last), @endif
                                @endforeach
                            </span>
                        @endif
                        <div class="image-box">
                            @include(Theme::getThemeNamespace('partials.job-company-badge'), [
                                'job' => $job,
                                'companyName' => $job->company_name ?: $job->company?->name ?: $job->name,
                                'companyUrl' => $job->company_url,
                                'logo' => $job->company_logo_thumb,
                            ])
                        </div>
                        <div class="right-info">
                            @if (! $job->hide_company)
                                <a class="name-job" title="{{ $job->company_name }}" href="{{ $job->company_url }}">{{ $job->company_name }}</a>
                            @endif
                            <span class="location-small">{{ $job->location }}</span>
                        </div>
                    </div>
                    <div class="card-block-info">
                        <div class="h6 fw-bold">
                            <a href="{{ $job->url }}" class="name-job" title="{{ $job->name }}">{{ $job->name }}</a>
                        </div>
                        <div class="mt-5">
                            <span class="card-time">{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-15 job-card-taxonomy">
                            @foreach($categoryLabels as $categoryLabel)
                                <span class="btn btn-grey-small mr-5 mb-2">{{ $categoryLabel }}</span>
                            @endforeach
                            @if($isRemoteJob)
                                <span class="btn btn-grey-small mr-5 mb-2">{{ __('Remote') }}</span>
                            @endif
                        </div>
                        <div class="card-2-bottom mt-20">
                            <div class="row">
                                <div class="col-12">
                                   {!! Theme::partial('salary', compact('job')) !!}
                                </div>
                                <div class="col-12 mt-3">
                                    {!! Theme::partial('apply-button', compact('job')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="col-12">
        <p class="text-center">{{ __('No data available') }}</p>
    </div>
@endif
