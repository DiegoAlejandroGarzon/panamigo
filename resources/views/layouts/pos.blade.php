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
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Panamigo">
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
        // Ensure Lucide icons are initialized
        document.addEventListener('livewire:navigated', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(reg => console.log('SW Registered', reg))
                    .catch(err => console.error('SW Error', err));
            });
        }

        let deferredPrompt;
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                banner.style.display = 'flex';
            });
            document.getElementById('pwa-install-btn')?.addEventListener('click', () => {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(() => { deferredPrompt = null; banner.style.display = 'none'; });
            });
            document.getElementById('pwa-dismiss-btn')?.addEventListener('click', () => { banner.style.display = 'none'; });
        }
    </script>

    <!-- PWA Install Banner -->
    <div id="pwa-install-banner" style="display:none; position:fixed; bottom:16px; left:50%; transform:translateX(-50%); background:#1e40af; color:#fff; padding:12px 20px; border-radius:12px; z-index:9999; box-shadow:0 4px 20px rgba(0,0,0,0.3); align-items:center; gap:12px; font-family:sans-serif; font-size:14px; max-width:90vw;">
        <img src="/icons/icon-192x192.png" style="width:36px;height:36px;border-radius:8px;" alt="">
        <span>Instala <strong>Panamigo</strong> en tu dispositivo</span>
        <button id="pwa-install-btn" style="background:#fff; color:#1e40af; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:bold; white-space:nowrap;">Instalar</button>
        <button id="pwa-dismiss-btn" style="background:transparent; color:#fff; border:none; cursor:pointer; font-size:18px; line-height:1; padding:0 4px;">&times;</button>
    </div>
</body>

</html>
