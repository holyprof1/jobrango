@php
    $companyName = trim($companyName ?? '') ?: trim($job?->company_name ?? '') ?: trim($job?->company?->name ?? '') ?: trim($job?->name ?? __('Company'));
    $companyUrl = $companyUrl ?? ($job?->company_url ?: null);
    $logo = $logo ?? ($job?->company_logo_thumb ?? $job?->company?->logo_thumb ?? null);
    $defaultCompanyLogo = theme_option('default_company_logo');
    $initials = \Illuminate\Support\Str::of($companyName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($word) => \Illuminate\Support\Str::substr($word, 0, 1))
        ->implode('');
    $initials = $initials ?: \Illuminate\Support\Str::substr($companyName, 0, 1);
@endphp

<div @class(['job-company-badge', $wrapperClass ?? null])>
    @if ($companyUrl)
        <a href="{{ $companyUrl }}" class="job-company-badge__link" aria-label="{{ $companyName }}">
            @if ($logo || $defaultCompanyLogo)
                <img
                    src="{{ RvMedia::getImageUrl($logo ?: $defaultCompanyLogo) }}"
                    alt="{{ $companyName }}"
                    class="job-company-badge__image"
                >
            @else
                <span class="job-company-badge__fallback">{{ \Illuminate\Support\Str::upper($initials) }}</span>
            @endif
        </a>
    @else
        <span class="job-company-badge__link" aria-label="{{ $companyName }}">
            @if ($logo || $defaultCompanyLogo)
                <img
                    src="{{ RvMedia::getImageUrl($logo ?: $defaultCompanyLogo) }}"
                    alt="{{ $companyName }}"
                    class="job-company-badge__image"
                >
            @else
                <span class="job-company-badge__fallback">{{ \Illuminate\Support\Str::upper($initials) }}</span>
            @endif
        </span>
    @endif
</div>
