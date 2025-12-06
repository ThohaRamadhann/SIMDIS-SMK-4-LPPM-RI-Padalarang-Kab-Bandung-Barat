import './bootstrap'; // pastikan Echo di bootstrap.js
import { listen } from 'laravel-echo';

window.Echo.private('admin.users')
    .listen('.UserChanged', (e) => {
        // contoh: emit event Livewire global supaya komponen Livewire reload
        window.livewire.emit('refreshUsers');
    });
