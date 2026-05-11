@extends(JobBoardHelper::viewPath('dashboard.layouts.master'))

@php
    $candidate = $jobApplication->account;
    $job = $jobApplication->job;
    $company = $job->company;
    $candidateSlug = $candidate->slugable?->key;
    $profileUrl = $candidate->id && $candidate->is_public_profile && $candidateSlug
        ? route('public.candidate', $candidateSlug)
        : null;
    $jobUrl = $jobApplication->job_url ?: null;
    $resumeUrl = ($jobApplication->resume || ($candidate->id && $candidate->resume))
        ? route('public.account.applicants.download-cv', $jobApplication->id)
        : null;
    $coverLetterUrl = $jobApplication->cover_letter ? RvMedia::url($jobApplication->cover_letter) : null;
    $candidateInitials = \Illuminate\Support\Str::of($jobApplication->full_name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))
        ->implode('');
    $candidateInitials = \Illuminate\Support\Str::upper($candidateInitials ?: \Illuminate\Support\Str::substr($jobApplication->full_name, 0, 1));
    $candidateSummary = trim((string) ($candidate->bio ?: $candidate->description ?: ''));
@endphp

@section('content')
    <div class="jobrango-employer-panel-card jobrango-applicant-review">
        <div class="jobrango-employer-panel-card__header">
            <div>
                <span class="jobrango-applicant-review__eyebrow">{{ __('Applicant review') }}</span>
                <h3>{{ $jobApplication->full_name }}</h3>
                <p>{{ __('Review the candidate, open their supporting files, and update the application status from one place.') }}</p>
            </div>
            <div class="jobrango-action-list">
                <a href="{{ route('public.account.applicants.index', ['job_id' => $jobApplication->job_id]) }}">{{ __('Back to Applicants') }}</a>
                @if ($profileUrl)
                    <a href="{{ $profileUrl }}" target="_blank">{{ __('Open Applicant Profile') }}</a>
                @endif
                @if ($jobUrl)
                    <a href="{{ $jobUrl }}" target="_blank">{{ __('View Job') }}</a>
                @endif
            </div>
        </div>

        <section class="jobrango-applicant-review__hero">
            <div class="jobrango-applicant-review__identity">
                @if ($candidate->id && $candidate->avatar_thumb_url)
                    <img class="jobrango-applicant-review__avatar" src="{{ $candidate->avatar_thumb_url }}" alt="{{ $jobApplication->full_name }}">
                @else
                    <span class="jobrango-applicant-review__avatar jobrango-applicant-review__avatar--fallback">{{ $candidateInitials }}</span>
                @endif
                <div class="jobrango-applicant-review__identity-copy">
                    <div class="jobrango-applicant-review__identity-line">
                        @if ($profileUrl)
                            <a href="{{ $profileUrl }}" target="_blank">{{ $jobApplication->full_name }}</a>
                        @else
                            <strong>{{ $jobApplication->full_name }}</strong>
                        @endif
                        {!! $jobApplication->status->toHtml() !!}
                    </div>
                    <p>{{ $jobApplication->display_id }} @if ($candidate->id) &bull; {{ $candidate->display_id }} @endif</p>
                    <div class="jobrango-applicant-review__highlights">
                        <span>{{ __('Applied :time', ['time' => $jobApplication->created_at->diffForHumans()]) }}</span>
                        @if ($candidate->available_for_hiring)
                            <span>{{ __('Open to work') }}</span>
                        @endif
                        @if (! $profileUrl)
                            <span>{{ __('Public profile not available') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="jobrango-applicant-review__hero-grid">
                <article>
                    <span>{{ __('Applied role') }}</span>
                    <strong>
                        @if ($jobUrl)
                            <a href="{{ $jobUrl }}" target="_blank">{{ $job->name }}</a>
                        @else
                            {{ $job->name }}
                        @endif
                    </strong>
                    <p>{{ $job->display_id }}</p>
                </article>
                <article>
                    <span>{{ __('Company') }}</span>
                    <strong>{{ $company->name ?: __('No company selected') }}</strong>
                    <p>{{ $job->display_location ?: __('Location not specified') }}</p>
                </article>
                <article>
                    <span>{{ __('Contact') }}</span>
                    <strong><a href="mailto:{{ $jobApplication->email }}">{{ $jobApplication->email }}</a></strong>
                    <p>
                        @if ($jobApplication->phone)
                            <a href="tel:{{ $jobApplication->phone }}">{{ $jobApplication->phone }}</a>
                        @else
                            {{ __('Phone not provided') }}
                        @endif
                    </p>
                </article>
            </div>
        </section>

        <div class="jobrango-applicant-review__layout">
            <div class="jobrango-applicant-review__main">
                <section class="jobrango-applicant-review__card">
                    <div class="jobrango-applicant-review__card-header">
                        <h4>{{ __('Profile snapshot') }}</h4>
                        @if ($profileUrl)
                            <a href="{{ $profileUrl }}" target="_blank">{{ __('Visit public profile') }}</a>
                        @endif
                    </div>
                    <div class="jobrango-applicant-review__details">
                        <article>
                            <span>{{ __('Name') }}</span>
                            <strong>
                                @if ($profileUrl)
                                    <a href="{{ $profileUrl }}" target="_blank">{{ $jobApplication->full_name }}</a>
                                @else
                                    {{ $jobApplication->full_name }}
                                @endif
                            </strong>
                        </article>
                        <article>
                            <span>{{ __('Email') }}</span>
                            <strong><a href="mailto:{{ $jobApplication->email }}">{{ $jobApplication->email }}</a></strong>
                        </article>
                        <article>
                            <span>{{ __('Phone') }}</span>
                            <strong>
                                @if ($jobApplication->phone)
                                    <a href="tel:{{ $jobApplication->phone }}">{{ $jobApplication->phone }}</a>
                                @else
                                    {{ __('Not provided') }}
                                @endif
                            </strong>
                        </article>
                        <article>
                            <span>{{ __('Resume') }}</span>
                            <strong>
                                @if ($resumeUrl)
                                    <a href="{{ $resumeUrl }}">{{ __('Download CV') }}</a>
                                @else
                                    {{ __('Not uploaded') }}
                                @endif
                            </strong>
                        </article>
                    </div>

                    @if ($candidateSummary)
                        <div class="jobrango-applicant-review__note">
                            <h5>{{ __('Candidate summary') }}</h5>
                            <p>{{ $candidateSummary }}</p>
                        </div>
                    @endif
                </section>

                @if ($jobApplication->message)
                    <section class="jobrango-applicant-review__card">
                        <div class="jobrango-applicant-review__card-header">
                            <h4>{{ __('Application message') }}</h4>
                        </div>
                        <div class="jobrango-applicant-review__rich-text">
                            {!! nl2br(e($jobApplication->message)) !!}
                        </div>
                    </section>
                @endif

                @if ($jobApplication->application_answers)
                    <section class="jobrango-applicant-review__card">
                        <div class="jobrango-applicant-review__card-header">
                            <h4>{{ __('Custom application answers') }}</h4>
                        </div>
                        <div class="jobrango-applicant-review__answers">
                            @foreach ($jobApplication->application_answers as $answer)
                                @php
                                    $value = $answer['value'] ?? null;
                                    $type = $answer['type'] ?? null;
                                @endphp
                                <article>
                                    <span>{{ $answer['label'] ?? __('Question') }}</span>
                                    <strong>
                                        @if (is_array($value))
                                            {{ implode(', ', $value) }}
                                        @elseif (in_array($type, ['file_upload', 'cv_upload'], true) && $value)
                                            <a href="{{ RvMedia::url($value) }}" target="_blank">{{ basename((string) $value) }}</a>
                                        @elseif (filled($value))
                                            {{ $value }}
                                        @else
                                            {{ __('No response provided') }}
                                        @endif
                                    </strong>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="jobrango-applicant-review__card">
                    <div class="jobrango-applicant-review__card-header">
                        <h4>{{ __('Screening result') }}</h4>
                    </div>
                    <div class="jobrango-applicant-review__answers">
                        <article>
                            <span>{{ __('Screening badge') }}</span>
                            <strong>{!! \Botble\Base\Facades\BaseHelper::renderBadge($jobApplication->screening_status_label, $jobApplication->screening_status_color) !!}</strong>
                        </article>
                        @forelse (($jobApplication->screening_summary['reasons'] ?? []) as $reason)
                            <article>
                                <span>{{ $reason['question_label'] ?? __('Rule') }}</span>
                                <strong>
                                    {{ \Botble\JobBoard\Supports\ApplicantScreeningManager::operatorOptions()[$reason['operator'] ?? 'equals'] ?? ($reason['operator'] ?? __('Matched')) }}
                                    @if (! empty($reason['expected']))
                                        : {{ is_array($reason['expected']) ? implode(', ', $reason['expected']) : $reason['expected'] }}
                                    @endif
                                    @if (! empty($reason['actual']) && ! is_array($reason['actual']))
                                        <small>{{ __('(Answer: :value)', ['value' => $reason['actual']]) }}</small>
                                    @endif
                                </strong>
                            </article>
                        @empty
                            <article>
                                <span>{{ __('Reason') }}</span>
                                <strong>{{ __('No automatic rule matched this application yet.') }}</strong>
                            </article>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="jobrango-applicant-review__sidebar">
                <section class="jobrango-applicant-review__card">
                    <div class="jobrango-applicant-review__card-header">
                        <h4>{{ __('Review status') }}</h4>
                    </div>
                    <form method="POST" action="{{ route('public.account.applicants.update', $jobApplication->id) }}" class="jobrango-applicant-review__status-form">
                        @csrf
                        @method('PUT')
                        <label class="form-label" for="application-status">{{ __('Application status') }}</label>
                        <select class="form-control" id="application-status" name="status">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected($jobApplication->status->getValue() === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-default btn-shadow hover-up" type="submit">{{ __('Save Status') }}</button>
                    </form>
                </section>

                <section class="jobrango-applicant-review__card">
                    <div class="jobrango-applicant-review__card-header">
                        <h4>{{ __('Files and links') }}</h4>
                    </div>
                    <div class="jobrango-applicant-review__link-list">
                        @if ($resumeUrl)
                            <a href="{{ $resumeUrl }}">{{ __('Download CV') }}</a>
                        @endif
                        @if ($coverLetterUrl)
                            <a href="{{ $coverLetterUrl }}" target="_blank">{{ __('Open cover letter file') }}</a>
                        @endif
                        @if ($profileUrl)
                            <a href="{{ $profileUrl }}" target="_blank">{{ __('Open applicant profile') }}</a>
                        @endif
                        @if ($jobUrl)
                            <a href="{{ $jobUrl }}" target="_blank">{{ __('Open job posting') }}</a>
                        @endif
                        @if (! $resumeUrl && ! $coverLetterUrl && ! $profileUrl && ! $jobUrl)
                            <p>{{ __('No additional files or links are available for this application yet.') }}</p>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
