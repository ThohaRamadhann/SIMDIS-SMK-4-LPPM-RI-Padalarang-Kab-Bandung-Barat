importScripts("https://js.pusher.com/beams/service-worker.js");

self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) return;

    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'SIMDIS', body: event.data ? event.data.text() : '' };
    }

    const title = data.title || 'SIMDIS';
    const options = {
        body: data.body || 'Ada notifikasi baru',
        icon: '/images/logo_simdis.png',
        badge: '/images/logo_simdis.png',
        vibrate: [200, 100, 200],
        requireInteraction: false,
        data: { url: data.url || '/dashboard' }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            const url = event.notification.data?.url || '/dashboard';
            for (let client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
