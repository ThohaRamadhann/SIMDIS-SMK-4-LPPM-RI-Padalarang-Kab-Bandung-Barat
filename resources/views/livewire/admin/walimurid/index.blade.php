<div class="space-y-6">

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="p-4 bg-green-100 text-green-700 rounded-lg border">
            {{ session('success') }}
        </div>
    @endif

    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- FORM --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg border p-6">

                <h2 class="text-lg font-bold mb-4">
                    {{ $isEdit ? 'Edit Wali Murid' : 'Tambah Wali Murid' }}
                </h2>

                <form wire:submit.prevent="simpan" class="space-y-4">

                    {{-- WALI --}}
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Pilih Nama Wali Murid *
                        </label>
                        <select wire:model.live="id_pengguna"
                            class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih Nama Wali Murid --</option>
                            @foreach ($pengguna as $p)
                                <option value="{{ $p->id_pengguna }}">
                                    {{ $p->name }} ({{ $p->username }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pengguna')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- HUBUNGAN --}}
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Hubungan *
                        </label>
                        <input type="text"
                            wire:model="hubungan"
                            placeholder="Ayah / Ibu / Wali"
                            class="w-full border rounded px-3 py-2">
                        @error('hubungan')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- INFO --}}
                    @if($id_pengguna)
                        <div class="p-3 bg-blue-50 border rounded text-sm text-blue-700">
                            {{ $isEdit ? 'Mode Edit: Data sudah ada' : 'Mode Tambah: Data baru' }}
                        </div>
                    @endif

                    {{-- BUTTON --}}
                    <div class="flex gap-3 pt-4">
                        @if ($isEdit)
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                Update
                            </button>
                            <button type="button"
                                wire:click="resetForm"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                                Batal
                            </button>
                        @else
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                                Simpan
                            </button>
                        @endif
                    </div>

                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="lg:col-span-3">
            <div class="bg-white shadow rounded-lg border p-4">

                <h3 class="font-bold text-lg mb-4">
                    Daftar Wali Murid
                </h3>

                @if($data->count())
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-[900px] w-full text-sm border-collapse">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2">No</th>
                                    <th class="border px-3 py-2 text-left">Nama</th>
                                    <th class="border px-3 py-2">Username</th>
                                    <th class="border px-3 py-2">Email</th>
                                    <th class="border px-3 py-2">Hubungan</th>
                                    <th class="border px-3 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-3 py-2 text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="border px-3 py-2 font-medium">
                                            {{ $item->pengguna->name ?? '-' }}
                                        </td>
                                        <td class="border px-3 py-2">
                                            {{ $item->pengguna->username ?? '-' }}
                                        </td>
                                        <td class="border px-3 py-2">
                                            {{ $item->pengguna->email ?? '-' }}
                                        </td>
                                        <td class="border px-3 py-2 text-center">
                                            {{ $item->hubungan }}
                                        </td>
                                        <td class="border px-3 py-2 text-center whitespace-nowrap">
                                            <button wire:click="edit({{ $item->id_walimurid }})"
                                                class="text-blue-600 hover:underline mr-2">
                                                Edit
                                            </button>
                                            <button wire:click="hapus({{ $item->id_walimurid }})"
                                                onclick="return confirm('Hapus data wali murid?')"
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
                @else
                    <p class="text-center text-gray-500 py-8">
                        Belum ada data wali murid
                    </p>
                @endif

            </div>
        </div>

    </div>
</div>
