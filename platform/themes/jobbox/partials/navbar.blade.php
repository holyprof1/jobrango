@if (theme_option('preloader_enabled', 'yes') == 'yes')
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
@endif

@if (is_plugin_active('job-board'))
    @include(Theme::getThemeNamespace('partials.apply-modal'))
@endif
@php
    $account = null;

    try {
        if (array_key_exists('account', config('auth.guards', []))) {
            $account = auth('account')->user();
        }
    } catch (Throwable) {
        $account = null;
    }

    $isEmployer = $account?->isEmployer() ?? false;
    $isJobSeeker = $account?->isJobSeeker() ?? false;
    $routeUrl = static fn (string $routeName, string $fallback): string => \Illuminate\Support\Facades\Route::has($routeName)
        ? route($routeName)
        : url($fallback);
    $dashboardRoute = $isEmployer
        ? $routeUrl('public.account.dashboard', '/account/dashboard')
        : $routeUrl('public.account.overview', '/account/overview');
    $logoutRoute = $routeUrl('public.account.logout', '/account/logout');
    $accountName = $account ? Str::limit($account->name, 18) : null;
    $companyProfileName = null;

    if ($isEmployer) {
        $account->loadMissing('companies');
        $companyProfileName = Str::limit($account->companies->first()?->name ?: $account->name, 18);
    }

    $primaryNavItems = [
        ['label' => __('Home'), 'url' => route('public.index')],
        ['label' => __('Jobs'), 'url' => url('/jobs')],
        ['label' => __('Companies'), 'url' => url('/companies')],
    ];

    $guestActionItems = [
        ['label' => __('For Employers'), 'url' => $routeUrl('public.account.register', '/register'), 'class' => 'jobrango-header-link'],
        ['label' => __('Post a Job'), 'url' => $routeUrl('public.account.register', '/register'), 'class' => 'jobrango-header-link'],
        ['label' => __('Sign In'), 'url' => $routeUrl('public.account.login', '/login'), 'class' => 'btn btn-default btn-shadow hover-up'],
    ];

    $authenticatedNavItems = $isEmployer
        ? [
            ['label' => __('Employer Dashboard'), 'short_label' => __('Dashboard'), 'url' => $routeUrl('public.account.dashboard', '/account/dashboard')],
            ['label' => __('Post a Job'), 'short_label' => __('Post a Job'), 'url' => $routeUrl('public.account.jobs.create', '/account/jobs/create')],
            ['label' => __('Applicants'), 'short_label' => __('Applicants'), 'url' => $routeUrl('public.account.applicants.index', '/account/applicants')],
        ]
        : [
            ['label' => __('Dashboard'), 'short_label' => __('Dashboard'), 'url' => $routeUrl('public.account.overview', '/account/overview')],
            ['label' => __('Applied Jobs'), 'short_label' => __('Applied Jobs'), 'url' => $routeUrl('public.account.jobs.applied-jobs', '/account/applied-jobs')],
            ['label' => __('Saved Jobs'), 'short_label' => __('Saved Jobs'), 'url' => $routeUrl('public.account.jobs.saved', '/account/saved-jobs')],
        ];

    $profileMenuItems = $isEmployer
        ? [
            ['label' => __('My Companies'), 'url' => $routeUrl('public.account.companies.index', '/account/companies')],
            ['label' => __('Settings'), 'url' => $routeUrl('public.account.employer.settings.edit', '/account/employer/settings')],
            ['label' => __('Logout'), 'url' => $logoutRoute, 'logout' => true],
        ]
        : [
            ['label' => __('My Profile'), 'url' => $routeUrl('public.account.settings', '/account/settings')],
            ['label' => __('Security'), 'url' => $routeUrl('public.account.security', '/account/security')],
            ['label' => __('Logout'), 'url' => $logoutRoute, 'logout' => true],
        ];

    $accountDisplayName = $isEmployer ? $companyProfileName : $accountName;
    $mobileMenuItems = $primaryNavItems;
    $logoutFormId = 'site-logout-form';

    if ($account) {
        $mobileProfileMenuItems = array_values(array_filter(
            $profileMenuItems,
            function (array $profileMenuItem) use ($authenticatedNavItems): bool {
                foreach ($authenticatedNavItems as $authenticatedNavItem) {
                    if ($authenticatedNavItem['url'] === $profileMenuItem['url']) {
                        return false;
                    }
                }

                return true;
            }
        ));

        $mobileMenuItems = array_merge($mobileMenuItems, $authenticatedNavItems, $mobileProfileMenuItems);
    } else {
        $mobileMenuItems = array_merge($mobileMenuItems, [
            ['label' => __('For Employers'), 'url' => $routeUrl('public.account.register', '/register')],
            ['label' => __('Post a Job'), 'url' => $routeUrl('public.account.register', '/register')],
            ['label' => __('Sign In'), 'url' => $routeUrl('public.account.login', '/login')],
        ]);
    }
