@if ($jobApplication)
    <x-core::datagrid>
        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/job-board::job-application.tables.time') }}
            </x-slot:title>

            {{ $jobApplication->created_at }}
        </x-core::datagrid.item>

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/job-board::job-application.tables.position') }}
            </x-slot:title>

            <a href="{{ $jobApplication->job->url }}" target="_blank">
                {{ $jobApplication->job->name }}
                <x-core::icon name="ti ti-external-link" />
            </a>
        </x-core::datagrid.item>

        @if (!$jobApplication->is_external_apply)
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/job-board::job-application.tables.name') }}
                </x-slot:title>

                @if ($jobApplication->account->id && $jobApplication->account->is_public_profile)
                    <a href="{{ $jobApplication->account->url }}" target="_blank">
                        {{ $jobApplication->account->name }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                @else
                    {{ $jobApplication->full_name }}
                @endif
            </x-core::datagrid.item>
        @endif

        @if ($jobApplication->phone)
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/job-board::job-application.tables.phone') }}
                </x-slot:title>

                <a href="tel:{{ $jobApplication->phone }}">{{ $jobApplication->phone }}</a>
            </x-core::datagrid.item>
        @endif

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/job-board::job-application.tables.email') }}
            </x-slot:title>

            <a href="mailto:{{ $jobApplication->email }}">{{ $jobApplication->email }}</a>
        </x-core::datagrid.item>

        @if (!$jobApplication->is_external_apply)
            @if ($jobApplication->resume || ($jobApplication->account->id && $jobApplication->account->resume))
                <x-core::datagrid.item>
                    <x-slot:title>
                        {{ trans('plugins/job-board::job-application.tables.resume') }}
                    </x-slot:title>

                    <a
                        href="{{ route(auth()->check() && is_in_admin(true) ? 'job-applications.download-cv' : 'public.account.applicants.download-cv', $jobApplication->id) }}"
                        target="_blank"
                        class="d-flex align-items-center gap-1"
                    >
                        {{ trans('plugins/job-board::job-application.tables.download_resume') }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                </x-core::datagrid.item>
            @endif

            @if ($jobApplication->cover_letter)
                    <x-core::datagrid.item>
                        <x-slot:title>
                            {{ trans('plugins/job-board::job-application.tables.cover_letter') }}
                        </x-slot:title>

                        <a href="{{ RvMedia::url($jobApplication->cover_letter) }}" target="_blank" class="d-flex align-items-center gap-1">
                            {{ RvMedia::url($jobApplication->cover_letter) }}
                            <x-core::icon name="ti ti-external-link" />
                        </a>
                    </x-core::datagrid.item>
            @endif
        @endif
    </x-core::datagrid>

    @if ($jobApplication->message)
        <x-core::datagrid.item class="mt-4">
            <x-slot:title>
                {{ trans('plugins/job-board::job-application.tables.message') }}
            </x-slot:title>

            {{ $jobApplication->message }}
        </x-core::datagrid.item>
    @endif

    @if ($jobApplication->application_answers)
        <div class="mt-4">
            <h5 class="mb-3">{{ __('Custom application answers') }}</h5>

            <x-core::datagrid>
                @foreach ($jobApplication->application_answers as $answer)
                    @php
                        $value = $answer['value'] ?? null;
                        $type = $answer['type'] ?? null;
                    @endphp
                    <x-core::datagrid.item>
                        <x-slot:title>
                            {{ $answer['label'] ?? __('Question') }}
                        </x-slot:title>

                        @if (is_array($value))
                            {{ implode(', ', $value) }}
                        @elseif (in_array($type, ['file_upload', 'cv_upload'], true) && $value)
                            <a href="{{ RvMedia::url($value) }}" target="_blank" class="d-flex align-items-center gap-1">
                                {{ basename((string) $value) }}
                                <x-core::icon name="ti ti-external-link" />
                            </a>
                        @else
                            {{ $value }}
                        @endif
                    </x-core::datagrid.item>
                @endforeach
            </x-core::datagrid>
        </div>
    @endif
@endif
