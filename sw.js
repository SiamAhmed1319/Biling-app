self.addEventListener('install', (e) => {
  console.log('Service Worker Installed');
});

self.addEventListener('fetch', (e) => {
  // Ekhane caching logic thake, ekhonoto dorkar nai
});