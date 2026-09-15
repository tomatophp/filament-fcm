<div wire:ignore>
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js';
        import { getMessaging, onMessage, getToken } from 'https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js';

        const app = initializeApp(@js($firebaseConfig));
        const messaging = getMessaging(app);

        if ('Notification' in window && 'serviceWorker' in navigator) {
            Notification.requestPermission().then(async (permission) => {
                if (permission !== 'granted') {
                    return;
                }

                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                const token = await getToken(messaging, {
                    vapidKey: @js($vapid),
                    serviceWorkerRegistration: registration,
                });

                if (token) {
                    Livewire.dispatch('fcm-token', { token: token });
                }

                onMessage(messaging, (payload) => {
                    Livewire.dispatch('fcm-notification', { data: payload });

                    const data = payload.data ?? {};

                    registration.showNotification(data.title ?? payload.notification?.title ?? '', {
                        body: data.body ?? payload.notification?.body ?? '',
                        icon: data.image ?? undefined,
                        tag: 'alert',
                        data: { url: data.url ?? null },
                    });
                });
            });
        }
    </script>
</div>
