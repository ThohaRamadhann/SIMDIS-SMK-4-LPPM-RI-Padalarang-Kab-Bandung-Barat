<div x-data class="space-y-4">

    {{-- Notifikasi Sukses --}}
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ================= FORM INPUT ================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded p-4">
                <h3 class="font-semibold mb-4 text-base">
                    {{ $editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">

                    {{-- GRID INPUT --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <div>
                            <input type="text" wire:model.defer="name" placeholder="Nama"
                                class="w-full border rounded px-3 py-2 text-sm" />
                            @error('name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model.defer="username" placeholder="Username"
                                class="w-full border rounded px-3 py-2 text-sm" />
                            @error('username')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="email" wire:model.defer="email" placeholder="Email (opsional)"
                                class="w-full border rounded px-3 py-2 text-sm" />
                            @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model.defer="no_telpon" placeholder="Nomor Telepon"
                                class="w-full border rounded px-3 py-2 text-sm" />
                            @error('no_telpon')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <select wire:model.live="id_role" class="w-full border rounded px-3 py-2 text-sm">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                            @error('id_role')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <input type="password" wire:model.defer="password"
                                placeholder="Password {{ $editingId ? '(kosongkan jika tidak diubah)' : '' }}"
                                class="w-full border rounded px-3 py-2 text-sm" />
                            @error('password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- ================= DINAMIS GURU / WALI KELAS ================= --}}
                    @if (in_array($this->selectedRoleName, ['guru_bk', 'wali_kelas']))
                        <div class="border-t pt-4 space-y-3">
                            <h4 class="font-medium text-gray-700 text-sm">
                                Detail Guru / Wali Kelas
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <input type="text" wire:model.defer="nuptk" placeholder="NUPTK"
                                        class="w-full border rounded px-3 py-2 text-sm" />
                                    @error('nuptk')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <input type="text" wire:model.defer="jabatan" placeholder="Jabatan"
                                        class="w-full border rounded px-3 py-2 text-sm" />
                                    @error('jabatan')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ================= DINAMIS ORANG TUA ================= --}}
                    @if ($this->selectedRoleName === 'orang_tua')
                        <div class="border-t pt-4 space-y-3">
                            <h4 class="font-medium text-gray-700 text-sm">
                                Detail Wali Murid
                            </h4>

                            <input type="text" wire:model.defer="hubungan"
                                placeholder="Hubungan dengan Siswa (Ayah / Ibu)"
                                class="w-full border rounded px-3 py-2 text-sm" />
                            @error('hubungan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- ================= BUTTON ================= --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-3">

                        @if ($editingId)
                            {{-- MODE EDIT --}}
                            <button type="submit"
                                class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-600">
                                Update
                            </button>

                            <button type="button" wire:click.prevent="resetForm"
                                class="w-full sm:w-auto border px-4 py-2 rounded text-sm hover:bg-gray-100">
                                Batal
                            </button>
                        @else
                            {{-- MODE TAMBAH --}}
                            <button type="submit"
                                class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                Simpan
                            </button>

                            <button type="button" wire:click.prevent="resetForm"
                                class="w-full sm:w-auto border px-4 py-2 rounded text-sm hover:bg-gray-100">
                                Reset
                            </button>
                        @endif

                    </div>

                </form>
            </div>
        </div>

        {{-- ================= TABLE USER ================= --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded p-4">
                <h3 class="font-semibold mb-3 text-base">Daftar Pengguna</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">#</th>
                                <th class="p-2 text-left">Nama</th>
                                <th class="p-2 text-left">Username</th>
                                <th class="p-2 text-left">Role</th>
                                <th class="p-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $u->id_pengguna }}</td>
                                    <td class="p-2">{{ $u->name }}</td>
                                    <td class="p-2">{{ $u->username }}</td>
                                    <td class="p-2 capitalize">{{ optional($u->role)->nama_role }}</td>
                                    <td class="p-2 whitespace-nowrap">
                                        <button wire:click="editUser({{ $u->id_pengguna }})"
                                            class="text-blue-600 hover:underline mr-2">
                                            Edit
                                        </button>
                                        <button wire:click="deleteUser({{ $u->id_pengguna }})"
                                            wire:confirm="Yakin ingin menghapus pengguna ini?"
                                            class="text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">
                                        Belum ada pengguna
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
