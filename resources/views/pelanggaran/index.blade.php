<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h1 class="text-2xl font-bold">Daftar Pelanggaran</h1>

            @can('create', App\Models\Pelanggaran::class)
                <a href="{{ route('pelanggaran.create') }}"
                   class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Tambah Pelanggaran
                </a>
            @endcan
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- ===================== --}}
        {{-- MOBILE VIEW (CARD) --}}
        {{-- ===================== --}}
        <div class="space-y-4 md:hidden">
            @forelse($pelanggarans as $p)
                <div class="border rounded-lg p-4 shadow-sm bg-white">
                    <div class="space-y-2 text-sm">

                        <div>
                            <span class="font-semibold">Siswa:</span>
                            {{ $p->siswa->nama ?? '-' }}
                        </div>

                        <div>
                            <span class="font-semibold">Wali Kelas:</span>
                            {{ $p->waliKelas->pengguna->name ?? '-' }}
                        </div>

                        <div>
                            <span class="font-semibold">Jenis:</span>
                            {{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}
                        </div>

                        <div>
                            <span class="font-semibold">Deskripsi:</span>
                            {{ $p->deskripsi ?? '-' }}
                        </div>

                        <div>
                            <span class="font-semibold">Tanggal:</span>
                            {{ $p->waktu_kejadian
                                ? \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i')
                                : '-' }}
                        </div>

                        {{-- ACTION --}}
                        <div class="flex gap-3 pt-2 border-t">
                            @can('update', $p)
                                <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}"
                                   class="text-blue-600 hover:underline">
                                    Edit
                                </a>
                            @endcan

                            @can('delete', $p)
                                <form action="{{ route('pelanggaran.destroy', $p->id_pelanggaran) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500">
                    Belum ada pelanggaran
                </div>
            @endforelse
        </div>

        {{-- ===================== --}}
        {{-- DESKTOP VIEW (TABLE) --}}
        {{-- ===================== --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full table-auto border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">No</th>
                        <th class="border px-3 py-2">Siswa</th>
                        <th class="border px-3 py-2">Wali Kelas</th>
                        <th class="border px-3 py-2">Jenis Pelanggaran</th>
                        <th class="border px-3 py-2">Deskripsi</th>
                        <th class="border px-3 py-2">Tanggal</th>
                        <th class="border px-3 py-2">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pelanggarans as $index => $p)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="border px-3 py-2">{{ $p->siswa->nama ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $p->waliKelas->pengguna->name ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $p->deskripsi ?? '-' }}</td>
                            <td class="border px-3 py-2">
                                {{ $p->waktu_kejadian
                                    ? \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i')
                                    : '-' }}
                            </td>
                            <td class="border px-3 py-2 whitespace-nowrap">
                                @can('update', $p)
                                    <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}"
                                       class="text-blue-600 hover:underline mr-2">
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
                                        <button type="submit" class="text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border px-3 py-4 text-center text-gray-500">
                                Belum ada pelanggaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
