@php
    $account = auth('account')->user();
    $customQuestions = \Botble\JobBoard\Supports\ApplicationFormManager::questionsForJob($job);
    $isGuestApplyBlocked = ! auth('account')->check() && ! \Botble\JobBoard\Facades\JobBoardHelper::isGuestApplyEnabled();
    $isEmployerViewer = $account && $account->isEmployer();
@endphp

<section class="jobrango-application-section" id="job-apply">
    <div class="jobrango-application-section__header">
        <div>
            <span class="jobrango-overview__eyebrow">{{ __('Apply') }}</span>
            <h3>{{ __('Apply for :job', ['job' => $job->name]) }}</h3>
            <p>
                @if ($job->apply_url)
                    {{ __('Candidates complete this form first, then continue to the employer\'s application page.') }}
                @else
                    {{ __('Submit your details, supporting files, and any custom answers required for this role.') }}
                @endif
            </p>
        </div>
        @if ($job->salary_text)
            <div class="jobrango-application-section__salary">{{ $job->salary_text }}</div>
        @endif
    </div>

    @if ($job->is_applied)
        <div class="jobrango-application-section__notice is-success">
            {{ __('You have already applied for this job.') }}
        </div>
    @elseif (! $job->isJobOpen())
        <div class="jobrango-application-section__notice is-danger">
            {{ __('This job is closed and no longer accepting applications.') }}
        </div>
    @elseif ($isEmployerViewer)
        <div class="jobrango-application-section__notice">
            {{ __('Employer accounts cannot apply to jobs. Switch to a job seeker account to continue.') }}
        </div>
    @elseif ($isGuestApplyBlocked)
        <div class="jobrango-application-section__notice">
            {{ __('You need to sign in before you can apply for this job.') }}
            <a href="{{ route('public.account.login') }}">{{ __('Sign in') }}</a>
        </div>
    @else
        <form class="job-apply-form jobrango-application-form" action="{{ route('public.job.apply', $job->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="job_id" value="{{ $job->id }}">
            <input type="hidden" name="job_type" value="{{ $job->apply_url ? 'external' : 'internal' }}">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="job-apply-first-name">{{ __('First name') }}</label>
                    <input class="form-control" id="job-apply-first-name" name="first_name" type="text" maxlength="120" value="{{ old('first_name', $account->first_name ?? '') }}" placeholder="{{ __('Enter first name') }}" @required(! $job->apply_url)>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="job-apply-last-name">{{ __('Last name') }}</label>
                    <input class="form-control" id="job-apply-last-name" name="last_name" type="text" maxlength="120" value="{{ old('last_name', $account->last_name ?? '') }}" placeholder="{{ __('Enter last name') }}" @required(! $job->apply_url)>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="job-apply-email">{{ __('Email address') }}</label>
                    <input class="form-control" id="job-apply-email" name="email" type="email" value="{{ old('email', $account->email ?? '') }}" placeholder="{{ __('Enter email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="job-apply-phone">{{ __('Phone') }}</label>
                    <input class="form-control" id="job-apply-phone" name="phone" type="text" value="{{ old('phone', $account->phone ?? '') }}" placeholder="{{ __('Enter phone number') }}">
                </div>

                @foreach ($customQuestions as $question)
                    @php
                        $fieldKey = $question['key'];
                        $fieldId = 'custom-answer-' . $fieldKey;
                        $oldValue = old('custom_answers.' . $fieldKey);
                    @endphp

                    <div class="col-12">
                        <div class="jobrango-custom-question">
                            <label class="form-label" for="{{ $fieldId }}">
                                {{ $question['label'] }}
                                @if ($question['required'])
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if ($question['type'] === 'paragraph')
                                <textarea class="form-control" id="{{ $fieldId }}" name="custom_answers[{{ $fieldKey }}]" rows="5" placeholder="{{ $question['placeholder'] }}" @required($question['required'])>{{ is_string($oldValue) ? $oldValue : '' }}</textarea>
                            @elseif ($question['type'] === 'multiple_choice')
                                <select class="form-control" id="{{ $fieldId }}" name="custom_answers[{{ $fieldKey }}]" @required($question['required'])>
                                    <option value="">{{ __('Select an option') }}</option>
                                    @foreach ($question['options'] as $option)
                                        <option value="{{ $option }}" @selected($oldValue === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif ($question['type'] === 'checkbox')
                                @php($selectedValues = is_array($oldValue) ? $oldValue : [])
                                <div class="jobrango-checkbox-grid">
                                    @foreach ($question['options'] as $index => $option)
                                        <label class="jobrango-check-option" for="{{ $fieldId }}-{{ $index }}">
                                            <input id="{{ $fieldId }}-{{ $index }}" name="custom_answers[{{ $fieldKey }}][]" type="checkbox" value="{{ $option }}" @checked(in_array($option, $selectedValues, true))>
                                            <span>{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($question['type'] === 'yes_no')
                                <div class="jobrango-radio-grid">
                                    <label class="jobrango-check-option" for="{{ $fieldId }}-yes">
                                        <input id="{{ $fieldId }}-yes" name="custom_answers[{{ $fieldKey }}]" type="radio" value="yes" @checked($oldValue === 'yes') @required($question['required'])>
                                        <span>{{ __('Yes') }}</span>
                                    </label>
                                    <label class="jobrango-check-option" for="{{ $fieldId }}-no">
                                        <input id="{{ $fieldId }}-no" name="custom_answers[{{ $fieldKey }}]" type="radio" value="no" @checked($oldValue === 'no') @required($question['required'])>
                                        <span>{{ __('No') }}</span>
                                    </label>
                                </div>
                            @elseif (in_array($question['type'], ['file_upload', 'cv_upload'], true))
                                <input class="form-control" id="{{ $fieldId }}" name="custom_answer_files[{{ $fieldKey }}]" type="file" @if($question['accept']) accept="{{ $question['accept'] }}" @endif @required($question['required'])>
                            @else
                                <input class="form-control" id="{{ $fieldId }}" name="custom_answers[{{ $fieldKey }}]" type="{{ $question['type'] === 'email' ? 'email' : 'text' }}" maxlength="255" value="{{ is_string($oldValue) ? $oldValue : '' }}" placeholder="{{ $question['placeholder'] }}" @required($question['required'])>
                            @endif

                            @if ($question['help_text'])
                                <small>{{ $question['help_text'] }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="col-12">
                    <label class="form-label" for="job-apply-message">{{ __('Message') }}</label>
                    <textarea class="form-control" id="job-apply-message" name="message" rows="5" placeholder="{{ __('Tell the employer why you are a good fit') }}" @required(setting('job_board_require_message_in_apply_job', false))>{{ old('message') }}</textarea>
                </div>

                @if (! $job->apply_url)
                    <div class="col-md-6">
                        <label class="form-label" for="job-apply-resume">
                            {{ setting('job_board_require_resume_in_apply_job', false) && ! ($account && $account->resume) ? __('Resume upload') : __('Resume upload (optional)') }}
                        </label>
                        <input class="form-control" id="job-apply-resume" name="resume" type="file" @required(setting('job_board_require_resume_in_apply_job', false) && ! ($account && $account->resume))>
                        @if ($account && $account->resume)
                            <small>{!! \Botble\Base\Facades\BaseHelper::clean(__('Current resume on file: :resume', ['resume' => '<a href="' . \Botble\Media\Facades\RvMedia::url($account->resume) . '" target="_blank">' . e($account->resume) . '</a>'])) !!}</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="job-apply-cover-letter">
                            {{ setting('job_board_require_cover_letter_in_apply_job', false) && ! ($account && $account->cover_letter) ? __('Cover letter upload') : __('Cover letter upload (optional)') }}
                        </label>
                        <input class="form-control" id="job-apply-cover-letter" name="cover_letter" type="file" @required(setting('job_board_require_cover_letter_in_apply_job', false) && ! ($account && $account->cover_letter))>
                        @if ($account && $account->cover_letter)
                            <small>{!! \Botble\Base\Facades\BaseHelper::clean(__('Current cover letter on file: :cover_letter', ['cover_letter' => '<a href="' . \Botble\Media\Facades\RvMedia::url($account->cover_letter) . '" target="_blank">' . e($account->cover_letter) . '</a>'])) !!}</small>
                        @endif
                    </div>
                @endif

                @if (is_plugin_active('captcha') && setting('enable_captcha') && setting('job_board_enable_recaptcha_in_apply_job', 0))
                    <div class="col-12">
                        {!! \Botble\Captcha\Facades\Captcha::display() !!}
                    </div>
                @endif
            </div>

            <div class="jobrango-application-form__footer">
                @if ($job->apply_url)
                    <p>{{ __('After submitting, we will open the employer\'s application page for the final step.') }}</p>
                @else
                    <p>{{ __('Your application will be sent directly to the employer from here.') }}</p>
                @endif
                <button class="btn btn-default btn-shadow hover-up" type="submit">
                    {{ $job->apply_url ? __('Continue to Employer Site') : __('Send Application') }}
                </button>
            </div>
        </form>
    @endif
</section>
