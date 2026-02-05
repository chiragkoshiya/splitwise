@props(['title' => 'Dashboard', 'back' => false])

<div class="app-bar">
    <div class="app-bar__left">
        @if($back)
            <a href="{{ $back }}" class="app-bar__back">←</a>
        @else
            <div style="font-size: 24px;">💸</div>
        @endif
    </div>
    <div class="app-bar__title">{{ $title }}</div>
    <div class="app-bar__right">
        {{ $slot }}
    </div>
</div>