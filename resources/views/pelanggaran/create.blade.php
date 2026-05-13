<x-app-layout>

    <div class="max-w-5xl mx-auto p-4">

        {{-- HEADER --}}
        <div class="mb-4">
            <h1 class="text-[20px] font-bold text-[#0D2D6B]">
                Tambah Pelanggaran
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Kelola data pelanggaran siswa SIMDIS
            </p>
        </div>


        {{-- ERROR --}}
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">

                <ul class="list-disc pl-5 space-y-1">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>
        @endif


        {{-- CARD --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">

            <form action="{{ route('pelanggaran.store') }}"
                  method="POST"
                  class="space-y-3">

                @csrf


                {{-- SISWA --}}
                <div>

                    <label class="text-xs font-semibold text-[#0D2D6B]">
                        Siswa
                    </label>

                    <select
                        name="id_siswa"
                        id="id_siswa"

                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">

                        <option value="">
                            -- Pilih Siswa --
                        </option>

                        @foreach($siswa as $s)

                            <option
                                value="{{ $s->id_siswa }}"

                                data-kelas="
                                    {{ $s->kelas->tingkat ?? '' }}
                                    {{ $s->kelas->nama_kelas ?? '' }}
                                    {{ $s->kelas->jurusan ?? '' }}
                                "

                                data-wali="
                                    {{ $s->kelas?->waliKelas?->pengguna?->name ?? '-' }}
                                "

                                data-idwalikelas="
                                    {{ $s->kelas?->waliKelas?->id_walikelas ?? '' }}
                                "

                                {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>

                                {{ $s->nama }} - {{ $s->nis }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- KELAS --}}
                <div>

                    <label class="text-xs font-semibold text-[#0D2D6B]">
                        Kelas
                    </label>

                    <input
                        type="text"
                        id="nama_kelas"
                        readonly

                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-100 text-black">

                </div>


                {{-- WALI KELAS --}}
                <div>

                    <label class="text-xs font-semibold text-[#0D2D6B]">
                        Wali Kelas
                    </label>

                    <input
                        type="text"
                        id="nama_walikelas"
                        readonly

                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-100 text-black">

                    {{-- hidden input --}}
                    <input
                        type="hidden"
                        name="id_walikelas"
                        id="id_walikelas">

                </div>


                {{-- JENIS PELANGGARAN --}}
                <div>

                    <label class="text-xs font-semibold text-[#0D2D6B]">
                        Jenis Pelanggaran
                    </label>

                    <select
                        name="id_jenispelanggaran"

                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">

                        <option value="">
                            -- Pilih Jenis Pelanggaran --
                        </option>

                        @foreach($jenisPelanggaran as $j)

                            <option
                                value="{{ $j->id_jenispelanggaran }}"
                                {{ old('id_jenispelanggaran') == $j->id_jenispelanggaran ? 'selected' : '' }}>

                                {{ $j->nama_pelanggaran }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- WAKTU --}}
                <div>

                    <label class="text-xs font-semibold text-[#0D2D6B]">
                        Waktu Kejadian
                    </label>

                    <input
                        type="datetime-local"
                        name="waktu_kejadian"
                        value="{{ old('waktu_kejadian') }}"

                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">

                </div>


                {{-- DESKRIPSI --}}
                <div>

                    <label class="text-xs font-semibold text-[#0D2D6B]">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="4"

                        class="mt-0.5 w-full px-3 py-2 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20 resize-none">{{ old('deskripsi') }}</textarea>

                </div>


                {{-- BUTTON --}}
                <div class="flex items-center gap-2 pt-2">

                    <button
                        type="submit"

                        class="bg-[#0D2D6B] text-white px-4 py-2 text-sm
                               rounded-lg hover:bg-[#163580] transition">

                        Simpan

                    </button>

                    <a href="{{ route('pelanggaran.index') }}"

                       class="bg-white border border-gray-300 text-gray-600
                              px-4 py-2 text-sm rounded-lg hover:bg-gray-50 transition">

                        Batal

                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- AUTO SET WALI KELAS --}}
    <script>

        const siswaSelect =
            document.getElementById('id_siswa');

        const namaWali =
            document.getElementById('nama_walikelas');

        const idWali =
            document.getElementById('id_walikelas');

        const namaKelas =
            document.getElementById('nama_kelas');


        function updateDataSiswa() {

            const selected =
                siswaSelect.options[siswaSelect.selectedIndex];

            namaWali.value =
                selected.getAttribute('data-wali') || '';

            idWali.value =
                selected.getAttribute('data-idwalikelas') || '';

            namaKelas.value =
                selected.getAttribute('data-kelas') || '';
        }


        siswaSelect.addEventListener('change', updateDataSiswa);


        // auto jalan ketika old value ada
        window.addEventListener('load', updateDataSiswa);

    </script>

</x-app-layout>