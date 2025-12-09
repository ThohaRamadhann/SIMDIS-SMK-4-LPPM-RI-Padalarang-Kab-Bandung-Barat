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
            {{ $isEdit ? 'Edit Data Wali Kelas' : 'Tambah Data Wali Kelas' }}
        </h2>

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label class="font-semibold">Nama Wali Kelas *</label>
                <select wire:model="id_pengguna" 
                        wire:change="$dispatch('updatedIdPengguna', $event.target.value)"
                        class="w-full border px-3 py-2 rounded">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($pengguna as $p)
                        <option value="{{ $p->id_pengguna }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('id_pengguna') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Pilih wali kelas yang sudah ada</p>
            </div>

            <div>
                <label class="font-semibold">NUPTK (Opsional)</label>
                <input type="text" 
                       wire:model="nuptk" 
                       placeholder="Masukkan NUPTK jika ada"
                       class="w-full border px-3 py-2 rounded">
                @error('nuptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Akan terisi otomatis jika data sudah ada</p>
            </div>

            <div>
                <label class="font-semibold">Jabatan (Opsional)</label>
                <input type="text" 
                       wire:model="jabatan" 
                       placeholder="Masukkan jabatan jika ada"
                       class="w-full border px-3 py-2 rounded">
                @error('jabatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">Akan terisi otomatis jika data sudah ada</p>
            </div>

        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded border">
            <h3 class="font-semibold text-gray-700 mb-2">Informasi:</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Pilih wali kelas dari daftar yang sudah terdaftar</li>
                <li>• Data NUPTK dan Jabatan akan otomatis terisi jika sudah ada di database</li>
                <li>• Jika belum ada, isi manual atau biarkan kosong</li>
                <li>• Form ini untuk melengkapi data wali kelas yang sudah ada</li>
            </ul>
        </div>

        <div class="mt-4">
            @if ($isEdit)
                <button wire:click="update" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
                <button wire:click="resetForm" 
                        class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
            @else
                <button wire:click="store" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Simpan
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
                    <td class="border p-2">
                        @if($w->nuptk)
                            {{ $w->nuptk }}
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>
                    <td class="border p-2">
                        @if($w->jabatan)
                            {{ $w->jabatan }}
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>
                    <td class="border p-2">

                        <button wire:click="edit({{ $w->id_walikelas }})"
                                class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>

                        <button wire:click="delete({{ $w->id_walikelas }})"
                                onclick="return confirm('Hapus data wali kelas ini?')"
                                class="text-red-600 hover:text-red-800 ml-3">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen to Livewire event
        Livewire.on('updatedIdPengguna', (idPengguna) => {
            // Dispatch to Livewire component
            @this.dispatch('updatedIdPengguna', {value: idPengguna});
        });
    });
</script>
@endpush