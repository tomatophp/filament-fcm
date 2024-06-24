<div>

</div>
<script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js'
    import {getMessaging, onMessage, getToken} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "{{ config('filament-fcm.project.apiKey') }}",
        authDomain: "{{ config('filament-fcm.project.authDomain') }}",
        databaseURL: "{{ config('filament-fcm.project.databaseURL') }}",
        projectId: "{{ config('filament-fcm.project.projectId') }}",
        storageBucket: "{{ config('filament-fcm.project.storageBucket') }}",
        messagingSenderId: "{{ config('filament-fcm.project.messagingSenderId') }}",
        appId: "{{ config('filament-fcm.project.appId') }}",
        measurementId: "{{ config('filament-fcm.project.measurementId') }}",
    };
    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker
                    .register("/firebase-messaging-sw.js");
            }
            navigator.serviceWorker.getRegistration().then(async (reg) => {
                let token = await getToken(messaging, {vapidKey: "{{ config('filament-fcm.vapid') }}"});
                console.log(token);
                Livewire.dispatch('fcm-token', { token: token });


                onMessage(messaging, (payload) => {
                    console.log(payload);
                    Livewire.dispatch('fcm-notification', {data: payload})
                    // push notification can send event.data.json() as well
                    const options = {
                        body: payload.data.body,
                        icon: payload.data.image,
                        tag: "alert",
                    };
                    let notification = reg.showNotification(
                        payload.data.title,
                        options
                    );
                    // link to page on clicking the notification
                    notification.onclick = (payload) => {
                        window.open(payload.data.url);
                    };
                });
            });
        }

    });

</script>
