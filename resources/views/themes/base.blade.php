<!DOCTYPE html>
<!--
Template Name: Midone - Admin Dashboard Template
Author: Left4code
Website: http://www.left4code.com/
Contact: muhammadrizki@left4code.com
Purchase: https://themeforest.net/user/left4code/portfolio
Renew Support: https://themeforest.net/user/left4code/portfolio
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html
    class="opacity-0"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="description"
        content="Midone admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities."
    >
    <meta
        name="keywords"
        content="admin template, midone Admin Template, dashboard template, flat admin template, responsive admin template, web app"
    >
    <meta
        name="author"
        content="LEFT4CODE"
    >

    @yield('head')

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#15803d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Panamigo">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <!-- BEGIN: CSS Assets-->
    @stack('styles')
   
   
    <!-- END: CSS Assets-->

    @vite('resources/css/app.css')

 
</head>
<!-- END: Head -->

<body>
    <x-theme-switcher />

    @yield('content')

    <!-- BEGIN: Vendor JS Assets-->
    @vite('resources/js/vendors/dom.js')
    @vite('resources/js/vendors/tailwind-merge.js')
    @stack('vendors')
    <!-- END: Vendor JS Assets-->

    <!-- BEGIN: Pages, layouts, components JS Assets-->
    @vite('resources/js/components/base/theme-color.js')
    @stack('scripts')
    <!-- END: Pages, layouts, components JS Assets-->

    <!-- PWA Install Banner -->
    <div id="pwa-install-banner" style="display:none; position:fixed; bottom:16px; left:50%; transform:translateX(-50%); background:#1e40af; color:#fff; padding:12px 20px; border-radius:12px; z-index:9999; box-shadow:0 4px 20px rgba(0,0,0,0.3); align-items:center; gap:12px; font-family:sans-serif; font-size:14px; max-width:90vw;">
        <img src="/icons/icon-192x192.png" style="width:36px;height:36px;border-radius:8px;" alt="">
        <span>Instala <strong>Panamigo</strong> en tu dispositivo</span>
        <button id="pwa-install-btn" style="background:#fff; color:#1e40af; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:bold; white-space:nowrap;">Instalar</button>
        <button id="pwa-dismiss-btn" style="background:transparent; color:#fff; border:none; cursor:pointer; font-size:18px; line-height:1; padding:0 4px;">&times;</button>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(function(reg) { console.log('SW registrado:', reg.scope); })
                    .catch(function(err) { console.error('SW error:', err); });
            });
        }

        let deferredPrompt;
        const banner = document.getElementById('pwa-install-banner');

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            banner.style.display = 'flex';
        });

        document.getElementById('pwa-install-btn').addEventListener('click', function() {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function() {
                deferredPrompt = null;
                banner.style.display = 'none';
            });
        });

        document.getElementById('pwa-dismiss-btn').addEventListener('click', function() {
            banner.style.display = 'none';
        });

        window.addEventListener('appinstalled', function() {
            banner.style.display = 'none';
            deferredPrompt = null;
        });
    </script>
</body>

</html>
