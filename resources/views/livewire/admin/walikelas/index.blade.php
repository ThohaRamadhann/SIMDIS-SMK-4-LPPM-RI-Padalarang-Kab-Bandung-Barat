<div>

    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- FORM --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow p-6 rounded-lg border">

                <h2 class="font-bold text-lg mb-4">
                    {{ $isEdit ? 'Edit Data Wali Kelas' : 'Tambah Data Wali Kelas' }}
                </h2>

                <div class="space-y-4">

                    <div>
                        <label class="font-semibold block mb-1">
                            Nama Wali Kelas *
                        </label>
                        <select wire:model="id_pengguna"
                            wire:change="$dispatch('updatedIdPengguna', $event.target.value)"
                            class="w-full border px-3 py-2 rounded">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($pengguna as $p)
                                <option value="{{ $p->id_pengguna }}">
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_pengguna')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            Pilih wali kelas dari pengguna terdaftar
                        </p>
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">
                            NUPTK (Opsional)
                        </label>
                        <input type="text"
                            wire:model.defer="nuptk"
                            placeholder="Masukkan NUPTK jika ada"
                            class="w-full border px-3 py-2 rounded">
                        @error('nuptk')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">
                            Jabatan (Opsional)
                        </label>
                        <input type="text"
                            wire:model.defer="jabatan"
                            placeholder="Contoh: Wali Kelas X IPA"
                            class="w-full border px-3 py-2 rounded">
                        @error('jabatan')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex gap-3 pt-4">
                        @if ($isEdit)
                            <button wire:click="update"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                Update
                            </button>
                            <button wire:click="resetForm"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                                Simpan
                            </button>
                        @endif
                    </div>

                    {{-- INFO --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded border text-sm text-gray-600">
                        <ul class="space-y-1">
                            <li>• Data NUPTK & Jabatan bisa dikosongkan</li>
                            <li>• Terisi otomatis jika sudah ada</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="lg:col-span-3">
            <div class="bg-white shadow rounded-lg border p-4">

                <h3 class="font-bold text-lg mb-4">
                    Daftar Wali Kelas
                </h3>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-[700px] w-full text-sm border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Nama</th>
                                <th class="border px-3 py-2 text-center">NUPTK</th>
                                <th class="border px-3 py-2">Jabatan</th>
                                <th class="border px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($dataWK as $w)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-3 py-2 font-medium">
                                        {{ $w->pengguna->name }}
                                    </td>
                                    <td class="border px-3 py-2 text-center">
                                        {{ $w->nuptk ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        {{ $w->jabatan ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2 text-center whitespace-nowrap">
                                        <button wire:click="edit({{ $w->id_walikelas }})"
                                            class="text-blue-600 hover:underline mr-2">
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $w->id_walikelas }})"
                                            onclick="return confirm('Hapus data wali kelas ini?')"
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
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('updatedIdPengguna', (idPengguna) => {
            @this.dispatch('updatedIdPengguna', { value: idPengguna });
        });
    });
</script>
@endpush
