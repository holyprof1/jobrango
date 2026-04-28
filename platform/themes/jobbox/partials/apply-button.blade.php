@if ($job->canShowApplyJob())
    @php($classButtonApply = $class ?? 'btn btn-apply-now')
    @php($applyTarget = url()->current() === $job->url ? '#job-apply' : $job->url . '#job-apply')
    <div class="{{ $wrapClass ?? '' }}">
        @if ($job->is_applied)
            <button class="{{ $classButtonApply }} disabled" disabled>{{ __('Applied') }}</button>
        @elseif (! $job->isJobOpen())
            <button disabled
                style="background-color: #f8d7da;  border: 0; background-image: unset"
                class="{{ $classButtonApply }} text-danger"
            >
                {{ __('Closed') }}
            </button>
        @elseif ($job->apply_url)
            <a href="{{ $applyTarget }}">
                <div class="{{ $classButtonApply }}">{{ __('Apply Now') }}</div>
            </a>
        @elseif (!auth('account')->check() && !JobBoardHelper::isGuestApplyEnabled())
            <a href="{{ route('public.account.login') }}">
                <div class="{{ $classButtonApply }}">{{ __('Apply Now') }}</div>
            </a>
        @else
            <a href="{{ $applyTarget }}">
                <div class="{{ $classButtonApply }}">{{ __('Apply Now') }}</div>
            </a>
        @endif
    </div>
@endif
