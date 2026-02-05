<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="Split expenses with friends. Track shared costs and settle up easily.">
    <meta name="theme-color" content="#6366F1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'SplitEasy') }}</title>
    
    {{-- Vite Assets --}}
    @vite(['resources/css/theme.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body>
    <div class="page page-with-appbar">
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
