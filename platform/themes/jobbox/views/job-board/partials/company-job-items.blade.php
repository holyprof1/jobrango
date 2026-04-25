@php
    $jobCollection = method_exists($jobs, 'getCollection') ? $jobs->getCollection() : $jobs;
    $jobCollection->loadMissing(['categories', 'jobTypes', 'company']);
@endphp

<h5 class="mb-30">{{ __('Latest Jobs') }}</h5>
<div class="display-list">
    @foreach($jobs as $job)
        @include(Theme::getThemeNamespace('views.job-board.partials.company-job-item'), ['job' => $job])
    @endforeach
</div>
{!! $jobs->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
