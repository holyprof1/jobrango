@php
    $account = auth('account')->user();
    $companyName = $account->companies()->value('name') ?: $account->name;
    $pageTitle = trim((string) PageTitle::getTitle(false));

    if (! $pageTitle || $pageTitle === theme_option('site_title')) {
        if (request()->routeIs('public.account.employer.settings.*')) {
            $pageTitle = trans('plugins/job-board::messages.account_settings');
        }
    }
@endphp

<header class="header--mobile jobrango-dashboard-mobile-header">
    <div class="header__left">
        <button class="navbar-toggler jobrango-dashboard-mobile-header__toggle">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="header__center">
        <a href="{{ route('public.index') }}" class="jobrango-dashboard-mobile-header__brand">
            <img
                alt="{{ theme_option('site_title') }}"
                src="{{ setting('theme-jobbox-logo') ? RvMedia::getImageUrl(setting('theme-jobbox-logo')) : url(config('core.base.general.logo')) }}"
            >
            <span class="jobrango-dashboard-mobile-header__brand-copy">
                <small>{{ __('Employer workspace') }}</small>
                <strong>{{ Str::limit($companyName, 24) }}</strong>
            </span>
        </a>
    </div>
    <div class="header__right">
        <a href="{{ route('public.account.jobs.create') }}" class="jobrango-dashboard-mobile-header__link" title="{{ __('Post a Job') }}">
            <x-core::icon name="ti ti-plus" />
        </a>
    </div>
</header>

<aside class="ps-drawer--mobile jobrango-dashboard-drawer">
    <div class="ps-drawer__header py-3">
        <div class="jobrango-dashboard-drawer__brand">
            <img
                alt="{{ theme_option('site_title') }}"
                src="{{ setting('theme-jobbox-logo') ? RvMedia::getImageUrl(setting('theme-jobbox-logo')) : url(config('core.base.general.logo')) }}"
            >
            <span>{{ __('Employer Menu') }}</span>
        </div>
        <button class="ps-drawer__close">
            <x-core::icon name="ti ti-x" />
        </button>
    </div>
    <div class="ps-drawer__content">
        @include(JobBoardHelper::viewPath('dashboard.layouts.menu-top'))
        <div class="my-4 border-bottom"></div>
        @include(JobBoardHelper::viewPath('dashboard.layouts.menu'))
    </div>
</aside>

<div class="ps-site-overlay"></div>

<main class="ps-main jobrango-dashboard-shell">
    <div class="ps-main__sidebar">
        <div class="ps-sidebar">
            @include(JobBoardHelper::viewPath('dashboard.layouts.menu-top'))

            <div class="ps-sidebar__content">
                <div class="ps-sidebar__center">
                    @include(JobBoardHelper::viewPath('dashboard.layouts.menu'))
                </div>
            </div>
        </div>
    </div>
    <div class="ps-main__wrapper">
        <header class="jobrango-dashboard-shell__header">
            <div>
                <span class="jobrango-dashboard-shell__eyebrow">{{ __('Employer workspace') }}</span>
                <h1>{{ $pageTitle }}</h1>
                <p>{{ __('Manage jobs, review applicants by role, and keep hiring activity organized.') }}</p>
            </div>
            <div class="jobrango-dashboard-shell__header-actions">
                <a href="{{ route('public.account.jobs.index') }}" class="jobrango-dashboard-shell__link">{{ __('Manage Jobs') }}</a>
                <a href="{{ route('public.account.jobs.create') }}" class="jobrango-dashboard-shell__cta">
                    <x-core::icon name="ti ti-briefcase" />
                    {{ __('Post a Job') }}
                </a>
                <a href="{{ route('public.index') }}" target="_blank" class="jobrango-dashboard-shell__link">{{ __('Open Site') }}</a>
            </div>
        </header>

        <div id="app">
            @yield('content')
        </div>
    </div>
</main>
