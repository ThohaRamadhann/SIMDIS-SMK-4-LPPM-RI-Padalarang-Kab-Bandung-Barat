<div class="p-6">
    
    {{-- SEARCH --}}
    <div class="mb-4 flex justify-between">
        <input type="text"
               wire:model.live="search"
               placeholder="Cari siswa..."
               class="border px-3 py-2 rounded w-1/3">

        <button wire:click="resetForm"
                class="bg-green-600 text-white px-4 py-2 rounded">
            + Tambah Siswa
        </button>
    </div>

    {{-- FORM TAMBAH / EDIT --}}
    <div class="bg-white shadow p-4 mb-6 rounded border">

        <h2 class="font-bold text-lg mb-2">
            {{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}
        </h2>

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label class="font-semibold">Nama Siswa *</label>
                <input type="text" wire:model="nama" class="w-full border px-3 py-2 rounded" placeholder="Nama lengkap siswa">
                @error('nama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-semibold">NIS *</label>
                <input type="text" wire:model="nis" class="w-full border px-3 py-2 rounded" placeholder="Nomor Induk Siswa">
                @error('nis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-semibold">Kelas *</label>
                <select wire:model="id_kelas" class="w-full border px-3 py-2 rounded">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                @error('id_kelas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-semibold">Wali Murid *</label>
                <select wire:model="id_walimurid" class="w-full border px-3 py-2 rounded">
                    <option value="">-- Pilih Wali Murid --</option>
                    @foreach($wali as $w)
                        <option value="{{ $w->id_walimurid }}">
                            @if($w->pengguna)
                                {{ $w->pengguna->name }}
                                @if($w->hubungan)
                                    ({{ $w->hubungan }})
                                @endif
                            @else
                                [Data tidak lengkap]
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('id_walimurid') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-semibold">Status</label>
                <select wire:model="status" class="w-full border px-3 py-2 rounded">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

        </div>

        <div class="mt-4">
            @if ($isEdit)
                <button wire:click="update" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>
                <button wire:click="resetForm" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                    Batal
                </button>
            @else
                <button wire:click="store" class="bg-green-600 text-white px-4 py-2 rounded">
                    Simpan
                </button>
            @endif
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded border">
            <h3 class="font-semibold text-gray-700 mb-2">Catatan:</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Pastikan wali murid sudah terdaftar di sistem sebelum memilih</li>
                <li>• NIS harus unik dan tidak boleh sama dengan siswa lain</li>
                <li>• Status nonaktif akan menonaktifkan akses siswa</li>
            </ul>
        </div>

    </div>

    {{-- TABEL --}}
    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="border p-2">Nama</th>
                <th class="border p-2">NIS</th>
                <th class="border p-2">Kelas</th>
                <th class="border p-2">Wali Murid</th>
                <th class="border p-2">Status</th>
                <th class="border p-2 w-40">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($dataSiswa as $s)
                @php
                    // Gunai optional() untuk menghindari error jika relasi null
                    $kelasNama = optional($s->kelas)->nama_kelas ?? '-';
                    $waliNama = optional(optional($s->waliMurid)->pengguna)->name ?? '-';
                @endphp
                <tr>
                    <td class="border p-2">{{ $s->nama }}</td>
                    <td class="border p-2">{{ $s->nis }}</td>
                    <td class="border p-2">{{ $kelasNama }}</td>
                    <td class="border p-2">{{ $waliNama }}</td>
                    <td class="border p-2">{{ $s->status }}</td>
                    <td class="border p-2">
                        <button wire:click="edit({{ $s->id_siswa }})"
                                class="text-blue-600">Edit</button>

                        <button wire:click="delete({{ $s->id_siswa }})"
                                class="text-red-600 ml-3"
                                onclick="return confirm('Hapus siswa?')">
                            Hapus
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>