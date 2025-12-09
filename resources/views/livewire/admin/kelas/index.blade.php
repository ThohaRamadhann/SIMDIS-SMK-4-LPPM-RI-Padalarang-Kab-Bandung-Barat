<div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">

    {{-- FORM TAMBAH/EDIT --}}
    <div class="bg-white shadow p-6 rounded-lg border">
        <h3 class="font-bold text-lg mb-4">{{ $editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h3>

        <div class="space-y-4">
            {{-- Nama Kelas --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Nama Kelas *</label>
                <input type="text"
                       wire:model="nama_kelas"
                       placeholder="Contoh: X IPA 1"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- Tingkat --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tingkat *</label>
                <input type="text"
                       wire:model="tingkat"
                       placeholder="Contoh: X, XI, XII"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- Jurusan --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Jurusan *</label>
                <input type="text"
                       wire:model="jurusan"
                       placeholder="Contoh: IPA, IPS, Bahasa"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- Tahun Ajaran --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Tahun Ajaran *</label>
                <input type="text"
                       wire:model="tahun_ajaran"
                       placeholder="Contoh: 2024/2025"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- Wali Kelas --}}
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Wali Kelas (Opsional)</label>
                <select wire:model="id_walikelas"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($waliKelasList as $wali)
                        <option value="{{ $wali->id_walikelas }}">
                            {{ optional($wali->pengguna)->name ?? 'Tidak diketahui' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BUTTONS --}}
            <div class="flex space-x-3 pt-4">
                <button wire:click="save"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                    {{ $editingId ? 'Update' : 'Simpan' }}
                </button>
                <button wire:click="resetForm"
                        class="bg-gray-500 text-white px-4 py-2 rounded">
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="md:col-span-2 bg-white shadow p-6 rounded-lg border">
        <h3 class="font-bold text-lg mb-4">Daftar Kelas</h3>

        @if(session()->has('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full border">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">No</th>
                    <th class="border p-2">Nama Kelas</th>
                    <th class="border p-2">Tingkat</th>
                    <th class="border p-2">Jurusan</th>
                    <th class="border p-2">Tahun Ajaran</th>
                    <th class="border p-2">Wali Kelas</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kelas as $index => $k)
                @php
                    // Gunai optional() untuk menghindari error
                    $waliNama = optional(optional($k->waliKelas)->pengguna)->name ?? '-';
                    $waliJabatan = optional($k->waliKelas)->jabatan ?? '';
                @endphp
                <tr>
                    <td class="border p-2">{{ $loop->iteration }}</td>
                    <td class="border p-2 font-medium">{{ $k->nama_kelas }}</td>
                    <td class="border p-2">{{ $k->tingkat }}</td>
                    <td class="border p-2">{{ $k->jurusan }}</td>
                    <td class="border p-2">{{ $k->tahun_ajaran }}</td>
                    <td class="border p-2">
                        <div>
                            <div>{{ $waliNama }}</div>
                            @if($waliJabatan)
                                <div class="text-xs text-gray-500">{{ $waliJabatan }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="border p-2">
                        <button wire:click="edit({{ $k->id_kelas }})"
                                class="text-blue-600 mr-3">
                            Edit
                        </button>
                        <button wire:click="delete({{ $k->id_kelas }})"
                                onclick="return confirm('Hapus kelas ini?')"
                                class="text-red-600">
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>