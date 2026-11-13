// Service Worker — ICB CT Presensi Guru
const CACHE_NAME = 'icbct-v1';

// Asset yang di-cache untuk offline
const PRECACHE = [
    '/',
    '/favicon/android-chrome-192x192.png',
    '/favicon/android-chrome-512x512.png',
    '/images/og-banner.png',
    '/images/logo.png',
];

self.addEventListener('install', function (e) {
    self.skipWaiting();
    e.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(PRECACHE).catch(function () {});
        })
    );
});

self.addEventListener('activate', function (e) {
    e.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (k) { return k !== CACHE_NAME; })
                    .map(function (k) { return caches.delete(k); })
            );
        }).then(function () { return self.clients.claim(); })
    );
});

// Network-first: coba network, fallback ke cache
self.addEventListener('fetch', function (e) {
    // Hanya handle GET dan URL same-origin
    if (e.request.method !== 'GET') return;
    var url = new URL(e.request.url);
    if (url.origin !== self.location.origin) return;

    // Jangan cache API calls
    if (url.pathname.startsWith('/teacher/attendance/') ||
        url.pathname.startsWith('/api/') ||
        url.pathname.includes('poll-status')) return;

    e.respondWith(
        fetch(e.request)
            .then(function (resp) {
                // Cache static assets
                if (resp.ok && (
                    url.pathname.startsWith('/images/') ||
                    url.pathname.startsWith('/favicon/') ||
                    url.pathname.startsWith('/build/')
                )) {
                    var clone = resp.clone();
                    caches.open(CACHE_NAME).then(function (c) { c.put(e.request, clone); });
                }
                return resp;
            })
            .catch(function () {
                return caches.match(e.request);
            })
    );
});
