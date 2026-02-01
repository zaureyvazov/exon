const CACHE_NAME = 'exon-v2';
const urlsToCache = [
  '/',
  '/offline',
  '/css/app.css',
  '/js/app.js',
  '/images/exon.webp',
  '/images/icon-192.png',
  '/images/icon-512.png',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
];

// Install - İlk quraşdırma zamanı faylları cache-lə
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache açıldı');
        return cache.addAll(urlsToCache).catch(err => {
          console.warn('Bəzi fayllar cache edilə bilmədi:', err);
        });
      })
  );
  self.skipWaiting();
});

// Fetch - Network request-lərə müdaxilə (Network First with timeout, fallback to cache)
self.addEventListener('fetch', (event) => {
  // Skip cross-origin requests
  if (!event.request.url.startsWith(self.location.origin) && 
      !event.request.url.includes('cdn.jsdelivr.net') &&
      !event.request.url.includes('bootstrap')) {
    return;
  }

  event.respondWith(
    // Network First with 5 second timeout
    Promise.race([
      fetch(event.request).then(response => {
        // Yalnız GET request-ləri və valid response-ları cache-lə
        if (event.request.method === 'GET' && 
            response && 
            response.status === 200 && 
            !event.request.url.includes('/api/') && 
            !event.request.url.includes('/logout')) {
          
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
          });
        }
        return response;
      }),
      // 5 saniyə timeout - əgər network cavab verməzsə cache-dən gətir
      new Promise((_, reject) => 
        setTimeout(() => reject(new Error('timeout')), 5000)
      )
    ]).catch(() => {
      // Network failed or timeout - cache-dən gətir
      return caches.match(event.request).then(cached => {
        if (cached) {
          return cached;
        }
        
        // Cache-də də yoxdursa, offline səhifə göstər
        if (event.request.mode === 'navigate') {
          return caches.match('/offline');
        }
        
        // Digər resource-lar üçün error
        return new Response('Offline', {
          status: 503,
          statusText: 'Service Unavailable',
          headers: new Headers({
            'Content-Type': 'text/plain'
          })
        });
      });
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
