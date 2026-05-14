<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('admin.users', function ($user) {
    return optional($user->role)->nama_role === 'admin';
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Hanya user yang id_pengguna-nya cocok yang boleh subscribe
Broadcast::channel('notifikasi.{id_pengguna}', function ($user, $id_pengguna) {
    return (int) $user->id_pengguna === (int) $id_pengguna;
});