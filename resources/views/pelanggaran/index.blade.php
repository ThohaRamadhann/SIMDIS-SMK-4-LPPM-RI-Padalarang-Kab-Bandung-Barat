<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Daftar Pelanggaran</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @can('create', App\Models\Pelanggaran::class)
            <a href="{{ route('pelanggaran.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Tambah Pelanggaran
            </a>
        @endcan

        <div class="overflow-x-auto mt-4">
            <table class="w-full table-auto border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-2 py-1">No</th>
                        <th class="border px-2 py-1">Siswa</th>
                        <th class="border px-2 py-1">Wali Kelas</th>
                        <th class="border px-2 py-1">Jenis Pelanggaran</th>
                        <th class="border px-2 py-1">Deskripsi</th>
                        <th class="border px-2 py-1">Tanggal</th>
                        <th class="border px-2 py-1">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pelanggarans as $index => $p)
                        <tr>
                            <td class="border px-2 py-1">{{ $index + 1 }}</td>

                            {{-- Nama siswa --}}
                            <td class="border px-2 py-1">
                                {{ $p->siswa->nama ?? '-' }}
                            </td>

                            {{-- Nama wali kelas --}}
                            <td class="border px-2 py-1">
                                {{ $p->waliKelas->pengguna->name ?? '-' }}
                            </td>

                            {{-- Jenis pelanggaran --}}
                            <td class="border px-2 py-1">
                                {{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}
                            </td>

                            {{-- Deskripsi --}}
                            <td class="border px-2 py-1">
                                {{ $p->deskripsi ?? '-' }}
                            </td>

                            {{-- Tanggal format Carbon --}}
                            <td class="border px-2 py-1">
                                {{ $p->waktu_kejadian ? \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i') : '-' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="border px-2 py-1">
                                @can('update', $p)
                                    <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}" class="text-blue-600 hover:underline mr-2">
                                        Edit
                                    </a>
                                @endcan

                                @can('delete', $p)
                                    <form action="{{ route('pelanggaran.destroy', $p->id_pelanggaran) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus?')" class="text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="border px-2 py-1 text-center">Belum ada pelanggaran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
