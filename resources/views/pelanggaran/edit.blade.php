<x-app-layout>

    <div class="max-w-5xl mx-auto p-4">

        {{-- HEADER --}}
        <div class="mb-4">
            <h1 class="text-[20px] font-bold text-[#0D2D6B]">Edit Pelanggaran</h1>
            <p class="text-xs text-gray-500 mt-1">Perbarui data pelanggaran siswa SIMDIS</p>
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

        {{-- CARD FORM --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">

            <form action="{{ route('pelanggaran.update', $pelanggaran->id_pelanggaran) }}"
                  method="POST"
                  class="space-y-4">

                @csrf
                @method('PUT')

                {{-- ── SISWA ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Siswa</label>
                    <select name="id_siswa" id="id_siswa"
                            class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswa as $s)
                            <option value="{{ $s->id_siswa }}"
                                    data-walikelas="{{ $s->kelas->waliKelas->id_walikelas ?? '' }}"
                                    data-namawalikelas="{{ $s->kelas->waliKelas->pengguna->name ?? '-' }}"
                                    {{ old('id_siswa', $pelanggaran->id_siswa) == $s->id_siswa ? 'selected' : '' }}>
                                {{ $s->nama }} - {{ $s->kelas->nama_kelas ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ── WALI KELAS ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Wali Kelas</label>
                    <input type="hidden" name="id_walikelas" id="id_walikelas"
                           value="{{ old('id_walikelas', $pelanggaran->id_walikelas) }}">
                    <input type="text" id="nama_walikelas" readonly
                           value="{{ $pelanggaran->waliKelas->pengguna->name ?? '-' }}"
                           class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                  border border-gray-200 bg-gray-100 text-gray-700">
                </div>

                {{-- ── JENIS PELANGGARAN ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Jenis Pelanggaran</label>
                    <select name="id_jenispelanggaran"
                            class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                        @foreach($jenisPelanggaran as $j)
                            <option value="{{ $j->id_jenispelanggaran }}"
                                    {{ old('id_jenispelanggaran', $pelanggaran->id_jenispelanggaran) == $j->id_jenispelanggaran ? 'selected' : '' }}>
                                {{ $j->nama_pelanggaran }}
                                ({{ ucfirst($j->tingkat_pelanggaran) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ── STATUS PEMBINAAN ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Status Pembinaan</label>
                    <select name="status_pembinaan" id="status_pembinaan"
                            onchange="updateInfoStatus(this.value)"
                            class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                        {{-- FIX: nilai konsisten dengan enum DB --}}
                        <option value="Belum Ditindak"
                            {{ old('status_pembinaan', $pelanggaran->status_pembinaan) === 'Belum Ditindak' ? 'selected' : '' }}>
                            🔴 Belum Ditindak
                        </option>
                        <option value="Dalam Proses"
                            {{ old('status_pembinaan', $pelanggaran->status_pembinaan) === 'Dalam Proses' ? 'selected' : '' }}>
                            🟡 Dalam Proses
                        </option>
                        <option value="Selesai"
                            {{ old('status_pembinaan', $pelanggaran->status_pembinaan) === 'Selesai' ? 'selected' : '' }}>
                            🟢 Selesai
                        </option>
                    </select>
                </div>

                {{-- ── INFO STATUS (dinamis via JS) ── --}}
                @php $statusSaat = old('status_pembinaan', $pelanggaran->status_pembinaan); @endphp
                <div id="infoStatus">
                    @if($statusSaat === 'Belum Ditindak')
                        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                            Siswa belum mendapatkan pembinaan dari guru BK.
                        </div>
                    @elseif($statusSaat === 'Dalam Proses')
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-4 py-3 rounded-xl">
                            Pembinaan sedang berlangsung / pemanggilan sedang diproses.
                        </div>
                    @elseif($statusSaat === 'Selesai')
                        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
                            Siswa sudah mendapatkan pembinaan dari guru BK.
                        </div>
                    @endif
                </div>

                {{-- ── TANGGAL PEMBINAAN ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Tanggal Pembinaan</label>
                    <input type="datetime-local"
                           name="tanggal_pembinaan"
                           value="{{ old('tanggal_pembinaan', $pelanggaran->tanggal_pembinaan
                               ? \Carbon\Carbon::parse($pelanggaran->tanggal_pembinaan)->format('Y-m-d\TH:i')
                               : '') }}"
                           class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                  border border-gray-200 bg-gray-50 text-black
                                  focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                </div>

                {{-- ── WAKTU KEJADIAN ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Waktu Kejadian</label>
                    <input type="datetime-local"
                           name="waktu_kejadian"
                           value="{{ old('waktu_kejadian', optional(\Carbon\Carbon::parse($pelanggaran->waktu_kejadian))->format('Y-m-d\TH:i')) }}"
                           class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                  border border-gray-200 bg-gray-50 text-black
                                  focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                </div>

                {{-- ── DESKRIPSI ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Deskripsi Pelanggaran</label>
                    <textarea name="deskripsi" rows="4"
                              class="mt-1 w-full px-3 py-3 text-sm rounded-xl
                                     border border-gray-200 bg-gray-50 text-black
                                     focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 resize-none">{{ old('deskripsi', $pelanggaran->deskripsi) }}</textarea>
                </div>

                {{-- ── CATATAN BK ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Catatan BK</label>
                    <textarea name="catatan_bk" rows="4"
                              placeholder="Contoh: siswa dipanggil, diberikan arahan, membuat surat pernyataan, dll"
                              class="mt-1 w-full px-3 py-3 text-sm rounded-xl
                                     border border-gray-200 bg-gray-50 text-black
                                     focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 resize-none">{{ old('catatan_bk', $pelanggaran->catatan_bk) }}</textarea>
                </div>

                {{-- ── BUTTON ── --}}
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit"
                            class="bg-[#0D2D6B] text-white px-5 py-2.5 text-sm
                                   rounded-xl hover:bg-[#163580] transition">
                        Update
                    </button>
                    <a href="{{ route('pelanggaran.index') }}"
                       class="bg-white border border-gray-300 text-gray-600
                              px-5 py-2.5 text-sm rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        // ── Auto-fill wali kelas saat siswa dipilih ──
        const siswaSelect    = document.getElementById('id_siswa');
        const waliKelasInput = document.getElementById('id_walikelas');
        const namaWaliKelas  = document.getElementById('nama_walikelas');

        siswaSelect.addEventListener('change', function () {
            const selected       = siswaSelect.options[siswaSelect.selectedIndex];
            waliKelasInput.value = selected.getAttribute('data-walikelas') || '';
            namaWaliKelas.value  = selected.getAttribute('data-namawalikelas') || '-';
        });

        // ── Update info status secara dinamis ──
        function updateInfoStatus(nilai) {
            const map = {
                'Belum Ditindak': `
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                        Siswa belum mendapatkan pembinaan dari guru BK.
                    </div>`,
                'Dalam Proses': `
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-4 py-3 rounded-xl">
                        Pembinaan sedang berlangsung / pemanggilan sedang diproses.
                    </div>`,
                'Selesai': `
                    <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
                        Siswa sudah mendapatkan pembinaan dari guru BK.
                    </div>`,
            };
            document.getElementById('infoStatus').innerHTML = map[nilai] || '';
        }
    </script>

</x-app-layout>