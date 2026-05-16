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
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- CARD --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">

            <form action="{{ route('pelanggaran.store') }}" method="POST" id="form_pelanggaran" class="space-y-3">

                @csrf


                {{-- SISWA --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Siswa</label>
                    <select name="id_siswa" id="id_siswa"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
               border border-gray-200 bg-gray-50 text-black
               focus:bg-white focus:border-[#F5B800]
               focus:ring-2 focus:ring-[#F5B800]/20">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->id_siswa }}"
                                data-kelas="{{ $s->kelas->nama_kelas ?? '' }}"
                                data-wali="{{ $s->kelas?->waliKelas?->pengguna?->name ?? '-' }}"
                                data-idwalikelas="{{ $s->kelas?->waliKelas?->id_walikelas ?? '' }}"
                                {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>
                                {{ $s->nama }} - {{ $s->nis }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- KELAS --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Kelas</label>
                    <input type="text" id="nama_kelas" readonly
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
               border border-gray-200 bg-gray-100 text-black">
                </div>


                {{-- WALI KELAS --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Wali Kelas</label>
                    <input type="text" id="nama_walikelas" readonly
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg
                               border border-gray-200 bg-gray-100 text-black">
                    <input type="hidden" name="id_walikelas" id="id_walikelas">
                </div>


                {{-- JENIS PELANGGARAN — custom dropdown --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Jenis Pelanggaran</label>

                    {{-- Hidden input yang dikirim ke server --}}
                    <input type="hidden" name="id_jenispelanggaran" id="id_jenispelanggaran_hidden"
                        value="{{ old('id_jenispelanggaran') }}">

                    <div class="relative mt-0.5" id="custom_dropdown_wrap">

                        {{-- Trigger --}}
                        <button type="button" id="dropdown_trigger"
                            class="w-full h-10 px-3 text-sm rounded-lg text-left
                                       border border-gray-200 bg-gray-50 text-black
                                       focus:outline-none focus:bg-white focus:border-[#F5B800]
                                       focus:ring-2 focus:ring-[#F5B800]/20
                                       flex items-center justify-between gap-2">
                            <span id="dropdown_label" class="flex items-center gap-2 flex-1 min-w-0">
                                <span class="text-gray-400 truncate">-- Pilih Jenis Pelanggaran --</span>
                            </span>
                            <svg id="dropdown_chevron"
                                class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- List --}}
                        <div id="dropdown_list"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200
                                    rounded-xl shadow-lg overflow-hidden hidden">
                            <div class="max-h-60 overflow-y-auto divide-y divide-gray-50">

                                {{-- Placeholder --}}
                                <div class="dropdown-item px-3 py-2.5 text-sm text-gray-400
                                            cursor-pointer hover:bg-gray-50"
                                    data-value="" data-tingkat="" data-nama="">
                                    -- Pilih Jenis Pelanggaran --
                                </div>

                                @foreach ($jenisPelanggaran as $j)
                                    <div class="dropdown-item px-3 py-2.5 text-sm text-black
                                                cursor-pointer hover:bg-gray-50
                                                flex items-center justify-between gap-3"
                                        data-value="{{ $j->id_jenispelanggaran }}"
                                        data-tingkat="{{ $j->tingkat_pelanggaran }}"
                                        data-nama="{{ $j->nama_pelanggaran }}">

                                        <span class="flex-1 min-w-0 truncate">{{ $j->nama_pelanggaran }}</span>

                                        @if ($j->tingkat_pelanggaran === 'ringan')
                                            <span
                                                class="flex-shrink-0 inline-flex items-center
                                                         px-2 py-0.5 rounded-full
                                                         text-[10px] font-semibold
                                                         bg-green-100 text-green-800">
                                                Ringan
                                            </span>
                                        @elseif($j->tingkat_pelanggaran === 'sedang')
                                            <span
                                                class="flex-shrink-0 inline-flex items-center
                                                         px-2 py-0.5 rounded-full
                                                         text-[10px] font-semibold
                                                         bg-yellow-100 text-yellow-800">
                                                Sedang
                                            </span>
                                        @elseif($j->tingkat_pelanggaran === 'berat')
                                            <span
                                                class="flex-shrink-0 inline-flex items-center
                                                         px-2 py-0.5 rounded-full
                                                         text-[10px] font-semibold
                                                         bg-red-100 text-red-800">
                                                Berat
                                            </span>
                                        @endif
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>

                    {{-- Badge terpilih di bawah dropdown --}}
                    <div id="badge_wrap" class="mt-1.5 hidden">
                        <span id="badge_label"
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5
                                     rounded-full text-xs font-semibold">
                        </span>
                    </div>
                </div>


                {{-- WAKTU KEJADIAN --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Waktu Kejadian</label>

                    {{-- Hidden field dikirim ke server --}}
                    <input type="hidden" name="waktu_kejadian" id="waktu_kejadian_hidden">

                    <div class="mt-0.5 flex items-center gap-2">

                        {{-- Tanggal --}}
                        <input type="date" id="wkt_tanggal"
                            class="flex-1 h-10 px-3 text-sm rounded-lg
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800]
                                   focus:ring-2 focus:ring-[#F5B800]/20">

                        <span class="text-gray-300 text-lg font-light select-none">|</span>

                        {{-- Jam --}}
                        <select id="wkt_jam"
                            class="w-[72px] h-10 px-2 text-sm text-center rounded-lg
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800]
                                   focus:ring-2 focus:ring-[#F5B800]/20">
                            @for ($h = 0; $h < 24; $h++)
                                <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">
                                    {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>

                        <span class="text-gray-500 font-bold text-base select-none">:</span>

                        {{-- Menit --}}
                        <select id="wkt_menit"
                            class="w-[72px] h-10 px-2 text-sm text-center rounded-lg
                                   border border-gray-200 bg-gray-50 text-black
                                   focus:bg-white focus:border-[#F5B800]
                                   focus:ring-2 focus:ring-[#F5B800]/20">
                            @for ($m = 0; $m < 60; $m += 5)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>

                    </div>

                    <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-3">
                        <span>📅 Tanggal</span>
                        <span>🕐 Jam</span>
                        <span>⏱ Menit</span>
                    </p>
                </div>


                {{-- DESKRIPSI --}}
                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Deskripsi</label>
                    <textarea name="deskripsi" rows="4"
                        class="mt-0.5 w-full px-3 py-2 text-sm rounded-lg
                               border border-gray-200 bg-gray-50 text-black
                               focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20 resize-none">{{ old('deskripsi') }}</textarea>
                </div>


                {{-- BUTTON --}}
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit"
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


    <script>
        // ────────────────────────────────────────────────────────────
        // SISWA → KELAS & WALI KELAS
        // ────────────────────────────────────────────────────────────
        const siswaSelect = document.getElementById('id_siswa');
        const namaWali = document.getElementById('nama_walikelas');
        const idWali = document.getElementById('id_walikelas');
        const namaKelas = document.getElementById('nama_kelas');

        function updateDataSiswa() {
            const sel = siswaSelect.options[siswaSelect.selectedIndex];
            namaWali.value = sel.getAttribute('data-wali') || '';
            idWali.value = sel.getAttribute('data-idwalikelas') || '';
            namaKelas.value = (sel.getAttribute('data-kelas') || '').trim();
        }

        siswaSelect.addEventListener('change', updateDataSiswa);


        // ────────────────────────────────────────────────────────────
        // CUSTOM DROPDOWN JENIS PELANGGARAN
        // ────────────────────────────────────────────────────────────
        const trigger = document.getElementById('dropdown_trigger');
        const dropLabel = document.getElementById('dropdown_label');
        const dropList = document.getElementById('dropdown_list');
        const dropChevron = document.getElementById('dropdown_chevron');
        const dropWrap = document.getElementById('custom_dropdown_wrap');
        const hiddenJenis = document.getElementById('id_jenispelanggaran_hidden');
        const badgeWrap = document.getElementById('badge_wrap');
        const badgeLabel = document.getElementById('badge_label');

        // Config badge per tingkat (nilai enum lowercase sesuai migration)
        const badgeCfg = {
            ringan: {
                text: '● Ringan',
                cls: 'bg-green-100 text-green-800',
                badgeHtml: '<span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800">Ringan</span>'
            },
            sedang: {
                text: '● Sedang',
                cls: 'bg-yellow-100 text-yellow-800',
                badgeHtml: '<span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800">Sedang</span>'
            },
            berat: {
                text: '● Berat',
                cls: 'bg-red-100 text-red-800',
                badgeHtml: '<span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800">Berat</span>'
            },
        };

        function openDropdown() {
            dropList.classList.remove('hidden');
            dropChevron.style.transform = 'rotate(180deg)';
        }

        function closeDropdown() {
            dropList.classList.add('hidden');
            dropChevron.style.transform = 'rotate(0deg)';
        }

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropList.classList.contains('hidden') ? openDropdown() : closeDropdown();
        });

        document.addEventListener('click', function(e) {
            if (!dropWrap.contains(e.target)) closeDropdown();
        });

        function selectItem(item) {
            const val = item.getAttribute('data-value');
            const tingkat = item.getAttribute('data-tingkat');
            const nama = item.getAttribute('data-nama') || '';

            hiddenJenis.value = val;

            if (!val) {
                dropLabel.innerHTML = '<span class="text-gray-400 truncate">-- Pilih Jenis Pelanggaran --</span>';
                badgeWrap.classList.add('hidden');
            } else {
                const cfg = badgeCfg[tingkat];
                dropLabel.innerHTML =
                    '<span class="flex-1 min-w-0 truncate">' + nama + '</span>' +
                    (cfg ? cfg.badgeHtml : '');

                if (cfg) {
                    badgeLabel.textContent = cfg.text;
                    badgeLabel.className =
                        'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold ' + cfg.cls;
                    badgeWrap.classList.remove('hidden');
                } else {
                    badgeWrap.classList.add('hidden');
                }
            }

            closeDropdown();
        }

        document.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                selectItem(this);
            });
        });

        // Restore nilai old() jika ada (misalnya setelah validasi gagal)
        (function() {
            const oldVal = hiddenJenis.value;
            if (!oldVal) return;
            const match = document.querySelector('.dropdown-item[data-value="' + oldVal + '"]');
            if (match) selectItem(match);
        })();


        // ────────────────────────────────────────────────────────────
        // WAKTU KEJADIAN — gabung sebelum submit
        // ────────────────────────────────────────────────────────────
        const wktTanggal = document.getElementById('wkt_tanggal');
        const wktJam = document.getElementById('wkt_jam');
        const wktMenit = document.getElementById('wkt_menit');
        const wktHidden = document.getElementById('waktu_kejadian_hidden');

        @if (old('waktu_kejadian'))
            (function() {
                try {
                    const oldVal = '{{ old('waktu_kejadian') }}';
                    const dt = new Date(oldVal);
                    if (!isNaN(dt.getTime())) {
                        wktTanggal.value = dt.toISOString().slice(0, 10);
                        wktJam.value = String(dt.getHours()).padStart(2, '0');
                        const snap = Math.round(dt.getMinutes() / 5) * 5 % 60;
                        wktMenit.value = String(snap).padStart(2, '0');
                    }
                } catch (e) {
                    /* silent */ }
            })();
        @endif

        document.getElementById('form_pelanggaran').addEventListener('submit', function() {
            if (wktTanggal.value) {
                wktHidden.value = wktTanggal.value + 'T' + wktJam.value + ':' + wktMenit.value;
            }
        });


        // ────────────────────────────────────────────────────────────
        // INIT
        // ────────────────────────────────────────────────────────────
        window.addEventListener('load', updateDataSiswa);
    </script>

</x-app-layout>
