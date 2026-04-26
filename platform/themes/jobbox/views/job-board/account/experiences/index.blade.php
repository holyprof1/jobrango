@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div class="jobrango-panel">
        <div class="jobrango-panel__header">
            <div>
                <h3>{{ __('Experiences') }}</h3>
                <p>{{ __('Highlight the work history that proves you can deliver in the roles you want next.') }}</p>
            </div>
            <a href="{{ route('public.account.experiences.create') }}" class="btn btn-default btn-brand icon-tick">{{ __('Add Experience') }}</a>
        </div>
        @if ($experiences->isNotEmpty())
            <div class="box-timeline mt-20">
                @foreach($experiences as $experience)
                    <div class="item-timeline">
                        <div class="timeline-year">
                            <span>{{ $experience->started_at->format('Y') }} - {{ $experience->ended_at ? $experience->ended_at->format('Y') : __('Now') }}</span>
                        </div>
                        <div class="timeline-info">
                            <h5 class="color-brand-1 mb-20">
                                {{ $experience->company }}
                                @if($experience->position)
                                    <span class="ml-5 text-muted">({{ $experience->position }})</span>
                                @endif
                            </h5>
                            <p class="color-text-paragraph-2 mb-15">{!! BaseHelper::clean($experience->description) !!}</p>
                        </div>
                        <div class="timeline-actions">
                            <a href="{{ route('public.account.experiences.edit', $experience->id) }}" class="btn btn-editor"></a>
                            <form method="post" action="{{ route('public.account.experiences.destroy', $experience->id) }}">
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
                <h4>{{ __('No experience entries yet') }}</h4>
                <p>{{ __('Add past roles, internships, or freelance work to strengthen your profile and improve employer trust.') }}</p>
            </div>
        @endif
    </div>
@endsection
