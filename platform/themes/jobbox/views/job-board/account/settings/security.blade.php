@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div class="jobrango-panel">
        <div class="jobrango-panel__header">
            <div>
                <h3>{{ __('Security') }}</h3>
                <p>{{ __('Use a strong password and update it anytime you need to secure your account.') }}</p>
            </div>
        </div>

        {!! Form::open(['route' => 'public.account.post.security', 'method' => 'PUT']) !!}
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label for="current-password-input" class="form-label">{{ __('Current password') }}</label>
                    <input type="password" @class(['form-control', 'is-invalid' => $errors->has('old_password')])
                    placeholder="{{ __('Enter current password') }}" name="old_password" id="current-password-input" autocomplete="password" />
                    @error('old_password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="new-password-input" class="form-label">{{ __('New password') }}</label>
                    <input type="password" @class(['form-control', 'is-invalid' => $errors->has('password')])
                    placeholder="{{ __('Enter new password') }}" name="password" id="new-password-input" autocomplete="new-password" />
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="confirm-password-input" class="form-label">{{ __('Password confirmation') }}</label>
                    <input type="password" @class(['form-control', 'is-invalid' => $errors->has('password_confirmation')])
                    placeholder="{{ __('Enter password confirmation') }}" name="password_confirmation" id="confirm-password-input" />
                    @error('password_confirmation')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