@endphp
<header class="header jobrango-site-header {{ $account ? 'jobrango-header--authenticated' : '' }} @if (theme_option('enabled_sticky_header', 'yes') == 'yes') sticky-bar @endif">
    <div class="container">
        <div class="main-header">
            <div class="header-left">
                <div class="header-logo">
                    <a class="d-flex" href="{{ route('public.index') }}">
                        <img alt="{{ theme_option('site_title') }}" src="{{ setting('theme-jobbox-logo') ? RvMedia::getImageUrl(setting('theme-jobbox-logo')) : url(config('core.base.general.logo')) }}">
                    </a>
                </div>
            </div>
            <div class="header-nav">
                <nav class="nav-main-menu">
                    <ul class="main-menu jobrango-main-menu">
                        @foreach ($primaryNavItems as $navItem)
                            <li><a href="{{ $navItem['url'] }}">{{ $navItem['label'] }}</a></li>
                        @endforeach

                        @if ($account)
                            @foreach ($authenticatedNavItems as $navItem)
                                <li><a href="{{ $navItem['url'] }}">{{ $navItem['short_label'] ?? $navItem['label'] }}</a></li>
                            @endforeach
                        @else
                            @foreach ($guestActionItems as $guestActionItem)
                                <li><a class="{{ $guestActionItem['class'] === 'jobrango-header-link' ? 'jobrango-header-link' : '' }}" href="{{ $guestActionItem['url'] }}">{{ $guestActionItem['label'] }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </nav>
                <div class="burger-icon burger-icon-white">
                    <span class="burger-icon-top"></span>
                    <span class="burger-icon-mid"></span>
                    <span class="burger-icon-bottom"></span>
                </div>
            </div>
            <div class="header-right">
                @if (is_plugin_active('job-board'))
                    @if ($account)
                        <div class="jobrango-auth-nav">
                            <ul class="header-menu list-inline d-flex align-items-center mb-0 user-header-dropdown">
                                {!! apply_filters('theme-header-right-nav', null) !!}
                                <li class="list-inline-item dropdown jobrango-auth-nav__profile">
                                    <a href="#" class="d-inline-flex header-item jobrango-profile-trigger" id="userdropdown" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <img src="{{ $account->avatar_thumb_url }}" alt="{{ $account->name }}" width="36" height="36" class="rounded-circle jobrango-profile-trigger__avatar">
                                        <span class="jobrango-profile-trigger__name" title="{{ $accountDisplayName }}">
                                            {{ $accountDisplayName }}
                                        </span>
                                        <span class="jobrango-profile-trigger__caret" aria-hidden="true"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userdropdown">
                                        @foreach ($profileMenuItems as $profileMenuItem)
                                            <li>
                                                @if (($profileMenuItem['logout'] ?? false) === true)
                                                    <a class="dropdown-item" href="{{ $profileMenuItem['url'] }}" onclick="event.preventDefault(); document.getElementById('{{ $logoutFormId }}').submit();">{{ $profileMenuItem['label'] }}</a>
                                                @else
                                                    <a class="dropdown-item" href="{{ $profileMenuItem['url'] }}">{{ $profileMenuItem['label'] }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                            <form id="{{ $logoutFormId }}" action="{{ $logoutRoute }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</header>
<div class="mobile-header-active mobile-header-wrapper-style perfect-scrollbar">
    <div class="offcanvas-header justify-content-end">
        <button type="button" class="btn-close burger-close burger-icon" aria-label="Close"></button>
    </div>
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-content-area">
            <div class="perfect-scroll">
                <div class="jobrango-mobile-menu__top">
                    <a class="jobrango-mobile-menu__brand" href="{{ route('public.index') }}">
                        <img alt="{{ theme_option('site_title') }}" src="{{ setting('theme-jobbox-logo') ? RvMedia::getImageUrl(setting('theme-jobbox-logo')) : url(config('core.base.general.logo')) }}">
                    </a>
                    @if ($account)
                        <a class="jobrango-mobile-menu__account" href="{{ $dashboardRoute }}">
                            <img src="{{ $account->avatar_thumb_url }}" alt="{{ $account->name }}" width="44" height="44" class="rounded-circle">
                            <span>
                                <small>{{ $isEmployer ? __('Employer account') : __('My account') }}</small>
                                <strong>{{ $accountDisplayName }}</strong>
                            </span>
                        </a>
                    @endif
                </div>
                <div class="mobile-menu-wrap mobile-header-border">
                    <span class="jobrango-mobile-menu__label">{{ __('Navigation') }}</span>
                    <nav>
                        <ul class="mobile-menu font-heading">
                            @foreach ($mobileMenuItems as $navItem)
                                <li>
                                    @if (($navItem['logout'] ?? false) === true)
                                        <a href="{{ $navItem['url'] }}" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit()">{{ $navItem['label'] }}</a>
                                    @else
                                        <a href="{{ $navItem['url'] }}">{{ $navItem['label'] }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
                <form id="mobile-logout-form" action="{{ $logoutRoute }}" method="post">
                    @csrf
                </form>
                <div class="site-copyright">{!! BaseHelper::clean(theme_option('copyright')) !!}</div>
            </div>
        </div>
    </div>
</div>
