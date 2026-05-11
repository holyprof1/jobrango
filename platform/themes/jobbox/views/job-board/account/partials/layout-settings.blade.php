@php
    Theme::asset()->add('avatar-css', 'vendor/core/plugins/job-board/css/avatar.css');
    Theme::asset()->add('tagify-css', 'vendor/core/core/base/libraries/tagify/tagify.css');
    Theme::asset()->container('footer')->add('cropper-js', 'vendor/core/plugins/job-board/libraries/cropper.js', ['jquery']);
    Theme::asset()->container('footer')->add('avatar-js', 'vendor/core/plugins/job-board/js/avatar.js');
    Theme::asset()->container('footer')->add('editor-lib-js', config('core.base.general.editor.' . BaseHelper::getRichEditor() . '.js'));
    Theme::asset()->container('footer')->add('editor-js', 'vendor/core/core/base/js/editor.js');
    Theme::asset()->container('footer')->add('tagify-js', 'vendor/core/core/base/libraries/tagify/tagify.js');
    Theme::asset()->container('footer')->add('tag-js', 'vendor/core/core/base/js/tags.js');

    $url = url()->current();
    $profileSummary = trim(strip_tags((string) ($account->description ?: $account->bio)));
    $profileSummary = $profileSummary ?: __('Complete your profile so employers can quickly understand your experience and preferred role.');
    $resumeLabel = $account->resume ? __('View CV / Resume') : __('Add CV / Resume');
    $publicProfileUrl = filled($account->url) && ! str_ends_with($account->url, '/undefined') ? $account->url : null;
    $accountInitials = collect(explode(' ', $account->name))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $isSettingsRoute = request()->routeIs('public.account.settings');
    $isOverviewRoute = request()->routeIs('public.account.overview');
    $isAppliedRoute = request()->routeIs('public.account.jobs.applied-jobs');
    $isSavedRoute = request()->routeIs('public.account.jobs.saved');
    $isSecurityRoute = request()->routeIs('public.account.security');
    $isExperienceRoute = request()->routeIs('public.account.experiences.*');
    $isEducationRoute = request()->routeIs('public.account.educations.*');
    $completionChecks = [
        filled($account->avatar_id),
        filled($account->address),
        filled($account->description ?: $account->bio),
        filled($account->resume),
        $account->experiences()->exists(),
        $account->educations()->exists(),
    ];
    $profileCompletion = (int) round((collect($completionChecks)->filter()->count() / count($completionChecks)) * 100);
@endphp

