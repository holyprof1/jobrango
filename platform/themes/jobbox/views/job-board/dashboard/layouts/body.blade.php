<header class="header--mobile jobrango-dashboard-mobile-header">
    <div class="header__left">
        <button class="navbar-toggler">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="header__center">
        <span class="jobrango-dashboard-mobile-header__title">{{ PageTitle::getTitle(false) }}</span>
    </div>
    <div class="header__right">
        <a href="{{ route('public.account.jobs.create') }}" class="jobrango-dashboard-mobile-header__link">
            <x-core::icon name="ti ti-plus" />
        </a>
    </div>
</header>

<aside class="ps-drawer--mobile jobrango-dashboard-drawer">
    <div class="ps-drawer__header py-3">
        <h4 class="fs-3 mb-0">{{ __('Employer Menu') }}</h4>
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
                <h1>{{ PageTitle::getTitle(false) }}</h1>
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
