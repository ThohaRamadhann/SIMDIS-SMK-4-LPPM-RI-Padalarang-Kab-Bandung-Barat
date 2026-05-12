<div class="space-y-3">

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
                </h3>

                <p class="text-xs text-gray-500 mb-3">
                    Kelola data kelas SIMDIS
                </p>

                <div class="space-y-2">

                    {{-- Nama Kelas --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Nama Kelas *
                        </label>
                        <input type="text" wire:model.defer="nama_kelas"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                    </div>

                    {{-- Tingkat --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Tingkat *
                        </label>
                        <input type="text" wire:model.defer="tingkat"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                    </div>

                    {{-- Jurusan --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Jurusan *
                        </label>
                        <input type="text" wire:model.defer="jurusan"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                    </div>

                    {{-- Tahun Ajaran --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Tahun Ajaran *
                        </label>
                        <input type="text" wire:model.defer="tahun_ajaran"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                    </div>

                    {{-- Wali Kelas --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">
                            Wali Kelas
                        </label>
                        <select wire:model.defer="id_walikelas"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($waliKelasList as $wali)
                                <option value="{{ $wali->id_walikelas }}">
                                    {{ optional($wali->pengguna)->name ?? '-' }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex gap-2 pt-2">
                        <button wire:click="save"
                            class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580]">
                            {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>

                        <button wire:click="resetForm"
                            class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                            Reset
                        </button>
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
                            Daftar Kelas
                        </h3>
                        <p class="text-xs text-gray-500">
                            Data seluruh kelas SIMDIS
                        </p>
                    </div>

                </div>

                @if(session()->has('success'))
                    <div class="mb-3 text-xs bg-green-50 text-green-700 px-3 py-2 rounded-lg border border-green-100">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto rounded-xl border border-gray-100">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                            <tr>
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2 text-left">Nama Kelas</th>
                                <th class="px-3 py-2">Tingkat</th>
                                <th class="px-3 py-2">Jurusan</th>
                                <th class="px-3 py-2">Tahun</th>
                                <th class="px-3 py-2">Wali Kelas</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach ($kelas as $k)
                                <tr class="hover:bg-gray-50 transition">

                                    <td class="px-3 py-2 text-center text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-3 py-2 font-medium text-[#0D2D6B]">
                                        {{ $k->nama_kelas }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600 text-center">
                                        {{ $k->tingkat }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $k->jurusan }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $k->tahun_ajaran }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ optional(optional($k->waliKelas)->pengguna)->name ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-center whitespace-nowrap">

                                        <button wire:click="edit({{ $k->id_kelas }})"
                                            class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                            Edit
                                        </button>

                                        <button wire:click="delete({{ $k->id_kelas }})"
                                            onclick="return confirm('Hapus kelas ini?')"
                                            class="text-red-500 text-xs font-semibold hover:underline">
                                            Hapus
                                        </button>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>
</div>