<main class="main crop-avatar user-profile-section">
    <section class="section-box jobrango-account-dashboard pt-40 pb-20">
        <div class="container">
            <div class="jobrango-account-hero">
                <div class="jobrango-account-hero__identity">
                    <div class="jobrango-account-hero__avatar">
                        @if ($account->avatar_id)
                            <img src="{{ $account->avatar_url }}" alt="{{ $account->name }}">
                        @else
                            <span>{{ $accountInitials ?: 'JR' }}</span>
                        @endif
                    </div>
                    <div class="jobrango-account-hero__copy">
                        <span class="jobrango-account-hero__eyebrow">{{ __('Job seeker dashboard') }}</span>
                        <h1>{{ $account->name }}</h1>
                        @if ($account->address)
                            <p class="jobrango-account-hero__location">{{ $account->address }}</p>
                        @endif
                        <p class="jobrango-account-hero__summary">{{ $profileSummary }}</p>
                    </div>
                </div>
                <div class="jobrango-account-hero__completion">
                    <span>{{ __('Profile completion') }}</span>
                    <strong>{{ $profileCompletion }}%</strong>
                    <div class="jobrango-account-hero__completion-bar">
                        <span style="width: {{ $profileCompletion }}%"></span>
                    </div>
                </div>
                <div class="jobrango-account-hero__actions">
                    <a class="btn btn-default btn-shadow hover-up" href="{{ route('public.account.settings') }}">{{ __('Edit Profile') }}</a>
                    <a class="btn btn-border hover-up" href="{{ url('/jobs') }}">{{ __('Browse Jobs') }}</a>
                    @if ($account->is_public_profile && $publicProfileUrl)
                        <a class="btn btn-border hover-up" href="{{ $publicProfileUrl }}">{{ __('Preview Profile') }}</a>
                    @else
                        <a class="btn btn-border hover-up" href="{{ route('public.account.settings') }}">{{ $resumeLabel }}</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="section-box pb-50">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="box-nav-tabs nav-tavs-profile mb-5 jobrango-account-sidebar">
                        <div class="jobrango-account-sidebar__heading">
                            <h5>{{ __('My Dashboard') }}</h5>
                            <p>{{ __('Quick access to your profile, saved roles, and applications.') }}</p>
                        </div>
                        <ul class="nav" role="tablist">
                            <li><a @class(['btn btn-border aboutus-icon mb-20', 'active' => $isOverviewRoute]) href="{{ route('public.account.overview') }}">{{ __('Overview') }}</a></li>
                            <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isSettingsRoute]) href="{{ route('public.account.settings') }}">{{ __('My Profile') }}</a></li>
                            <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isAppliedRoute]) href="{{ route('public.account.jobs.applied-jobs') }}">{{ __('Applied Jobs') }}</a></li>
                            <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isSavedRoute]) href="{{ route('public.account.jobs.saved') }}">{{ __('Saved Jobs') }}</a></li>
                            <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isSettingsRoute]) href="{{ route('public.account.settings') }}">{{ __('Resume & Documents') }}</a></li>
                            <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isSecurityRoute]) href="{{ route('public.account.security') }}">{{ __('Security') }}</a></li>
                            @if ($account->isJobSeeker())
                                <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isExperienceRoute]) href="{{ route('public.account.experiences.index') }}">{{ __('Experiences') }}</a></li>
                                <li><a @class(['btn btn-border recruitment-icon mb-20', 'active' => $isEducationRoute]) href="{{ route('public.account.educations.index') }}">{{ __('Educations') }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-12 col-12 mb-50">
                    <div class="content-single jobrango-account-content">
                        <div class="tab-content">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="avatar-modal" tabindex="-1" role="dialog" aria-labelledby="avatar-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form class="avatar-form" method="post" action="{{ route('public.account.avatar') }}" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title" id="avatar-modal-label">
                            <strong>{{ __('Profile Image') }}</strong>
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="avatar-body">

                            <!-- Upload image and data -->
                            <div class="avatar-upload">
                                <input class="avatar-src" name="avatar_src" type="hidden">
                                <input class="avatar-data" name="avatar_data" type="hidden">
                                @csrf
                                <label for="avatarInput">{{ __('New image') }}</label>
                                <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                            </div>

                            <div class="loading" tabindex="-1" role="img" aria-label="{{ __('Loading') }}"></div>

                            <!-- Crop and preview -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="avatar-wrapper"></div>
                                    <div class="error-message text-danger" style="display: none"></div>
                                </div>
                                <div class="col-md-3 avatar-preview-wrapper">
                                    <div class="avatar-preview preview-lg"></div>
                                    <div class="avatar-preview preview-md"></div>
                                    <div class="avatar-preview preview-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button class="btn btn-outline-primary avatar-save" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    'use strict';

    var RV_MEDIA_URL = {
        base: '{{ url('') }}',
        filebrowserImageBrowseUrl: false,
        media_upload_from_editor: '{{ route('public.account.upload-from-editor') }}'
    }

    function setImageValue(file) {
        $('.mce-btn.mce-open').parent().find('.mce-textbox').val(file);
    }
</script>
<iframe id="form_target" name="form_target" style="display:none"></iframe>
<form id="tinymce_form" action="{{ route('public.account.upload-from-editor') }}" target="form_target" method="post" enctype="multipart/form-data"
      style="width:0; height:0; overflow:hidden; display: none;">
    @csrf
    <input name="upload" id="upload_file" type="file" onchange="$('#tinymce_form').submit();this.value='';">
    <input type="hidden" value="tinymce" name="upload_type">
</form>
