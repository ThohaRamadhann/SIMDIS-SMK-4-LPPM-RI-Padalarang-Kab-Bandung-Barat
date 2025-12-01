<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Tambah Pelanggaran</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-2 mb-4 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pelanggaran.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- SISWA --}}
            <div>
                <label class="block font-medium">Siswa</label>
                <select name="id_siswa" class="border rounded w-full px-2 py-1 text-black">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->id_siswa }}" {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- WALI KELAS --}}
            <div>
                <label class="block font-medium">Wali Kelas</label>
                <select name="id_walikelas" class="border rounded w-full px-2 py-1 text-black">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($waliKelas as $w)
                        <option value="{{ $w->id_walikelas }}" {{ old('id_walikelas') == $w->id_walikelas ? 'selected' : '' }}>
                            {{ $w->pengguna->name ?? 'Nama tidak ada' }} {{-- ambil dari relasi pengguna --}}
                        </option>
                    @endforeach
                </select>                
            </div>

            {{-- JENIS PELANGGARAN --}}
            <div>
                <label class="block font-medium">Jenis Pelanggaran</label>
                <select name="id_jenispelanggaran" class="border rounded w-full px-2 py-1 text-black">
                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                    @foreach($jenisPelanggaran as $j)
                        <option value="{{ $j->id_jenispelanggaran }}" {{ old('id_jenispelanggaran') == $j->id_jenispelanggaran ? 'selected' : '' }}>
                            {{ $j->nama_pelanggaran }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- WAKTU --}}
            <div>
                <label class="block font-medium">Waktu Kejadian</label>
                <input type="datetime-local" name="waktu_kejadian" class="border rounded w-full px-2 py-1 text-black" value="{{ old('waktu_kejadian') }}">
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label class="block font-medium">Deskripsi</label>
                <textarea name="deskripsi" class="border rounded w-full px-2 py-1 text-black">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- BUTTON --}}
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Simpan
                </button>
                <a href="{{ route('pelanggaran.index') }}" class="ml-2 text-gray-700 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
