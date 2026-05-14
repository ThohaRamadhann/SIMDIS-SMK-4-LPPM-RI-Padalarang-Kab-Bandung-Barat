import './bootstrap';

// Realtime admin users
window.Echo.private('admin.users')
    .listen('.UserChanged', (e) => {
        console.log('UserChanged:', e);

        // Reload Livewire component
        if (window.Livewire) {
            Livewire.dispatch('refreshUsers');
        }
    });