<div class="space-y-6">
    
    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- FORM --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow p-6 rounded-lg border">

                <h2 class="font-bold text-lg mb-4">
                    {{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}
                </h2>

                <div class="space-y-4">

                    <div>
                        <label class="font-semibold block mb-1">Nama Siswa *</label>
                        <input type="text" wire:model.defer="nama"
                            class="w-full border px-3 py-2 rounded">
                        @error('nama')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">NIS *</label>
                        <input type="text" wire:model.defer="nis"
                            class="w-full border px-3 py-2 rounded">
                        @error('nis')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Kelas *</label>
                        <select wire:model.defer="id_kelas"
                            class="w-full border px-3 py-2 rounded">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id_kelas }}">
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kelas')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Wali Murid *</label>
                        <select wire:model.defer="id_walimurid"
                            class="w-full border px-3 py-2 rounded">
                            <option value="">-- Pilih Wali Murid --</option>
                            @foreach($wali as $w)
                                <option value="{{ $w->id_walimurid }}">
                                    {{ optional($w->pengguna)->name ?? '-' }}
                                    {{ $w->hubungan ? '(' . $w->hubungan . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_walimurid')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Status</label>
                        <select wire:model.defer="status"
                            class="w-full border px-3 py-2 rounded">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex gap-3 pt-4">
                        @if ($isEdit)
                            <button wire:click="update"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Update
                            </button>
                            <button wire:click="resetForm"
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Simpan
                            </button>
                        @endif
                    </div>

                    {{-- CATATAN --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded border text-sm text-gray-600">
                        <ul class="space-y-1">
                            <li>• Pastikan wali murid sudah terdaftar</li>
                            <li>• NIS harus unik</li>
                            <li>• Status nonaktif menonaktifkan siswa</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="lg:col-span-3">
            <div class="bg-white shadow rounded-lg border p-4">

                <h3 class="font-bold text-lg mb-4">Daftar Siswa</h3>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-[900px] w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Nama</th>
                                <th class="border px-3 py-2">NIS</th>
                                <th class="border px-3 py-2">Kelas</th>
                                <th class="border px-3 py-2">Wali Murid</th>
                                <th class="border px-3 py-2">Status</th>
                                <th class="border px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($dataSiswa as $s)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2 font-medium">
                                        {{ $s->nama }}
                                    </td>
                                    <td class="border px-3 py-2 text-center">
                                        {{ $s->nis }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        {{ optional($s->kelas)->nama_kelas ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        {{ optional(optional($s->waliMurid)->pengguna)->name ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2 capitalize">
                                        {{ $s->status }}
                                    </td>
                                    <td class="border px-3 py-2 text-center whitespace-nowrap">
                                        <button wire:click="edit({{ $s->id_siswa }})"
                                            class="text-blue-600 hover:underline mr-2">
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $s->id_siswa }})"
                                            onclick="return confirm('Hapus siswa?')"
                                            class="text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-gray-500 mt-2 lg:hidden">
                    Geser tabel ke samping →
                </p>

            </div>
        </div>

    </div>
</div>
