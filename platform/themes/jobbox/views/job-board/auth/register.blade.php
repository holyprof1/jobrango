<section class="pt-100 login-register">
    <div class="container">
        <div class="row login-register-cover">
            <div class="col-lg-5 col-md-8 col-sm-12 mx-auto auth-card">
                <div class="text-center">
                    <p class="font-sm text-brand-2">{{ __('Register') }}</p>
                    <h2 class="mt-10 mb-5 text-brand-1">{{ __("Let's Get Started") }}</h2>
                    <p class="font-sm text-muted mb-30">{{ __('Create your JobRango account to apply for jobs or start hiring.') }}</p>
                </div>

                <br>
                @if (session()->has('status'))
                    <div role="alert" class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @elseif (session()->has('auth_error_message'))
                    <div role="alert" class="alert alert-danger">
                        {{ session('auth_error_message') }}
                    </div>
                @elseif (session()->has('auth_success_message'))
                    <div role="alert" class="alert alert-success">
                        {{ session('auth_success_message') }}
                    </div>
                @elseif (session()->has('auth_warning_message'))
                    <div role="alert" class="alert alert-warning">
                        {{ session('auth_warning_message') }}
                    </div>
                @endif

                {!!
                    $form
                        ->formClass('text-start mt-20 auth-form')
                        ->when(setting('job_board_enabled_register_as_employer', 1), function ($form) {
                            return $form
                                ->modify('is_employer', 'html', ['html' => sprintf('<div class="mb-3 position-relative"><label class="cb-container">
                                    <input type="checkbox" name="is_employer" value="1">
                                    <span class="text-small">%s</span>
                                    <span class="checkmark"></span>
                                </label></div>', __('Register as employer'))], true);
                        })
                         ->modify('agree_terms_and_policy', 'html', ['html' => sprintf('<div class="mb-3 position-relative"><label class="cb-container">
                                    <input type="checkbox" name="agree_terms_and_policy" value="1">
                                    <span class="text-small">%s</span>
                                    <span class="checkmark"></span>
                                </label></div>', __('Agree our terms and policy'))], true)
                        ->renderForm()
                !!}
            </div>
        </div>
    </div>
</section>
