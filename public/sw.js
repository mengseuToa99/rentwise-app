/*
 * RentWise service worker.
 * Strategy is tuned for a Laravel + Livewire multi-page app:
 *   - Dynamic/stateful requests (POST, Livewire, broadcasting) ALWAYS hit the network.
 *   - HTML navigations use network-first so pages are never served stale; offline -> offline.html.
 *   - Static build assets / fonts / images use stale-while-revalidate (fast + self-updating).
 * Bump CACHE_VERSION to force clients onto a new cache.
 */
const CACHE_VERSION = "v2";
const STATIC_CACHE = `rentwise-static-${CACHE_VERSION}`;
const PAGE_CACHE = `rentwise-pages-${CACHE_VERSION}`;
const OFFLINE_URL = "/offline.html";

const PRECACHE = [OFFLINE_URL, "/manifest.webmanifest", "/icons/icon-192.png", "/icons/icon-512.png"];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => ![STATIC_CACHE, PAGE_CACHE].includes(key))
                        .map((key) => caches.delete(key))
                )
            )
            .then(() => self.clients.claim())
    );
});

// Let the page tell a waiting SW to take over immediately.
self.addEventListener("message", (event) => {
    if (event.data === "SKIP_WAITING") self.skipWaiting();
});

const isStaticAsset = (url) =>
    url.pathname.startsWith("/build/") ||
    url.pathname.startsWith("/icons/") ||
    /\.(?:css|js|png|jpg|jpeg|gif|svg|webp|ico|woff2?|ttf|eot)$/i.test(url.pathname);

self.addEventListener("fetch", (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin GET. Everything else (POST, Livewire updates,
    // broadcasting, cross-origin, etc.) goes straight to the network untouched.
    if (request.method !== "GET" || url.origin !== self.location.origin) return;

    // Never intercept Livewire or broadcasting traffic.
    if (url.pathname.startsWith("/livewire/") || url.pathname.startsWith("/broadcasting/")) return;

    // HTML navigations: network-first, fall back to cache, then the offline page.
    if (request.mode === "navigate") {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open(PAGE_CACHE).then((cache) => cache.put(request, copy));
                    return response;
                })
                .catch(() =>
                    caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL))
                )
        );
        return;
    }

    // Static assets: stale-while-revalidate.
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.open(STATIC_CACHE).then((cache) =>
                cache.match(request).then((cached) => {
                    const network = fetch(request)
                        .then((response) => {
                            if (response && response.status === 200) cache.put(request, response.clone());
                            return response;
                        })
                        .catch(() => cached);
                    return cached || network;
                })
            )
        );
    }
});
