@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div class="jobrango-panel">
        <div class="jobrango-panel__header">
            <div>
                <h3>{{ __('Applied Jobs') }}</h3>
                <p>{{ __('Review the roles you have already submitted and reopen any details you need.') }}</p>
            </div>
            @if ($applications->isNotEmpty())
                <form action="{{ URL::current() }}" method="GET" class="jobrango-inline-filter">
                    <select class="form-control" name="order_by" aria-label="{{ __('Sort applied jobs') }}">
                        <option value="">{{ __('Default') }}</option>
                        <option value="newest" @selected(request('order_by') === 'newest')>{{ __('Newest') }}</option>
                        <option value="oldest" @selected(request('order_by') === 'oldest')>{{ __('Oldest') }}</option>
                        <option value="random" @selected(request('order_by') === 'random')>{{ __('Random') }}</option>
                    </select>
                    <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Apply') }}</button>
                </form>
            @endif
        </div>

        @if ($applications->isNotEmpty())
            <div class="jobrango-job-list">
                @foreach ($applications as $application)
                    <article class="jobrango-job-list__item">
                        <div class="jobrango-job-list__logo">
                            @if ($application->job->hide_company)
                                @if (theme_option('logo'))
                                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}">
                                @endif
                            @else
                                <img src="{{ $application->job->company->logo_thumb }}" alt="{{ $application->job->company->name }}">
                            @endif
                        </div>
                        <div class="jobrango-job-list__copy">
                            <h4>
                                @if ($application->job_url)
                                    <a href="{{ $application->job_url }}">{{ $application->job->name }}</a>
                                @else
                                    <span class="text-decoration-line-through">{{ $application->job->name }}</span>
                                @endif
                            </h4>
                            <p>
                                @if (! $application->job->hide_company)
                                    {{ $application->job->company->name }} |
                                @endif
                                {{ $application->job->full_address ?: __('Location not specified') }}
                            </p>
                            <span>{{ $application->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="jobrango-job-list__meta">
                            @if ($application->job_url)
                                <a href="{{ $application->job_url }}">{{ __('View Job') }}</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="jobrango-empty-state">
                <h4>{{ __('No applied jobs yet') }}</h4>
                <p>{{ __('Applications will appear here once you start applying for roles.') }}</p>
                <a class="btn btn-default btn-shadow hover-up" href="{{ url('/jobs') }}">{{ __('Browse Jobs') }}</a>
            </div>
        @endif
    </div>

    <div class="mt-4">
        {!! $applications->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
    </div>
@endsection
