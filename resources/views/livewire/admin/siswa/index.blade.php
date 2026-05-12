<div class="space-y-2">

    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-3">

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-3">

                <h2 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}
                </h2>

                <p class="text-xs text-gray-500 mb-4">
                    Kelola data siswa SIMDIS
                </p>

                <div class="space-y-2">

                    {{-- Nama --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Nama Siswa *</label>
                        <input type="text" wire:model.defer="nama"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                        @error('nama')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- NIS --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">NIS *</label>
                        <input type="text" wire:model.defer="nis"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                        @error('nis')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Kelas *</label>
                        <select wire:model.defer="id_kelas"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('id_kelas')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Wali --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Wali Murid *</label>
                        <select wire:model.defer="id_walimurid"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="">-- Pilih Wali Murid --</option>
                            @foreach($wali as $w)
                                <option value="{{ $w->id_walimurid }}">
                                    {{ optional($w->pengguna)->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_walimurid')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Status</label>
                        <select wire:model.defer="status"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex gap-2 pt-2">
                        @if ($isEdit)
                            <button wire:click="update"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580] transition">
                                Update
                            </button>

                            <button wire:click="resetForm"
                                class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                        @else
                            <button wire:click="store"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm rounded-lg hover:bg-[#163580] transition">
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
                            Daftar Siswa
                        </h3>
                        <p class="text-xs text-gray-500">
                            Data siswa aktif & nonaktif
                        </p>
                    </div>

                    <span class="text-xs font-semibold text-[#0D2D6B] bg-blue-50 px-3 py-1 rounded-full">
                        {{ count($dataSiswa) }} siswa
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                            <tr>
                                <th>No</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">NIS</th>
                                <th class="px-3 py-2">Kelas</th>
                                <th class="px-3 py-2">Wali Murid</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($dataSiswa as $s)
                                <tr class="hover:bg-gray-50 transition">

                                    <td class="px-3 py-2 text-center text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-3 py-2 font-medium text-[#0D2D6B]">
                                        {{ $s->nama }}
                                    </td>

                                    <td class="px-3 py-2 text-center text-gray-600">
                                        {{ $s->nis }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ optional($s->kelas)->nama_kelas ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ optional(optional($s->waliMurid)->pengguna)->name ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2">
                                        <span class="text-xs px-2 py-1 rounded-full
                                            {{ $s->status == 'aktif'
                                                ? 'bg-green-50 text-green-600'
                                                : 'bg-red-50 text-red-600' }}">
                                            {{ $s->status }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                        <button wire:click="edit({{ $s->id_siswa }})"
                                            class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                            Edit
                                        </button>

                                        <button wire:click="delete({{ $s->id_siswa }})"
                                            onclick="return confirm('Hapus siswa?')"
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