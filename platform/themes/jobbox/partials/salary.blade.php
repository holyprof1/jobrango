@php
    $salaryRange = $job->displaySalaryRangeLabel();
@endphp

@if ($job->hide_salary)
    <span class="text-muted">{{ __('Attractive') }}</span>
@elseif ($job->isNegotiableSalary())
    <span class="card-text-price">{{ __('Negotiable') }}</span>
@elseif ($job->salary_from || $job->salary_to)
    @if(! JobBoardHelper::isSalaryHiddenForGuests())
        @if ($job->salary_from && $job->salary_to)
            <span class="card-text-price" title="{{ $job->formatDisplayedSalaryAmount($job->salary_from) }} - {{ $job->formatDisplayedSalaryAmount($job->salary_to) }}">
            {{ $job->formatDisplayedSalaryAmount($job->salary_from) }} - {{ $job->formatDisplayedSalaryAmount($job->salary_to) }}
        </span>
        @elseif ($job->salary_from)
            <span class="card-text-price" title="{{ __('From :price', ['price' => $job->formatDisplayedSalaryAmount($job->salary_from)]) }}">
            {{ __('From :price', ['price' => $job->formatDisplayedSalaryAmount($job->salary_from)]) }}
        </span>
        @elseif ($job->salary_to)
            <span class="card-text-price" title="{{ __('Upto :price', ['price' => $job->formatDisplayedSalaryAmount($job->salary_to)]) }}">
            {{ __('Upto :price', ['price' => $job->formatDisplayedSalaryAmount($job->salary_to)]) }}
        </span>
        @endif
        <span class="text-muted">/ {{ $salaryRange }}</span>
    @else
        <a class="job-hidden-job-for-guest-text" href="{{ route('public.account.login') }}">
            <x-core::icon name="ti ti-coin" />
            {{ __('Sign in to view salary') }}
        </a>
    @endif
@elseif ($job->listing_salary_text)
    <span class="text-muted">{{ $job->listing_salary_text }}</span>
@else
    <span class="text-muted">{{ __('Attractive') }}</span>
@endif
