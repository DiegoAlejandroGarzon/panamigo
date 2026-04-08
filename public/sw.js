// Service Worker básico para habilitar el botón de instalación de PWA
self.addEventListener('install', (e) => {
    console.log('[Service Worker] Install');
});

self.addEventListener('fetch', (e) => {
    // Necesario para que Chrome lo considere PWA, aunque no cacheemos nada por ahora
    e.respondWith(fetch(e.request));
});
