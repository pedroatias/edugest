// EduGest Service Worker - Push Notifications
const CACHE_NAME = 'edugest-v1';

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(clients.claim());
});

self.addEventListener('push', event => {
    if (!event.data) return;
    let data = {};
    try { data = event.data.json(); } catch(e) { data = { title: 'EduGest', body: event.data.text() }; }

    const options = {
        body:    data.body || '',
        icon:    data.icon || '/assets/img/icon-192.png',
        badge:   data.badge || '/assets/img/badge-72.png',
        tag:     data.tag || 'edugest',
        data:    { url: data.url || '/' },
        vibrate: [200, 100, 200],
        actions: [{ action: 'open', title: 'Ver', icon: '/assets/img/icon-check.png' }]
    };

    event.waitUntil(self.registration.showNotification(data.title || 'EduGest', options));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) return client.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});