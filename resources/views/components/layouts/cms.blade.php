<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset

    @isset($keywords)
        <meta name="keywords" content="{{ $keywords }}">
    @endisset

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMjQgMFY2NGMxMy45NTkgMCAyNCAxMC4wNDEgMCAyNHoiLz48L3N2Zz4=" type="image/svg+xml">

    <!-- Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite('resources/css/cms/cms.css')
    @stack('styles')
    @isset($colors)
        <style>
            :root { {{$colors}} }
        </style>
    @endisset
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body >
<x-cms.top-bar />
<x-cms.header />

    <!-- Page Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

<x-cms.footer />
</div>

<!-- Scripts -->
@vite('resources/js/cms/cms.js')
@stack('scripts')
</body>
</html>
