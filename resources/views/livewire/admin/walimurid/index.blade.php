<div class="space-y-3">

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200
                    text-green-700 px-3 py-2 rounded-xl text-sm">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif


    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                <h2 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $isEdit ? 'Edit Wali Murid' : 'Tambah Wali Murid' }}
                </h2>

                <p class="text-xs text-gray-500 mb-3">
                    Kelola data wali murid SIMDIS
                </p>

                <form wire:submit.prevent="simpan" class="space-y-2">

                    {{-- PILIH PENGGUNA --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Nama Wali Murid *
                        </label>

                        <select wire:model.live="id_pengguna"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                            <option value="">-- Pilih Nama Wali Murid --</option>
                            @foreach ($pengguna as $p)
                                <option value="{{ $p->id_pengguna }}">
                                    {{ $p->name }} ({{ $p->username }})
                                </option>
                            @endforeach

                        </select>

                        @error('id_pengguna')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- HUBUNGAN --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Hubungan *
                        </label>

                        <input type="text"
                            wire:model="hubungan"
                            placeholder="Ayah / Ibu / Wali"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                        @error('hubungan')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- INFO --}}
                    @if($id_pengguna)
                        <div class="text-xs bg-blue-50 text-blue-700 border border-blue-100 px-3 py-2 rounded-lg">
                            {{ $isEdit ? 'Mode Edit: Data sudah ada' : 'Mode Tambah: Data baru' }}
                        </div>
                    @endif

                    {{-- BUTTON --}}
                    <div class="flex gap-2 pt-2">
                        @if ($isEdit)
                            <button type="submit"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580]">
                                Update
                            </button>

                            <button type="button"
                                wire:click="resetForm"
                                class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                        @else
                            <button type="submit"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580]">
                                Simpan
                            </button>
                        @endif
                    </div>

                </form>

            </div>
        </div>


        {{-- ================= TABLE ================= --}}
        <div class="xl:col-span-3">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                            Daftar Wali Murid
                        </h3>
                        <p class="text-xs text-gray-500">
                            Data seluruh wali murid
                        </p>
                    </div>

                    <span class="text-xs font-semibold text-[#0D2D6B] bg-blue-50 px-3 py-1 rounded-full">
                        {{ $data->count() }} data
                    </span>
                </div>

                @if($data->count())

                    <div class="overflow-x-auto rounded-xl border border-gray-100">

                        <table class="w-full text-sm">

                            <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                                <tr>
                                    <th class="px-3 py-2">No</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2">Username</th>
                                    <th class="px-3 py-2">Email</th>
                                    <th class="px-3 py-2">Hubungan</th>
                                    <th class="px-3 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">

                                @foreach ($data as $item)
                                    <tr class="hover:bg-gray-50 transition">

                                        <td class="px-3 py-2 text-center text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="px-3 py-2 font-medium text-[#0D2D6B]">
                                            {{ $item->pengguna->name ?? '-' }}
                                        </td>

                                        <td class="px-3 py-2 text-gray-600">
                                            {{ $item->pengguna->username ?? '-' }}
                                        </td>

                                        <td class="px-3 py-2 text-gray-600">
                                            {{ $item->pengguna->email ?? '-' }}
                                        </td>

                                        <td class="px-3 py-2 text-center">
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-600">
                                                {{ $item->hubungan }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-2 text-center whitespace-nowrap">

                                            <button wire:click="edit({{ $item->id_walimurid }})"
                                                class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                                Edit
                                            </button>

                                            <button wire:click="hapus({{ $item->id_walimurid }})"
                                                onclick="return confirm('Hapus data wali murid?')"
                                                class="text-red-500 text-xs font-semibold hover:underline">
                                                Hapus
                                            </button>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Belum ada data wali murid
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>