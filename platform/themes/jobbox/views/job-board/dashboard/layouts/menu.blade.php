<ul class="menu jobrango-employer-menu">
    @foreach (DashboardMenu::getAll('account') as $item)
        @continue(! $item['name'])
        <li>
            <a
                href="{{ $item['url'] }}"
                @class(['active' => $item['active']])
            >
                <span class="jobrango-employer-menu__icon">
                    <x-core::icon :name="$item['icon']" />
                </span>
                <span>{{ trans($item['name']) }}</span>
            </a>
        </li>
    @endforeach
</ul>
