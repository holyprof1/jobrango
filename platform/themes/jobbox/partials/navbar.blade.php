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
    $account = auth('account')->user();
    $isEmployer = $account?->isEmployer() ?? false;
    $isJobSeeker = $account?->isJobSeeker() ?? false;
    $dashboardRoute = $isEmployer ? route('public.account.dashboard') : route('public.account.overview');
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
        ['label' => __('For Employers'), 'url' => route('public.account.register'), 'class' => 'jobrango-header-link'],
        ['label' => __('Post a Job'), 'url' => route('public.account.register'), 'class' => 'jobrango-header-link'],
        ['label' => __('Sign In'), 'url' => route('public.account.login'), 'class' => 'btn btn-default btn-shadow hover-up'],
    ];

    $authenticatedNavItems = $isEmployer
        ? [
            ['label' => __('Employer Dashboard'), 'url' => route('public.account.dashboard')],
            ['label' => __('Post a Job'), 'url' => route('public.account.jobs.create')],
            ['label' => __('Applicants'), 'url' => route('public.account.applicants.index')],
        ]
        : [
            ['label' => __('Dashboard'), 'url' => route('public.account.overview')],
            ['label' => __('Applied Jobs'), 'url' => route('public.account.jobs.applied-jobs')],
            ['label' => __('Saved Jobs'), 'url' => route('public.account.jobs.saved')],
        ];

    $profileMenuItems = $isEmployer
        ? [
            ['label' => __('Employer Dashboard'), 'url' => route('public.account.dashboard')],
            ['label' => __('My Companies'), 'url' => route('public.account.companies.index')],
            ['label' => __('Settings'), 'url' => route('public.account.employer.settings.edit')],
        ]
        : [
            ['label' => __('My Profile'), 'url' => route('public.account.settings')],
            ['label' => __('Dashboard'), 'url' => route('public.account.overview')],
            ['label' => __('Security'), 'url' => route('public.account.security')],
        ];

    $mobileMenuItems = $primaryNavItems;

    if ($account) {
        $mobileMenuItems = array_merge($mobileMenuItems, $authenticatedNavItems, $profileMenuItems, [
            ['label' => __('Logout'), 'url' => route('public.account.logout'), 'logout' => true],
        ]);
    } else {
        $mobileMenuItems = array_merge($mobileMenuItems, [
            ['label' => __('For Employers'), 'url' => route('public.account.register')],
            ['label' => __('Post a Job'), 'url' => route('public.account.register')],
            ['label' => __('Sign In'), 'url' => route('public.account.login')],
        ]);
    }
@endphp
<header class="header @if (theme_option('enabled_sticky_header', 'yes') == 'yes') sticky-bar @endif">
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
                    @auth('account')
                        <div class="jobrango-auth-nav">
                            <ul class="header-menu list-inline d-flex align-items-center mb-0 user-header-dropdown">
                                {!! apply_filters('theme-header-right-nav', null) !!}
                                @foreach ($authenticatedNavItems as $navItem)
                                    <li class="list-inline-item">
                                        <a class="header-item" href="{{ $navItem['url'] }}">{{ $navItem['label'] }}</a>
                                    </li>
                                @endforeach
                                <li class="list-inline-item dropdown">
                                    <a href="#" class="d-inline-flex header-item jobrango-profile-trigger" id="userdropdown" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <img src="{{ $account->avatar_thumb_url }}" alt="{{ $account->name }}" width="38" height="38" class="rounded-circle me-2">
                                        <span class="text-left fw-medium icon-down" title="{{ $isEmployer ? $companyProfileName : $accountName }}">
                                            {{ $isEmployer ? $companyProfileName : __('Profile') }}
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userdropdown">
                                        @foreach ($profileMenuItems as $profileMenuItem)
                                            <li><a class="dropdown-item" href="{{ $profileMenuItem['url'] }}">{{ $profileMenuItem['label'] }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="list-inline-item">
                                    <a class="header-item jobrango-logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#">{{ __('Logout') }}</a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @else
                        <div class="block-signin">
                            @foreach ($guestActionItems as $guestActionItem)
                                <a class="{{ $guestActionItem['class'] }}" href="{{ $guestActionItem['url'] }}">{{ $guestActionItem['label'] }}</a>
                            @endforeach
                        </div>
                    @endauth
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
                <div class="mobile-menu-wrap mobile-header-border">
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
                <form id="mobile-logout-form" action="{{ route('public.account.logout') }}" method="post">
                    @csrf
                </form>
                <div class="site-copyright">{!! BaseHelper::clean(theme_option('copyright')) !!}</div>
            </div>
        </div>
    </div>
</div>
