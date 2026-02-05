<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="Split expenses with friends. Track shared costs and settle up easily.">
    <meta name="theme-color" content="#6366F1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'SplitEasy') }}</title>

    {{-- Vite Assets --}}
    @vite(['resources/css/theme.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    <style>
        .offline-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }

        .offline-modal {
            background: white;
            padding: 32px;
            border-radius: 20px;
            max-width: 85%;
            width: 380px;
            text-align: center;
            box-shadow: var(--shadow-xl);
        }

        .is-offline button[type="submit"],
        .is-offline .btn--primary,
        .is-offline .fab {
            pointer-events: none;
            opacity: 0.6;
            filter: grayscale(0.5);
        }
    </style>
</head>

<body x-data="{ isOffline: !Connectivity.isOnline }" @connectivity-changed.window="isOffline = !$event.detail.online">

    <!-- Offline Modal -->
    <template x-if="isOffline">
        <div class="offline-modal-overlay">
            <div class="offline-modal">
                <div style="font-size: 64px; margin-bottom: 24px;">📡</div>
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; color: var(--color-text);">No
                    Internet Connection</h2>
                <p style="color: var(--color-text-secondary); margin-bottom: 24px; font-size: 14px;">
                    You're offline. Some actions are paused. Reconnect to sync data.
                </p>
                <div class="skeleton" style="height: 4px; border-radius: 2px; background: var(--color-border);"></div>
                <p
                    style="font-size: 11px; color: var(--color-text-tertiary); margin-top: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Searching for network...</p>
            </div>
        </div>
    </template>

    <div class="page page-with-appbar" :class="{ 'is-offline': isOffline }">
        {{-- App Bar --}}
        <x-app-bar :title="$title ?? 'Dashboard'" :back="$back ?? false">
            {{ $appBarActions ?? '' }}
        </x-app-bar>

        {{-- Page Content --}}
        <div class="page-content">
            {{ $slot }}
        </div>

        {{-- FAB (if needed) --}}
        @isset($fab)
            {{ $fab }}
        @endisset

        {{-- Bottom Navigation --}}
        <x-bottom-nav :active="$active ?? 'dashboard'" />
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>

</html>