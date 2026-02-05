<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#6366F1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Welcome' }} - {{ config('app.name', 'SplitEasy') }}</title>
    
    @vite(['resources/css/theme.css', 'resources/js/app.js'])
</head>
<body>
    <div class="page">
        {{ $slot }}
    </div>
</body>
</html>
