const CACHE_NAME = 'panamigo-v1';

// Al instalar, podemos precachear la página de inicio
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(['/']);
        })
    );
});

// El evento fetch es obligatorio para que Chrome muestre el botón de instalar
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
