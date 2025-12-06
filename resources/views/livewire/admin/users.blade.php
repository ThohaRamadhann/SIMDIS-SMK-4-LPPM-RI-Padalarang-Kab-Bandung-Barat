<div x-data>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- FORM INPUT --}}
        <div class="col-span-1">
            <div class="bg-white shadow rounded p-4">
                <h3 class="font-semibold mb-2">
                    {{ $editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                </h3>

                <div class="space-y-2">

                    <input type="text" wire:model.defer="name" placeholder="Nama"
                        class="w-full border rounded px-2 py-1" />

                    <input type="text" wire:model.defer="username" placeholder="Username"
                        class="w-full border rounded px-2 py-1" />

                    <input type="email" wire:model.defer="email" placeholder="Email (opsional)"
                        class="w-full border rounded px-2 py-1" />

                    <input type="text" wire:model.defer="no_telpon" placeholder="Nomor Telepon"
                        class="w-full border rounded px-2 py-1" />

                    <select wire:model.defer="id_role" class="w-full border rounded px-2 py-1">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                        @endforeach
                    </select>

                    <input type="password" wire:model.defer="password"
                        placeholder="Password (kosong = tidak diubah)"
                        class="w-full border rounded px-2 py-1" />

                    <div class="flex gap-2">
                        <button wire:click.prevent="save" class="bg-blue-600 text-white px-3 py-1 rounded">
                            Simpan
                        </button>

                        <button wire:click.prevent="resetForm" class="px-3 py-1 rounded border">
                            Reset
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE USER --}}
        <div class="col-span-1 md:col-span-2">
            <div class="bg-white shadow rounded p-4">
                <h3 class="font-semibold mb-2">Daftar Pengguna</h3>

                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">#</th>
                            <th class="p-2">Nama</th>
                            <th class="p-2">Username</th>
                            <th class="p-2">Role</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr class="border-b">
                                <td class="p-2">{{ $u->id_pengguna }}</td>
                                <td class="p-2">{{ $u->name }}</td>
                                <td class="p-2">{{ $u->username }}</td>
                                <td class="p-2">{{ optional($u->role)->nama_role }}</td>
                                <td class="p-2">
                                    <button wire:click="editUser({{ $u->id_pengguna }})"
                                        class="text-blue-600 mr-2">Edit</button>

                                    <button wire:click="deleteUser({{ $u->id_pengguna }})"
                                        class="text-red-600">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-2 text-center text-gray-500">Belum ada pengguna</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>
