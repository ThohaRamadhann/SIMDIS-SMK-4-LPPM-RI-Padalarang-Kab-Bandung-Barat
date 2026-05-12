<x-app-layout>

    <div class="max-w-5xl mx-auto p-4">

        {{-- HEADER --}}
        <div class="mb-4">
            <h1 class="text-[20px] font-bold text-[#0D2D6B]">
                Edit Pelanggaran
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Perbarui data pelanggaran siswa SIMDIS
            </p>
        </div>


        {{-- ERROR --}}
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700
                        px-4 py-3 rounded-xl text-sm">

                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- CARD FORM --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">

            <form action="{{ route('pelanggaran.update', $pelanggaran->id_pelanggaran) }}"
                  method="POST"
                  class="space-y-3">

                @csrf
                @method('PUT')


                {{-- SISWA --}}
                <div>
                    <label for="id_siswa"
                        class="text-xs font-semibold text-[#0D2D6B]">
                        Siswa
                    </label>

                    <select name="id_siswa"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">

                        <option value="">-- Pilih Siswa --</option>

                        @foreach($siswa as $s)
                            <option value="{{ $s->id_siswa }}"
                                {{ old('id_siswa', $pelanggaran->id_siswa) == $s->id_siswa ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- WALI KELAS --}}
                <div>
                    <label for="id_walikelas"
                        class="text-xs font-semibold text-[#0D2D6B]">
                        Wali Kelas
                    </label>

                    <select name="id_walikelas"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">

                        <option value="">-- Pilih Wali Kelas --</option>

                        @foreach($waliKelas as $w)
                            <option value="{{ $w->id_walikelas }}"
                                {{ old('id_walikelas', $pelanggaran->id_walikelas) == $w->id_walikelas ? 'selected' : '' }}>
                                {{ $w->pengguna->name ?? 'Nama tidak ada' }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- JENIS PELANGGARAN --}}
                <div>
                    <label for="id_jenispelanggaran"
                        class="text-xs font-semibold text-[#0D2D6B]">
                        Jenis Pelanggaran
                    </label>

                    <select name="id_jenispelanggaran"
                        id="id_jenispelanggaran"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">

                        @foreach($jenisPelanggaran as $j)
                            <option value="{{ $j->id_jenispelanggaran }}"
                                {{ old('id_jenispelanggaran', $pelanggaran->id_jenispelanggaran) == $j->id_jenispelanggaran ? 'selected' : '' }}>
                                {{ $j->nama_pelanggaran }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- WAKTU KEJADIAN --}}
                <div>
                    <label for="waktu_kejadian"
                        class="text-xs font-semibold text-[#0D2D6B]">
                        Waktu Kejadian
                    </label>

                    <input
                        type="datetime-local"
                        name="waktu_kejadian"
                        id="waktu_kejadian"

                        value="{{ old('waktu_kejadian', optional(\Carbon\Carbon::parse($pelanggaran->waktu_kejadian))->format('Y-m-d\TH:i')) }}"

                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20">
                </div>


                {{-- DESKRIPSI --}}
                <div>
                    <label for="deskripsi"
                        class="text-xs font-semibold text-[#0D2D6B]">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        id="deskripsi"
                        rows="4"

                        class="mt-0.5 w-full px-3 py-2 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20 resize-none">{{ old('deskripsi', $pelanggaran->deskripsi) }}</textarea>
                </div>


                {{-- BUTTON --}}
                <div class="flex items-center gap-2 pt-2">

                    <button type="submit"
                        class="bg-[#0D2D6B] text-white px-4 py-2 text-sm
                               rounded-lg hover:bg-[#163580] transition">
                        Update
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

</x-app-layout>