<x-app-layout>

    <div class="max-w-7xl mx-auto p-4 space-y-4">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h1 class="text-[20px] font-bold text-[#0D2D6B]">
                    Daftar Pelanggaran
                </h1>
            </div>

            @can('create', App\Models\Pelanggaran::class)
                <a href="{{ route('pelanggaran.create') }}"
                    class="inline-flex items-center justify-center bg-[#0D2D6B] text-white
                           px-4 py-2 text-sm rounded-lg hover:bg-[#163580] transition">
                    + Tambah Pelanggaran
                </a>
            @endcan

        </div>


        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-100 text-green-700
                        px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif


        {{-- ================= MOBILE VIEW ================= --}}
        <div class="space-y-3 md:hidden">

            @forelse($pelanggarans as $p)

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                    <div class="space-y-2 text-sm">

                        <div>
                            <span class="font-semibold text-[#0D2D6B]">Siswa:</span>
                            <span class="text-gray-700">
                                {{ $p->siswa->nama ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="font-semibold text-[#0D2D6B]">Wali Kelas:</span>
                            <span class="text-gray-700">
                                {{ $p->waliKelas->pengguna->name ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="font-semibold text-[#0D2D6B]">Jenis:</span>
                            <span class="text-gray-700">
                                {{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="font-semibold text-[#0D2D6B]">Deskripsi:</span>
                            <span class="text-gray-700">
                                {{ $p->deskripsi ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="font-semibold text-[#0D2D6B]">Tanggal:</span>
                            <span class="text-gray-700">
                                {{ $p->waktu_kejadian
                                    ? \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i')
                                    : '-' }}
                            </span>
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="flex gap-3 pt-3 mt-3 border-t border-gray-100">

                        @can('update', $p)
                            <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}"
                                class="text-[#0D2D6B] text-xs font-semibold hover:underline">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $p)
                            <form action="{{ route('pelanggaran.destroy', $p->id_pelanggaran) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin hapus?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="text-red-500 text-xs font-semibold hover:underline">
                                    Hapus
                                </button>

                            </form>
                        @endcan

                    </div>

                </div>

            @empty

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8
                            text-center text-sm text-gray-400">
                    Belum ada pelanggaran
                </div>

            @endforelse

        </div>


        {{-- ================= DESKTOP VIEW ================= --}}
        <div class="hidden md:block">

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                <div class="mb-3">
                    <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                        Data Pelanggaran seluruh Siswa
                    </h3>
                    <p class="text-xs text-gray-500">
                        Riwayat seluruh pelanggaran siswa
                    </p>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                            <tr>
                                <th class="px-3 py-2 text-center">No</th>
                                <th class="px-3 py-2 text-left">Siswa</th>
                                <th class="px-3 py-2">Wali Kelas</th>
                                <th class="px-3 py-2">Jenis Pelanggaran</th>
                                <th class="px-3 py-2">Deskripsi</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($pelanggarans as $index => $p)

                                <tr class="hover:bg-gray-50 transition">

                                    <td class="px-3 py-2 text-center text-gray-500">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-3 py-2 font-medium text-[#0D2D6B]">
                                        {{ $p->siswa->nama ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $p->waliKelas->pengguna->name ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600">
                                        {{ $p->deskripsi ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600 whitespace-nowrap">
                                        {{ $p->waktu_kejadian
                                            ? \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i')
                                            : '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-center whitespace-nowrap">

                                        @can('update', $p)
                                            <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}"
                                                class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('delete', $p)
                                            <form action="{{ route('pelanggaran.destroy', $p->id_pelanggaran) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin hapus?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="text-red-500 text-xs font-semibold hover:underline">
                                                    Hapus
                                                </button>

                                            </form>
                                        @endcan

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7"
                                        class="px-3 py-8 text-center text-sm text-gray-400">
                                        Belum ada pelanggaran
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>