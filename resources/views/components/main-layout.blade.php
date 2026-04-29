<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">

        <title>{{ $seoMeta['title'] ?? config('app.name', 'WATCHMARKET') }}</title>
        <meta name="description" content="{{ $seoMeta['description'] ?? config('app.name', 'WATCHMARKET') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">
        @include('layouts.partials.header')

        <main class="flex-grow">
            {{ $slot }}
        </main>

        @include('layouts.partials.footer')
    </body>
</html>
