import './bootstrap';

const beamsInstanceId = document.querySelector('meta[name="beams-instance-id"]')?.content;
const userId = document.querySelector('meta[name="user-id"]')?.content;

if (beamsInstanceId && 'serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(function (swReg) {
            console.log('[SW] Registered:', swReg.scope);

            const beamsClient = new PusherPushNotifications.Client({
                instanceId: beamsInstanceId,
            });

            window.beamsClient = beamsClient;

            beamsClient.start()
                .then(() => beamsClient.addDeviceInterest('global-notifications'))
                .then(() => {
                    if (userId) {
                        return beamsClient.addDeviceInterest('user-' + userId);
                    }
                })
                .then(() => console.log('[Beams] Interests set'))
                .catch(err => console.error('[Beams] Error:', err));
        })
        .catch(err => console.error('[SW] Registration failed:', err));
}

// Interest user TIDAK dihapus saat logout
// agar notif tetap masuk ke device meski sudah logout

window.Echo.private('admin.users')
    .listen('.UserChanged', (e) => {
        console.log('UserChanged:', e);
        if (window.Livewire) {
            Livewire.dispatch('refreshUsers');
        }
    });
