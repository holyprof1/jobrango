@php
    $account = auth('account')->user();
    $accountInitials = collect(explode(' ', $account->name))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $companyName = $account->companies()->value('name') ?: $account->name;
    $logoutFormId = 'dashboard-logout-' . Str::random(8);
@endphp

<div class="jobrango-employer-panel">
    <div class="jobrango-employer-panel__avatar">
        @if ($account->avatar_id)
            <img src="{{ $account->avatar_thumb_url }}" alt="{{ $account->name }}">
        @else
            <span>{{ $accountInitials ?: 'JR' }}</span>
        @endif
    </div>
    <div class="jobrango-employer-panel__copy">
        <span>{{ __('Employer account') }}</span>
        <h4>{{ Str::limit($companyName, 26) }}</h4>
        <p>{{ $account->display_id }}</p>
    </div>
    <div class="jobrango-employer-panel__actions">
        <a href="{{ route('public.account.employer.settings.edit') }}" title="{{ __('Settings') }}">
            <x-core::icon name="ti ti-settings" />
        </a>
        <a
            href="{{ route('public.account.logout') }}"
            title="{{ __('Logout') }}"
            onclick="event.preventDefault(); document.getElementById('{{ $logoutFormId }}').submit();"
        >
            <x-core::icon name="ti ti-logout" />
        </a>
        <form id="{{ $logoutFormId }}" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
