@php
    $defaultCompanyLogo = theme_option('default_company_logo', true);
@endphp

@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div class="jobrango-panel">
        <div class="jobrango-panel__header">
            <div>
                <h3>{{ __('Saved Jobs') }}</h3>
                <p>{{ __('Manage the roles you bookmarked and reopen them when you are ready to apply.') }}</p>
            </div>
            <form action="{{ URL::current() }}" method="GET" class="jobrango-inline-filter jobrango-inline-filter--wide">
                <select class="form-control" name="order_by" aria-label="{{ __('Sort saved jobs') }}">
                    <option value="">{{ __('Default') }}</option>
                    <option value="newest" @selected(request('order_by') === 'newest')>{{ __('Newest') }}</option>
                    <option value="oldest" @selected(request('order_by') === 'oldest')>{{ __('Oldest') }}</option>
                    <option value="random" @selected(request('order_by') === 'random')>{{ __('Random') }}</option>
                </select>
                <select class="form-control" name="category" aria-label="{{ __('Filter saved jobs by category') }}">
                    <option value="">{{ __('All categories') }}</option>
                    @foreach (app(Botble\JobBoard\Repositories\Interfaces\CategoryInterface::class)->getCategories() as $category)
                        <option value="{{ $category->id }}" @selected((int) request()->input('category') === (int) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Apply') }}</button>
            </form>
        </div>

        @if ($jobs->isNotEmpty())
            <div class="jobrango-job-list">
                @foreach ($jobs as $job)
                    @php
                        $companyLogo = $job->company->logo;
                    @endphp
                    <article class="jobrango-job-list__item">
                        <div class="jobrango-job-list__logo">
                            @if (! $job->hide_company && ($companyLogo || $defaultCompanyLogo))
                                {{ RvMedia::image($companyLogo ?: $defaultCompanyLogo, $job->company->name, attributes: ['class' => 'img-fluid rounded-3']) }}
                            @elseif (theme_option('logo'))
                                {{ Theme::getLogoImage(['class' => 'img-fluid rounded-3'], 'logo') }}
                            @endif
                        </div>
                        <div class="jobrango-job-list__copy">
                            <h4><a href="{{ $job->url }}">{{ $job->name }}</a></h4>
                            <p>
                                @if (! $job->hide_company)
                                    {{ $job->company->name }} |
                                @endif
                                {{ $job->full_address ?: __('Location not specified') }}
                            </p>
                            <span>{{ $job->salary_text }}</span>
                        </div>
                        <div class="jobrango-job-list__meta">
                            <a href="{{ $job->url }}">{{ __('View Job') }}</a>
                            <form id="bookmark-form-{{ $job->id }}" action="{{ route('public.account.jobs.saved.action') }}" method="POST">
                                @csrf
                                <input type="hidden" name="job_id" value="{{ $job->id }}">
                                <button class="btn btn-link p-0" onclick="return confirm('{{ __('Remove this saved job?') }}');" type="submit">
                                    {{ __('Remove') }}
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="jobrango-empty-state">
                <h4>{{ __('No saved jobs yet') }}</h4>
                <p>{{ __('Use the save action on any interesting role and it will appear here for quick access.') }}</p>
                <a class="btn btn-default btn-shadow hover-up" href="{{ url('/jobs') }}">{{ __('Browse Jobs') }}</a>
            </div>
        @endif
    </div>

    <div class="mt-4">
        {!! $jobs->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
    </div>
@endsection
