@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div class="jobrango-panel">
        <div class="jobrango-panel__header">
            <div>
                <h3>{{ __('Educations') }}</h3>
                <p>{{ __('Show employers the schools, certifications, and focused training behind your profile.') }}</p>
            </div>
            <a href="{{ route('public.account.educations.create') }}" class="btn btn-default btn-brand icon-tick">{{ __('Add Education') }}</a>
        </div>
        @if ($educations->isNotEmpty())
            <div class="box-timeline mt-20">
                @foreach($educations as $education)
                    <div class="item-timeline">
                        <div class="timeline-year">
                            <span>{{ $education->started_at->format('Y') }} - {{ $education->ended_at ? $education->ended_at->format('Y') : __('Now') }}</span>
                        </div>
                        <div class="timeline-info">
                            <h5 class="color-brand-1">
                                {{ $education->school }}
                                @if ($education->specialized)
                                    <span class="ml-5 text-muted">({{ $education->specialized }})</span>
                                @endif
                            </h5>
                            <p class="color-text-paragraph-2 mb-15">{!! BaseHelper::clean($education->description) !!}</p>
                        </div>
                        <div class="timeline-actions">
                            <a href="{{ route('public.account.educations.edit', $education->id) }}" class="btn btn-editor"></a>
                            <form method="post" action="{{ route('public.account.educations.destroy', $education->id) }}">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('{{ __('Are you sure you want to delete this item?') }}');" class="btn btn-remove" type="submit"></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="jobrango-empty-state">
                <h4>{{ __('No education entries yet') }}</h4>
                <p>{{ __('Add your schools, certifications, or programs so employers can quickly assess your background.') }}</p>
            </div>
        @endif
    </div>
@endsection
