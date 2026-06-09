<x-app-layout>

    <div class="max-w-5xl mx-auto p-4">

        {{-- HEADER --}}
        <div class="mb-4">
            <h1 class="text-[20px] font-bold text-[#0D2D6B]">Edit Pelanggaran</h1>
            <p class="text-xs text-gray-500 mt-1">Perbarui data pelanggaran siswa SIMDIS</p>
        </div>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CARD FORM --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">

            <form action="{{ route('pelanggaran.update', $pelanggaran->id_pelanggaran) }}" method="POST"
                id="form_pelanggaran" class="space-y-4">

                @csrf
                @method('PUT')

                {{-- ── SISWA ── --}}
                {{-- ── SISWA ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Siswa</label>
                    <select name="id_siswa" id="id_siswa">
                        <option value="{{ $pelanggaran->siswa->id_siswa }}"
                            data-walikelas="{{ $pelanggaran->siswa->kelas?->waliKelas?->id_walikelas ?? '' }}"
                            data-namawalikelas="{{ $pelanggaran->siswa->kelas?->waliKelas?->pengguna?->name ?? '-' }}"
                            selected>
                            {{ $pelanggaran->siswa->nama }} — {{ $pelanggaran->siswa->nis }}
                        </option>
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
                        @foreach ($jenisPelanggaran as $j)
                            <option value="{{ $j->id_jenispelanggaran }}"
                                {{ old('id_jenispelanggaran', $pelanggaran->id_jenispelanggaran) == $j->id_jenispelanggaran ? 'selected' : '' }}>
                                {{ $j->nama_pelanggaran }} ({{ ucfirst($j->tingkat_pelanggaran) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ── STATUS PEMBINAAN ── --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Status Pembinaan</label>
                    <select name="status_pembinaan" id="status_pembinaan" onchange="updateInfoStatus(this.value)"
                        class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
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

                {{-- ── INFO STATUS ── --}}
                @php $statusSaat = old('status_pembinaan', $pelanggaran->status_pembinaan); @endphp
                <div id="infoStatus">
                    @if ($statusSaat === 'Belum Ditindak')
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
                    <input type="date" name="tanggal_pembinaan"
                        value="{{ old(
                            'tanggal_pembinaan',
                            $pelanggaran->tanggal_pembinaan ? \Carbon\Carbon::parse($pelanggaran->tanggal_pembinaan)->format('Y-m-d') : '',
                        ) }}"
                        class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                  border border-gray-200 bg-gray-50 text-black
                                  focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                </div>

                {{-- ── JAM PEMBINAAN ── --}}
                @php
                    $jamRaw = $pelanggaran->getRawOriginal('jam_pembinaan');
                    $jamParts = $jamRaw ? explode(':', substr($jamRaw, 0, 5)) : ['', ''];
                    $jamH = old('jam_pembinaan_hour', $jamParts[0] ?? '');
                    $jamM = old('jam_pembinaan_minute', $jamParts[1] ?? '');
                @endphp
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Jam Pembinaan</label>
                    <div class="flex items-center gap-2 mt-1">
                        <select name="jam_pembinaan_hour"
                            class="flex-1 h-11 px-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-black
                                       focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="">Jam</option>
                            @for ($h = 6; $h <= 18; $h++)
                                @php $val = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $val }}" {{ $jamH === $val ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endfor
                        </select>

                        <span class="text-gray-400 font-bold text-lg">:</span>

                        <select name="jam_pembinaan_minute"
                            class="flex-1 h-11 px-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-black
                                       focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="">Menit</option>
                            @foreach (['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55'] as $m)
                                <option value="{{ $m }}" {{ $jamM === $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika jam pembinaan belum diketahui</p>
                </div>

                {{-- ── WAKTU KEJADIAN ── --}}
                @php
                    $wk = \Carbon\Carbon::parse($pelanggaran->waktu_kejadian);
                    $wkTgl = old('waktu_kejadian_date', $wk->format('Y-m-d'));
                    $wkH = old('waktu_kejadian_hour', $wk->format('H'));
                    $wkM = old('waktu_kejadian_minute', $wk->format('i'));
                    $wkM = str_pad((int) (round((int) $wkM / 5) * 5), 2, '0', STR_PAD_LEFT);
                    $wkH = str_pad((int) $wkH, 2, '0', STR_PAD_LEFT);
                @endphp
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Waktu Kejadian</label>
                    <input type="date" name="waktu_kejadian_date" value="{{ $wkTgl }}"
                        class="mt-1 w-full h-11 px-3 text-sm rounded-xl
                                  border border-gray-200 bg-gray-50 text-black
                                  focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">

                    <div class="flex items-center gap-2 mt-2">
                        <select name="waktu_kejadian_hour"
                            class="flex-1 h-11 px-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-black
                                       focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="">Jam</option>
                            @for ($h = 6; $h <= 18; $h++)
                                @php $val = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $val }}" {{ $wkH === $val ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endfor
                        </select>

                        <span class="text-gray-400 font-bold text-lg">:</span>

                        <select name="waktu_kejadian_minute"
                            class="flex-1 h-11 px-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-black
                                       focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20">
                            <option value="">Menit</option>
                            @foreach (['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55'] as $m)
                                <option value="{{ $m }}" {{ $wkM === $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
                    <button type="submit" id="btn-simpan"
                        class="inline-flex items-center gap-2 bg-[#0D2D6B] text-white px-5 py-2.5 text-sm
                               rounded-xl hover:bg-[#163580] transition disabled:opacity-75 disabled:cursor-not-allowed">
                        <span id="btn-simpan-text">Update</span>
                        <span id="btn-simpan-loading" style="display:none; align-items:center; gap:6px;">
                            <span class="simdis-btn-spinner"></span>
                            Menyimpan...
                        </span>
                    </button>

                    {{-- ++ TAMBAHAN: onclick showBatalLoading() pada tombol Batal ++ --}}
                    <a href="{{ route('pelanggaran.index') }}" id="btn-batal" onclick="showBatalLoading(event)"
                        class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-600
                               px-5 py-2.5 text-sm rounded-xl hover:bg-gray-50 transition">
                        <span id="batal-text">Batal</span>
                        <span id="batal-loading" style="display:none; align-items:center; gap:6px;">
                            <span
                                style="
                                display:inline-block; width:13px; height:13px;
                                border:2px solid #d1d5db;
                                border-top-color:#6b7280;
                                border-radius:50%;
                                animation:simdis-spin .7s linear infinite;">
                            </span>
                            Kembali...
                        </span>
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        // ── FORM SUBMIT LOADING ──────────────────────────────────────────
        document.getElementById('form_pelanggaran').addEventListener('submit', function() {
            const btn = document.getElementById('btn-simpan');
            const text = document.getElementById('btn-simpan-text');
            const loading = document.getElementById('btn-simpan-loading');
            btn.disabled = true;
            text.style.display = 'none';
            loading.style.display = 'inline-flex';
        });

        // ── REFERENSI ELEMEN WALI KELAS ──────────────────────────────────
        const waliKelasInput = document.getElementById('id_walikelas');
        const namaWaliKelas = document.getElementById('nama_walikelas');

        // ── INIT TOMSELECT (AJAX) ────────────────────────────────────────
        function initTomSelect() {
            if (window._tomSiswa) {
                try {
                    window._tomSiswa.destroy();
                } catch (e) {}
                window._tomSiswa = null;
            }

            const el = document.getElementById('id_siswa');
            if (!el || el.tomselect) return;

            window._tomSiswa = new TomSelect('#id_siswa', {
                create: false,
                placeholder: 'Cari nama siswa atau NIS...',
                valueField: 'id',
                labelField: 'text',
                searchField: ['text'],
                preload: false,
                // ✅ Pre-load siswa yang sedang diedit
                options: [{
                    id: '{{ $pelanggaran->siswa->id_siswa }}',
                    text: '{{ addslashes($pelanggaran->siswa->nama) }} — {{ $pelanggaran->siswa->nis }}',
                    kelas: '{{ addslashes($pelanggaran->siswa->kelas?->nama_kelas ?? '') }}',
                    wali: '{{ addslashes($pelanggaran->siswa->kelas?->waliKelas?->pengguna?->name ?? '-') }}',
                    idwalikelas: '{{ $pelanggaran->siswa->kelas?->waliKelas?->id_walikelas ?? '' }}',
                }],
                items: ['{{ $pelanggaran->siswa->id_siswa }}'],
                load: function(query, callback) {
                    if (query.length < 2) return callback();
                    fetch('{{ route('siswa.search') }}?q=' + encodeURIComponent(query), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => callback(data))
                        .catch(() => callback());
                },
                onChange: function(value) {
                    const item = this.options[value];
                    if (!item) {
                        waliKelasInput.value = '';
                        namaWaliKelas.value = '-';
                        return;
                    }
                    waliKelasInput.value = item.idwalikelas || '';
                    namaWaliKelas.value = item.wali || '-';
                },
                render: {
                    option: data => `<div class="py-1">${data.text}</div>`,
                    item: data => `<div>${data.text}</div>`,
                    no_results: () => `<div class="py-2 px-3 text-sm text-gray-400">Siswa tidak ditemukan</div>`,
                    loading: () => `<div class="py-2 px-3 text-sm text-gray-400">Mencari...</div>`,
                },
            });
        }

        initTomSelect();

        document.addEventListener('livewire:navigated', function() {
            if (document.getElementById('id_siswa')) initTomSelect();
        });

        // ── UPDATE INFO STATUS ───────────────────────────────────────────
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

        // ── LOADING BATAL ────────────────────────────────────────────────
        function showBatalLoading(e) {
            document.getElementById('batal-text').style.display = 'none';
            document.getElementById('batal-loading').style.display = 'inline-flex';
            const btn = document.getElementById('btn-batal');
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.75';
        }
    </script>

    <style>
        @keyframes simdis-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

</x-app-layout>
