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
    $dashboardRoute = $account?->isEmployer() ? route('public.account.dashboard') : route('public.account.overview');
    $accountName = $account ? Str::limit($account->name, 15) : null;
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
                        <ul class="header-menu list-inline d-flex align-items-center mb-0 user-header-dropdown">
                            {!! apply_filters('theme-header-right-nav', null) !!}
                            @if ($account)
                                <li class="list-inline-item">
                                    <a class="header-item" href="{{ $dashboardRoute }}">
                                        {{ $account->isEmployer() ? __('Employer Dashboard') : __('Dashboard') }}
                                    </a>
                                </li>
                                @if ($account->isEmployer())
                                    <li class="list-inline-item">
                                        <a class="header-item" href="{{ route('public.account.jobs.create') }}">
                                            {{ __('Post a Job') }}
                                        </a>
                                    </li>
                                @endif
                                <li class="list-inline-item dropdown">
                                    <a href="#" class="d-inline-flex header-item" id="userdropdown" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <img src="{{ $account->avatar_thumb_url }}" alt="{{ $account->name }}" width="35" height="35" class="rounded-circle me-1 mt-1 mr-2">
                                        <span class="text-left fw-medium icon-down" title="{{ __('Hi, :name', ['name' => $accountName]) }}">{{ __('Hi, :name', ['name' => $accountName]) }} </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userdropdown">
                                        @if ($account->isEmployer())
                                            <li><a class="dropdown-item" href="{{ route('public.account.dashboard') }}">{{ __('Employer Dashboard') }}</a></li>
                                            <li><a class="dropdown-item" href="{{ route('public.account.jobs.create') }}">{{ __('Post a Job') }}</a></li>
                                            <li><a class="dropdown-item" href="{{ route('public.account.companies.index') }}">{{ __('My Companies') }}</a></li>
                                        @else
                                            <li><a class="dropdown-item" href="{{ route('public.account.overview') }}">{{ __('Dashboard') }}</a></li>
                                            <li><a class="dropdown-item" href="{{ route('public.account.jobs.saved') }}">{{ __('Saved Jobs') }}</a></li>
                                            <li><a class="dropdown-item" href="{{ route('public.account.jobs.applied-jobs') }}">{{ __('Applied Jobs') }}</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="{{ route('public.account.settings') }}">{{ __('Account Settings') }}</a></li>
                                        <li>
                                            <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#">{{ __('Logout') }}</a>
                                            <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
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
                            @foreach ($primaryNavItems as $navItem)
                                <li><a href="{{ $navItem['url'] }}">{{ $navItem['label'] }}</a></li>
                            @endforeach
                            @guest('account')
                                <li><a href="{{ route('public.account.register') }}">{{ __('For Employers') }}</a></li>
                                <li><a href="{{ route('public.account.register') }}">{{ __('Post a Job') }}</a></li>
                                <li><a href="{{ route('public.account.login') }}">{{ __('Sign In') }}</a></li>
                            @endguest
                        </ul>
                    </nav>
                </div>
                @if (is_plugin_active('job-board'))
                    @auth('account')
                        <div class="mobile-account">
                            <h6 class="mb-10">{{ __('Your Account') }}</h6>
                            <ul class="mobile-menu font-heading">
                                <li><a href="{{ $dashboardRoute }}">{{ $account->isEmployer() ? __('Employer Dashboard') : __('Dashboard') }}</a></li>
                                @if ($account->isEmployer())
                                    <li><a href="{{ route('public.account.jobs.create') }}">{{ __('Post a Job') }}</a></li>
                                    <li><a href="{{ route('public.account.companies.index') }}">{{ __('My Companies') }}</a></li>
                                @else
                                    <li><a href="{{ route('public.account.jobs.saved') }}">{{ __('Saved Jobs') }}</a></li>
                                    <li><a href="{{ route('public.account.jobs.applied-jobs') }}">{{ __('Applied Jobs') }}</a></li>
                                @endif
                                <li><a href="{{ route('public.account.settings') }}">{{ __('Account Settings') }}</a></li>
                                <li><a href="{{ route('public.account.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">{{ __('Sign Out') }}</a></li>
                            </ul>
                        </div>
                        <form id="logout-form" action="{{ route('public.account.logout') }}" method="post">
                            @csrf
                        </form>
                    @endauth
                @endif
                <div class="site-copyright">{!! BaseHelper::clean(theme_option('copyright')) !!}</div>
            </div>
        </div>
    </div>
</div>
