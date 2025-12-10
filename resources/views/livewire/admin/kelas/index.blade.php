<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- FORM TAMBAH / EDIT --}}
    <div class="lg:col-span-2">
        <div class="bg-white shadow p-6 rounded-lg border">
            <h3 class="font-bold text-lg mb-4">
                {{ $editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
            </h3>

            <div class="space-y-4">

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Kelas *</label>
                    <input type="text" wire:model.defer="nama_kelas"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tingkat *</label>
                    <input type="text" wire:model.defer="tingkat"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Jurusan *</label>
                    <input type="text" wire:model.defer="jurusan"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tahun Ajaran *</label>
                    <input type="text" wire:model.defer="tahun_ajaran"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Wali Kelas</label>
                    <select wire:model.defer="id_walikelas"
                        class="w-full border rounded px-3 py-2">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($waliKelasList as $wali)
                            <option value="{{ $wali->id_walikelas }}">
                                {{ optional($wali->pengguna)->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <button wire:click="save"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ $editingId ? 'Update' : 'Simpan' }}
                    </button>

                    <button wire:click="resetForm"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Reset
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="lg:col-span-3">
        <div class="bg-white shadow rounded-lg border p-4">

            <h3 class="font-bold text-lg mb-4">Daftar Kelas</h3>

            @if(session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-[900px] w-full text-sm border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">No</th>
                            <th class="border px-3 py-2 text-left">Nama Kelas</th>
                            <th class="border px-3 py-2">Tingkat</th>
                            <th class="border px-3 py-2">Jurusan</th>
                            <th class="border px-3 py-2">Tahun Ajaran</th>
                            <th class="border px-3 py-2">Wali Kelas</th>
                            <th class="border px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($kelas as $k)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-center">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="border px-3 py-2 font-medium">
                                    {{ $k->nama_kelas }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    {{ $k->tingkat }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ $k->jurusan }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ $k->tahun_ajaran }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ optional(optional($k->waliKelas)->pengguna)->name ?? '-' }}
                                </td>
                                <td class="border px-3 py-2 text-center whitespace-nowrap">
                                    <button wire:click="edit({{ $k->id_kelas }})"
                                        class="text-blue-600 hover:underline mr-2">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $k->id_kelas }})"
                                        onclick="return confirm('Hapus kelas ini?')"
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
                Geser tabel ke samping untuk melihat semua kolom →
            </p>

        </div>
    </div>

</div>
