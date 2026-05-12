<div class="space-y-3">

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                <h2 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $isEdit ? 'Edit Data Wali Kelas' : 'Tambah Data Wali Kelas' }}
                </h2>

                <p class="text-xs text-gray-500 mb-3">
                    Kelola data wali kelas SIMDIS
                </p>

                <div class="space-y-2">

                    {{-- Nama Wali Kelas --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Nama Wali Kelas *
                        </label>

                        <select wire:model="id_pengguna"
                            wire:change="$dispatch('updatedIdPengguna', $event.target.value)"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($pengguna as $p)
                                <option value="{{ $p->id_pengguna }}">
                                    {{ $p->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('id_pengguna')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror

                        <p class="text-[11px] text-gray-400 mt-1">
                            Pilih wali kelas dari pengguna terdaftar
                        </p>
                    </div>

                    {{-- NUPTK --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            NUPTK (Opsional)
                        </label>

                        <input type="text"
                            wire:model.defer="nuptk"
                            placeholder="Masukkan NUPTK jika ada"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                        @error('nuptk')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Jabatan (Opsional)
                        </label>

                        <input type="text"
                            wire:model.defer="jabatan"
                            placeholder="Contoh: Wali Kelas X IPA"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                        @error('jabatan')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex gap-2 pt-2">
                        @if ($isEdit)
                            <button wire:click="update"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580]">
                                Update
                            </button>

                            <button wire:click="resetForm"
                                class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580]">
                                Simpan
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>


        {{-- ================= TABLE ================= --}}
        <div class="xl:col-span-3">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                <div class="flex justify-between items-center mb-3">

                    <div>
                        <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                            Daftar Wali Kelas
                        </h3>
                        <p class="text-xs text-gray-500">
                            Data seluruh wali kelas SIMDIS
                        </p>
                    </div>

                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-center">NUPTK</th>
                                <th class="px-3 py-2">Jabatan</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach ($dataWK as $w)
                                <tr class="hover:bg-gray-50 transition">
                                    
                                    <td class="px-3 py-2 text-center text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-3 py-2 font-medium text-[#0D2D6B]">
                                        {{ $w->pengguna->name }}
                                    </td>

                                    <td class="px-3 py-2 text-center text-gray-600">
                                        {{ $w->nuptk ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $w->jabatan ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-center whitespace-nowrap">

                                        <button wire:click="edit({{ $w->id_walikelas }})"
                                            class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                            Edit
                                        </button>

                                        <button wire:click="delete({{ $w->id_walikelas }})"
                                            onclick="return confirm('Hapus data wali kelas ini?')"
                                            class="text-red-500 text-xs font-semibold hover:underline">
                                            Hapus
                                        </button>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                <p class="text-xs text-gray-400 mt-2 xl:hidden">
                    Geser tabel ke samping →
                </p>

            </div>
        </div>

    </div>
</div>