@php($account = auth('account')->user())

<div class="ps-sidebar__top">
    <div class="ps-block--user-wellcome">
        <div class="ps-block__left">
            <img
                src="{{ $account->avatar_url }}"
                alt="{{ $account->name }}"
                class="avatar avatar-lg"
            />
        </div>
        <div class="ps-block__right">
            <p>{{ trans('plugins/job-board::dashboard.hello') }}, {{ $account->name }}</p>
            <small>{{ trans('plugins/job-board::dashboard.joined_on', ['date' => $account->created_at->translatedFormat('M d, Y')]) }}</small>
            <small class="d-block mt-1">{{ $account->isEmployer() ? __('Employer account') : __('Job seeker account') }}</small>
        </div>
        <div class="ps-block__action">
            <a
                href="{{ route('public.account.logout') }}"
                title="{{ trans('plugins/job-board::dashboard.header_logout_link') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            >
                <x-core::icon name="ti ti-logout" />
            </a>
        </div>
    </div>

    @if (JobBoardHelper::isEnabledCreditsSystem())
        <div class="ps-block--earning-count">
            <small>{{ trans('plugins/job-board::dashboard.credits') }}</small>
            <h3 class="my-2">{{ number_format(auth('account')->user()->credits) }}</h3>
            <a href="{{ route('public.account.packages') }}" target="_blank">
                {{ trans('plugins/job-board::dashboard.buy_credits') }}
            </a>
        </div>
    @endif
</div>
