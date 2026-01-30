const CACHE_NAME = 'exon-v1';
const urlsToCache = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/images/exon.webp',
];

// Install - İlk quraşdırma zamanı faylları cache-lə
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache açıldı');
        return cache.addAll(urlsToCache);
      })
  );
  self.skipWaiting();
});

// Fetch - Network request-lərə müdaxilə (Cache First strategiyası)
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache-də varsa ver, yoxdursa network-dən çək
        if (response) {
          return response;
        }
        return fetch(event.request).then(
          (response) => {
            // Yalnız GET request-ləri və valid response-ları cache-lə
            if (!response || response.status !== 200 || response.type === 'error') {
              return response;
            }

            // API və dinamik məlumatları cache-ləmə
            if (event.request.url.includes('/api/') || 
                event.request.url.includes('/logout') ||
                event.request.method !== 'GET') {
              return response;
            }

            const responseToCache = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        );
      })
      .catch(() => {
        // Offline səhifə göstər (istəyə bağlı)
        return caches.match('/');
      })
  );
});

// Activate - Köhnə cache-ləri sil
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('Köhnə cache silindi:', cache);
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Push notification (gələcəkdə istifadə üçün)
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'Yeni bildiriş',
    icon: '/images/icon-192.png',
    badge: '/images/icon-192.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  };

  event.waitUntil(
    self.registration.showNotification('EXON Klinika', options)
  );
});
