<div x-data class="space-y-3">

    {{-- SUCCESS NOTIFICATION --}}
    @if (session()->has('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200
                   text-green-700 px-3 py-2 rounded-xl shadow-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-1">
            <div class="simdis-card">

                <h2 class="simdis-title">{{ $editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                <p class="simdis-subtitle">Kelola data pengguna SIMDIS.</p>

                <form wire:submit.prevent="save" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <div>
                            <label class="simdis-label">Nama</label>
                            <input type="text" wire:model="name" placeholder="Masukkan nama" class="simdis-input">
                            @error('name') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="simdis-label">Username</label>
                            <input type="text" wire:model="username" placeholder="Masukkan username" class="simdis-input">
                            @error('username') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="simdis-label">Email</label>
                            <input type="email" wire:model="email" placeholder="Email opsional" class="simdis-input">
                            @error('email') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="simdis-label">Nomor Telepon</label>
                            <input type="text" wire:model="no_telpon" placeholder="08xxxxxxxxxx" class="simdis-input">
                            @error('no_telpon') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="simdis-label">Role</label>
                            <select wire:model.live="id_role" class="simdis-select">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                            @error('id_role') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="simdis-label">Password</label>
                            <input type="password" wire:model="password"
                                placeholder="{{ $editingId ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                class="simdis-input">
                            @error('password') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    {{-- DETAIL GURU --}}
                    @if (in_array($this->selectedRoleName, ['guru_bk', 'wali_kelas']))
                        <div class="pt-3 border-t">
                            <h4 class="simdis-section-title">Detail Guru / Wali Kelas</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="simdis-label">NUPTK</label>
                                    <input type="text" wire:model="nuptk" placeholder="Masukkan NUPTK" class="simdis-input">
                                    @error('nuptk') <div class="simdis-error">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="simdis-label">Jabatan</label>
                                    <input type="text" wire:model="jabatan" placeholder="Masukkan jabatan" class="simdis-input">
                                    @error('jabatan') <div class="simdis-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- DETAIL ORTU --}}
                    @if ($this->selectedRoleName === 'orang_tua')
                        <div class="pt-3 border-t">
                            <h4 class="simdis-section-title">Detail Wali Murid</h4>
                            <input type="text" wire:model="hubungan" placeholder="Ayah / Ibu / Wali" class="simdis-input">
                            @error('hubungan') <div class="simdis-error">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    {{-- BUTTON --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-1">
                        <button type="submit" class="simdis-btn-primary">
                            {{ $editingId ? 'Update Pengguna' : 'Simpan Pengguna' }}
                        </button>
                        <button type="button" wire:click="resetForm" class="simdis-btn-secondary">
                            {{ $editingId ? 'Batal' : 'Reset' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="xl:col-span-2">
            <div class="simdis-card">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="simdis-title">
                            {{ $showTrash ? 'Tong Sampah' : 'Daftar Pengguna' }}
                        </h2>
                        <p class="simdis-subtitle mb-0">
                            {{ $showTrash ? 'Pengguna yang telah dihapus.' : 'Data seluruh pengguna sistem SIMDIS.' }}
                        </p>
                    </div>

                    {{-- Toggle trash --}}
                    <button wire:click="$toggle('showTrash')"
                        class="flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                               {{ $showTrash
                                   ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'
                                   : 'bg-[#F0F4FB] text-[#4A5E8A] border-[#E2EAF4] hover:bg-[#e2eaf7]' }}">
                        <i class="fas {{ $showTrash ? 'fa-arrow-left' : 'fa-trash-can' }}"></i>
                        {{ $showTrash ? 'Kembali' : 'Tong Sampah' }}
                        @if (!$showTrash && $trashCount > 0)
                            <span class="ml-1 bg-red-500 text-white text-[10px] font-bold
                                         px-1.5 py-0.5 rounded-full leading-none">
                                {{ $trashCount }}
                            </span>
                        @endif
                    </button>
                </div>

                {{-- ── TOOLBAR ── --}}
                @if (!$showTrash)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-3">

                        {{-- Search --}}
                        <div class="lg:col-span-2 relative">
                            <input type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Cari nama atau username..."
                                class="simdis-input pl-8"
                                style="height:36px; font-size:12px;">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2
                                      text-[#9AAAC4] text-xs pointer-events-none"></i>
                            @if($search)
                                <button wire:click="$set('search','')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-xmark text-xs"></i>
                                </button>
                            @endif
                        </div>

                        {{-- Filter Role --}}
                        <div>
                            <select wire:model.live="filterRole" class="simdis-select" style="height:36px;font-size:12px;">
                                <option value="">Semua Role</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort + Per page --}}
                        <div class="flex gap-2">
                            <select wire:model.live="sortBy" class="simdis-select flex-1" style="height:36px;font-size:12px;">
                                <option value="terbaru">Terbaru</option>
                                <option value="az">A → Z</option>
                                <option value="za">Z → A</option>
                            </select>
                            <select wire:model.live="perPage" class="simdis-select" style="height:36px;font-size:12px;width:70px;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                    </div>
                @else
                    {{-- Trash: tombol kosongkan --}}
                    @if($trashCount > 0)
                        <div class="flex justify-end mb-3">
                            <button wire:click="emptyTrash"
                                wire:confirm="Hapus SEMUA data di tong sampah secara permanen? Tidak bisa dikembalikan."
                                class="flex items-center gap-1 text-xs font-semibold px-3 py-1.5
                                       bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash"></i> Kosongkan Semua
                            </button>
                        </div>
                    @endif
                @endif

                {{-- ── TABLE ── --}}
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="simdis-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                @php $isEditing = !$showTrash && $editingId == $u->id_pengguna; @endphp
                                <tr wire:key="user-{{ $u->id_pengguna }}"
                                    style="{{ $isEditing ? 'background:rgba(245,184,0,0.07);outline:1.5px solid rgba(245,184,0,0.35);outline-offset:-1px;' : '' }}">

                                    <td style="color:#4A5E8A;font-size:12px;">{{ $u->id_pengguna }}</td>

                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div style="width:26px;height:26px;border-radius:50%;
                                                        background:linear-gradient(135deg,#0D2D6B,#163580);
                                                        color:#F5B800;font-size:11px;font-weight:700;
                                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                            <span style="font-weight:600;color:#0D2D6B;font-size:13px;">
                                                {{ $u->name }}
                                                @if($u->trashed())
                                                    <span style="font-size:10px;color:#DC2626;font-weight:600;
                                                                 background:rgba(229,62,62,0.08);padding:1px 6px;
                                                                 border-radius:20px;margin-left:4px;">Dihapus</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    <td style="color:#4A5E8A;font-size:12px;">{{ $u->username }}</td>

                                    <td>
                                        <span class="badge-role">{{ optional($u->role)->nama_role ?? '-' }}</span>
                                    </td>

                                    <td class="whitespace-nowrap">
                                        @if ($showTrash)
                                            <button wire:click="restoreUser({{ $u->id_pengguna }})"
                                                class="action-btn mr-2" style="color:#276749;">
                                                <i class="fas fa-rotate-left"></i> Pulihkan
                                            </button>
                                            <button wire:click="forceDeleteUser({{ $u->id_pengguna }})"
                                                wire:confirm="Hapus permanen? Data tidak bisa dikembalikan."
                                                class="action-btn action-delete">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        @else
                                            <button wire:click="editUser({{ $u->id_pengguna }})"
                                                class="action-btn action-edit mr-3">
                                                {{ $isEditing ? '✎ Diedit' : 'Edit' }}
                                            </button>
                                            @if (!$isEditing)
                                                <button wire:click="deleteUser({{ $u->id_pengguna }})"
                                                    wire:confirm="Pindahkan pengguna ini ke tong sampah?"
                                                    class="action-btn action-delete">
                                                    Hapus
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8" style="color:#9AAAC4;font-size:13px;">
                                        <i class="fas fa-{{ $showTrash ? 'trash' : 'users-slash' }} block text-2xl mb-2 opacity-25"></i>
                                        {{ $showTrash ? 'Tong sampah kosong.' : 'Tidak ada pengguna ditemukan.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ── PAGINATION ── --}}
                @if ($users->hasPages())
                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                        <span style="font-size:12px;color:#4A5E8A;">
                            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
                            dari {{ $users->total() }} pengguna
                        </span>
                        <div class="flex items-center gap-1">
                            {{-- Prev --}}
                            @if ($users->onFirstPage())
                                <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <button wire:click="previousPage" class="simdis-page-btn">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                            @endif

                            {{-- Nomor halaman --}}
                            @foreach ($users->getUrlRange(
                                max(1, $users->currentPage() - 2),
                                min($users->lastPage(), $users->currentPage() + 2)
                            ) as $page => $url)
                                @if ($page == $users->currentPage())
                                    <span class="simdis-page-btn simdis-page-active">{{ $page }}</span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" class="simdis-page-btn">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($users->hasMorePages())
                                <button wire:click="nextPage" class="simdis-page-btn">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            @else
                                <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mt-2" style="font-size:11px;color:#9AAAC4;">
                        Total {{ $users->total() }} pengguna
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>