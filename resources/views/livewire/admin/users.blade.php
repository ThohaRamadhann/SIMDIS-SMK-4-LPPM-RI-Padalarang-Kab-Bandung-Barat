<div x-data>
    {{-- Notifikasi Sukses --}}
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- FORM INPUT (Kolom Kiri) --}}
        <div class="col-span-1">
            <div class="bg-white shadow rounded p-4">
                <h3 class="font-semibold mb-2">
                    {{ $editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                </h3>

                {{-- Gunakan form tag dengan wire:submit untuk Livewire --}}
                <form wire:submit.prevent="save" class="space-y-3">

                    <input type="text" wire:model.defer="name" placeholder="Nama"
                        class="w-full border rounded px-2 py-1" />
                    @error('name') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror

                    <input type="text" wire:model.defer="username" placeholder="Username"
                        class="w-full border rounded px-2 py-1" />
                    @error('username') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror

                    <input type="email" wire:model.defer="email" placeholder="Email (opsional)"
                        class="w-full border rounded px-2 py-1" />
                    @error('email') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror

                    <input type="text" wire:model.defer="no_telpon" placeholder="Nomor Telepon"
                        class="w-full border rounded px-2 py-1" />
                    @error('no_telpon') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror

                    <select wire:model.live="id_role" class="w-full border rounded px-2 py-1">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                        @endforeach
                    </select>
                    @error('id_role') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror

                    <input type="password" wire:model.defer="password"
                        placeholder="Password {{ $editingId ? '(kosongkan jika tidak diubah)' : '' }}"
                        class="w-full border rounded px-2 py-1" />
                    @error('password') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                    
                    
                    {{-- ➡️ FIELD DINAMIS UNTUK WALI KELAS / GURU BK --}}
                    {{-- Mengakses Computed Property dengan $this->selectedRoleName --}}
                    @if (in_array($this->selectedRoleName, ['guru_bk', 'wali_kelas']))
                        <div class="space-y-2 pt-2 border-t mt-2">
                            <h4 class="font-medium text-gray-700">Detail Guru/Wali Kelas</h4>
                            
                            <input type="text" wire:model.defer="nuptk" placeholder="NUPTK"
                                class="w-full border rounded px-2 py-1" />
                            @error('nuptk') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror

                            <input type="text" wire:model.defer="jabatan" placeholder="Jabatan (cth: Guru BK)"
                                class="w-full border rounded px-2 py-1" />
                            @error('jabatan') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                        </div>
                    @endif


                    {{-- ➡️ FIELD DINAMIS UNTUK WALI MURID (Orang Tua) --}}
                    @if ($this->selectedRoleName === 'orang_tua')
                        <div class="space-y-2 pt-2 border-t mt-2">
                            <h4 class="font-medium text-gray-700">Detail Wali Murid</h4>
                            
                            <input type="text" wire:model.defer="hubungan" placeholder="Hubungan dengan Siswa (cth: Ayah/Ibu)"
                                class="w-full border rounded px-2 py-1" />
                            @error('hubungan') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Simpan
                        </button>

                        <button type="button" wire:click.prevent="resetForm" class="px-3 py-1 rounded border hover:bg-gray-100">
                            Reset
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- TABLE USER (Kolom Kanan) --}}
        <div class="col-span-1 md:col-span-2">
            <div class="bg-white shadow rounded p-4">
                <h3 class="font-semibold mb-2">Daftar Pengguna</h3>

                <table class="w-full table-auto text-sm">
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
                                <td class="p-2">
                                    <button wire:click="editUser({{ $u->id_pengguna }})"
                                        class="text-blue-600 mr-2 hover:text-blue-800">Edit</button>

                                    <button wire:click="deleteUser({{ $u->id_pengguna }})"
                                        wire:confirm="Yakin ingin menghapus pengguna ini?"
                                        class="text-red-600 hover:text-red-800">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">Belum ada pengguna</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>