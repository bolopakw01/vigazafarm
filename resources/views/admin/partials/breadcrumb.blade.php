@php
    $itemsCollection = collect($items ?? []);
    $totalItems = $itemsCollection->count();
@endphp

<div class="bolopa-breadcrumb-pill" role="navigation" aria-label="Breadcrumb trail">
    @foreach($itemsCollection as $index => $item)
        @php
            $isActive = $index === $totalItems - 1;
            $isStandaloneBackoffice = $totalItems === 1 && ($item['label'] ?? '') === 'Backoffice';
        @endphp
        <div class="bolopa-breadcrumb-node {{ $isActive ? 'is-active' : '' }}">
            @if(!empty($item['icon']))
                @php
                    $iconData = $item['icon'];
                    $iconType = is_array($iconData) ? ($iconData['type'] ?? 'text') : 'text';
                    $iconValue = is_array($iconData) ? ($iconData['value'] ?? $iconData['src'] ?? null) : $iconData;
                    $iconAlt = is_array($iconData) ? ($iconData['alt'] ?? $item['label']) : $item['label'];
                @endphp
                <span class="bolopa-breadcrumb-node-icon {{ $iconType === 'image' ? 'is-image' : 'is-symbol' }}">
                    @if($iconType === 'image' && !empty($iconValue))
                        <img src="{{ $iconValue }}" alt="{{ $iconAlt }}" width="16" height="16" loading="lazy">
                    @elseif(!empty($iconValue))
                        {{ $iconValue }}
                    @endif
                </span>
            @endif

            @if(!$isActive && !empty($item['link']))
                <a href="{{ $item['link'] }}" class="bolopa-breadcrumb-node-link">
                    <span class="bolopa-breadcrumb-label">{{ $item['label'] }}</span>
                </a>
            @else
                <span class="bolopa-breadcrumb-node-link is-active{{ $isStandaloneBackoffice ? ' is-standalone' : '' }}" aria-current="page">
                    <span class="bolopa-breadcrumb-label">{{ $item['label'] }}</span>
                </span>
            @endif

            @if(!empty($item['badge']))
                <span class="bolopa-breadcrumb-node-badge">{{ $item['badge'] }}</span>
            @endif
        </div>

        @if($index < $totalItems - 1)
            <span class="bolopa-breadcrumb-arrow" aria-hidden="true">›</span>
        @endif
    @endforeach
</div>
