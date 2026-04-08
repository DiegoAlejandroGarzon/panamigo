<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-slate-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - Panamigo</title>

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <!-- Midone CSS -->
    @vite(['resources/css/app.css'])

    @stack('styles')

    <!-- Handle Alpine and Livewire -->
    @livewireStyles
</head>

<body class="p-0 m-0 bg-slate-100 antialiased overflow-x-hidden">
    {{ $slot }}

    <!-- Midone Core JS -->
    @vite(['resources/js/vendors/dom.js', 'resources/js/vendors/tailwind-merge.js', 'resources/js/vendors/lucide.js', 'resources/js/vendors/modal.js', 'resources/js/vendors/transition.js'])
    @vite(['resources/js/components/base/lucide.js', 'resources/js/components/base/theme-color.js'])

    @livewireScripts
    @stack('scripts')

    <script>
        // Ensure Lucide icons are initialized if they haven't been
        document.addEventListener('livewire:navigated', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>

</html>
