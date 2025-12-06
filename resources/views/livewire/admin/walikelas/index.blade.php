<div class="p-6">

    {{-- SEARCH --}}
    <div class="mb-4 flex justify-between">
        <input type="text"
               wire:model.live="search"
               placeholder="Cari wali kelas..."
               class="border px-3 py-2 rounded w-1/3">

        <button wire:click="resetForm"
                class="bg-green-600 text-white px-4 py-2 rounded">
            + Tambah Wali Kelas
        </button>
    </div>

    {{-- FORM --}}
    <div class="bg-white shadow p-4 mb-6 rounded border">
        <h2 class="font-bold text-lg mb-3">
            {{ $isEdit ? 'Edit Wali Kelas' : 'Tambah Wali Kelas' }}
        </h2>

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label class="font-semibold">Nama Pengguna</label>
                <select wire:model="id_pengguna" class="w-full border px-3 py-2 rounded">
                    <option value="">-- Pilih Pengguna --</option>
                    @foreach($pengguna as $p)
                        <option value="{{ $p->id_pengguna }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('id_pengguna') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-semibold">NUPTK</label>
                <input type="text" wire:model="nuptk" class="w-full border px-3 py-2 rounded">
                @error('nuptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-semibold">Jabatan</label>
                <input type="text" wire:model="jabatan" class="w-full border px-3 py-2 rounded">
                @error('jabatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
    </div>

    {{-- TABLE --}}
    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="border p-2">Nama</th>
                <th class="border p-2">NUPTK</th>
                <th class="border p-2">Jabatan</th>
                <th class="border p-2 w-40">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataWK as $w)
                <tr>
                    <td class="border p-2">{{ $w->pengguna->name }}</td>
                    <td class="border p-2">{{ $w->nuptk }}</td>
                    <td class="border p-2">{{ $w->jabatan }}</td>
                    <td class="border p-2">

                        <button wire:click="edit({{ $w->id_walikelas }})"
                                class="text-blue-600">Edit</button>

                        <button wire:click="delete({{ $w->id_walikelas }})"
                                onclick="return confirm('Hapus data ini?')"
                                class="text-red-600 ml-3">Hapus</button>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
