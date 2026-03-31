@props(['items' => []])

@if(count($items) > 0)
    <div style="margin-bottom: 20px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
        @foreach($items as $index => $item)
            @if($index > 0)
                <span style="color: #cbd5e0; font-size: 14px;">›</span>
            @endif
            
            @if(isset($item['url']) && $item['url'])
                <a href="{{ $item['url'] }}" style="color: #667eea; text-decoration: none; font-size: 14px; transition: color 0.2s;">
                    {{ $item['name'] }}
                </a>
            @else
                <span style="color: #2d3748; font-weight: 500; font-size: 14px;">
                    {{ $item['name'] }}
                </span>
            @endif
        @endforeach
    </div>
@endif