<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
            @if(session('success') || session('error'))
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                @if(session('success'))
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
            @endif
            {{ $slot }}
        </main>

        @include('layouts.partials.footer')
    </body>
</html>
