<div>
    <style>
        .jp-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .jp-header-title h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0D2D6B;
            margin: 0 0 0.2rem;
        }

        .jp-header-title p {
            font-size: 0.82rem;
            color: #4A5E8A;
            margin: 0;
        }

        .jp-header-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .jp-toolbar {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .jp-search-wrap {
            position: relative;
            flex: 1;
            min-width: 180px;
        }

        .jp-search-wrap i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #8da0c0;
            font-size: 0.8rem;
            pointer-events: none;
        }

        .jp-search-input {
            width: 100%;
            padding: 0.58rem 0.85rem 0.58rem 2.1rem;
            border: 1.5px solid #d1d9ea;
            border-radius: 8px;
            font-size: 0.84rem;
            color: #1e3a6e;
            background: #fff;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .jp-search-input:focus {
            border-color: #0D2D6B;
            box-shadow: 0 0 0 3px rgba(13, 45, 107, 0.1);
        }

        .jp-filter-select,
        .jp-perpage-select {
            padding: 0.58rem 0.85rem;
            border: 1.5px solid #d1d9ea;
            border-radius: 8px;
            font-size: 0.84rem;
            color: #1e3a6e;
            background: #fff;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .jp-filter-select:focus,
        .jp-perpage-select:focus {
            border-color: #0D2D6B;
            box-shadow: 0 0 0 3px rgba(13, 45, 107, 0.1);
        }

        .jp-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, #F5B800, #C99800);
            color: #091E4A;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(245, 184, 0, 0.35);
            transition: transform 0.15s, box-shadow 0.15s;
            white-space: nowrap;
        }

        .jp-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(245, 184, 0, 0.45);
        }

        .jp-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: transparent;
            color: #4A5E8A;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            border: 1.5px solid #d1d9ea;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
        }

        .jp-btn-secondary:hover {
            background: #F0F4FB;
            color: #0D2D6B;
        }

        .jp-btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(239, 68, 68, 0.3);
            transition: transform 0.15s;
            white-space: nowrap;
        }

        .jp-btn-danger:hover {
            transform: translateY(-1px);
        }

        .jp-btn-trash {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: transparent;
            color: #dc2626;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            border: 1.5px solid #fca5a5;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
        }

        .jp-btn-trash:hover {
            background: #fee2e2;
        }

        .jp-btn-trash.active {
            background: #fee2e2;
            color: #991b1b;
            border-color: #f87171;
        }

        .jp-trash-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 20px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            line-height: 1;
        }

        .jp-flash {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            font-size: 0.83rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            animation: jp-fadein 0.3s ease;
        }

        @keyframes jp-fadein {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .jp-trash-banner {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .jp-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(13, 45, 107, 0.08);
            overflow: hidden;
            border: 1px solid rgba(13, 45, 107, 0.07);
        }

        .jp-table {
            width: 100%;
            border-collapse: collapse;
        }

        .jp-table thead {
            background: linear-gradient(90deg, #0D2D6B 0%, #163580 100%);
        }

        .jp-table thead th {
            padding: 0.85rem 1rem;
            text-align: left;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
        }

        .jp-table thead th:first-child {
            width: 50px;
            text-align: center;
        }

        .jp-table thead th:last-child {
            text-align: center;
            width: 150px;
        }

        .jp-table tbody tr {
            border-bottom: 1px solid #f0f4fb;
            transition: background 0.15s;
        }

        .jp-table tbody tr:last-child {
            border-bottom: none;
        }

        .jp-table tbody tr:hover {
            background: #f7f9fd;
        }

        .jp-table tbody td {
            padding: 0.85rem 1rem;
            font-size: 0.85rem;
            color: #1e3a6e;
        }

        .jp-table tbody td:first-child {
            text-align: center;
            font-weight: 600;
            color: #4A5E8A;
            font-size: 0.78rem;
        }

        .jp-table tbody td:last-child {
            text-align: center;
        }

        .jp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.28rem 0.7rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .jp-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .jp-badge-ringan {
            background: #fef9c3;
            color: #854d0e;
        }

        .jp-badge-sedang {
            background: #fef3c7;
            color: #b45309;
        }

        .jp-badge-berat {
            background: #fee2e2;
            color: #991b1b;
        }

        .jp-badge-aktif {
            background: #dcfce7;
            color: #15803d;
        }

        .jp-badge-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .jp-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .jp-icon-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: transform 0.15s, background 0.15s;
        }

        .jp-icon-btn:hover {
            transform: scale(1.12);
        }

        .jp-icon-btn.edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .jp-icon-btn.delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .jp-icon-btn.restore {
            background: #dcfce7;
            color: #15803d;
        }

        .jp-icon-btn.destroy {
            background: #fee2e2;
            color: #991b1b;
        }

        .jp-icon-btn.aktifkan {
            background: #dcfce7;
            color: #15803d;
        }

        .jp-icon-btn.nonaktif {
            background: #fef9c3;
            color: #854d0e;
        }

        .jp-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #4A5E8A;
        }

        .jp-empty i {
            font-size: 2.5rem;
            opacity: 0.3;
            margin-bottom: 0.75rem;
            display: block;
        }

        .jp-empty p {
            font-size: 0.85rem;
            margin: 0;
        }

        .jp-pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-top: 1px solid #f0f4fb;
            font-size: 0.8rem;
            color: #4A5E8A;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .jp-overlay {
            position: fixed;
            inset: 0;
            background: rgba(9, 30, 74, 0.55);
            backdrop-filter: blur(3px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: jp-fadein 0.2s ease;
        }

        .jp-modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(9, 30, 74, 0.25);
            overflow: hidden;
            animation: jp-slideup 0.25s ease;
        }

        .jp-modal-sm {
            max-width: 370px;
        }

        @keyframes jp-slideup {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .jp-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.4rem 0.9rem;
            border-bottom: 1px solid #f0f4fb;
            background: linear-gradient(90deg, #0D2D6B, #163580);
        }

        .jp-modal-header.danger {
            background: linear-gradient(90deg, #991b1b, #dc2626);
        }

        .jp-modal-header.warning {
            background: linear-gradient(90deg, #92400e, #b45309);
        }

        .jp-modal-header h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .jp-modal-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }

        .jp-modal-close:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .jp-modal-body {
            padding: 1.3rem 1.4rem;
        }

        .jp-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.6rem;
            padding: 0.9rem 1.4rem 1.1rem;
            border-top: 1px solid #f0f4fb;
            background: #fafbfd;
        }

        .jp-form-group {
            margin-bottom: 1rem;
        }

        .jp-form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 0.4rem;
            letter-spacing: 0.02em;
        }

        .jp-form-group input,
        .jp-form-group select {
            width: 100%;
            padding: 0.6rem 0.85rem;
            border: 1.5px solid #d1d9ea;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #1e3a6e;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .jp-form-group input:focus,
        .jp-form-group select:focus {
            border-color: #0D2D6B;
            box-shadow: 0 0 0 3px rgba(13, 45, 107, 0.1);
        }

        .jp-error {
            display: block;
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 0.3rem;
        }

        .jp-delete-warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #9a3412;
            margin-top: 0.75rem;
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .jp-danger-warning {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #991b1b;
            margin-top: 0.75rem;
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .jp-info-warning {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #854d0e;
            margin-top: 0.75rem;
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }
    </style>

    {{-- Flash --}}
    @if (session('success'))
        <div class="jp-flash">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="jp-header">
        <div class="jp-header-title">
            <h2>
                <i class="fas fa-{{ $showTrash ? 'trash-alt' : 'list-alt' }}"
                    style="color:{{ $showTrash ? '#ef4444' : '#F5B800' }}; margin-right:0.4rem;"></i>
                {{ $showTrash ? 'Tong Sampah' : 'Jenis Pelanggaran' }}
            </h2>
            <p>{{ $showTrash ? 'Data yang dihapus sementara — bisa dipulihkan kapan saja' : 'Kelola daftar jenis dan tingkat pelanggaran siswa' }}
            </p>
        </div>
        <div class="jp-header-actions">
            <button wire:click="toggleTrash" class="jp-btn-trash {{ $showTrash ? 'active' : '' }}">
                <i class="fas fa-{{ $showTrash ? 'arrow-left' : 'trash-alt' }}"></i>
                {{ $showTrash ? 'Kembali' : 'Tong Sampah' }}
                @if (!$showTrash && $trashCount > 0)
                    <span class="jp-trash-badge">{{ $trashCount }}</span>
                @endif
            </button>
            @if (!$showTrash)
                <button wire:click="openCreate" class="jp-btn-primary">
                    <i class="fas fa-plus"></i> Tambah Jenis
                </button>
            @endif
        </div>
    </div>

    {{-- Banner mode trash --}}
    @if ($showTrash)
        <div class="jp-trash-banner">
            <i class="fas fa-info-circle"></i>
            Menampilkan <strong>{{ $trashCount }} data</strong> di tong sampah.
            Pulihkan data yang terhapus tidak sengaja, atau hapus permanen jika sudah tidak diperlukan.
        </div>
    @endif

    {{-- Toolbar --}}
    @if (!$showTrash)
        <div class="jp-toolbar">
            <div class="jp-search-wrap">
                <i class="fas fa-search"></i>
                <input wire:model.live.debounce.300ms="search" class="jp-search-input" type="text"
                    placeholder="Cari nama pelanggaran…">
            </div>

            <select wire:model.live="filterTingkat" class="jp-filter-select">
                <option value="">Semua Tingkat</option>
                <option value="Ringan">🟡 Ringan</option>
                <option value="Sedang">🟠 Sedang</option>
                <option value="Berat">🔴 Berat</option>
            </select>

            {{-- ✅ Filter Status Aktif/Nonaktif --}}
            <select wire:model.live="filterStatus" class="jp-filter-select">
                <option value="">Semua Status</option>
                <option value="1">✅ Aktif</option>
                <option value="0">🔴 Nonaktif</option>
            </select>

            <select wire:model.live="perPage" class="jp-perpage-select">
                <option value="10">10 / hal</option>
                <option value="20">20 / hal</option>
                <option value="50">50 / hal</option>
                <option value="100">100 / hal</option>
            </select>
        </div>
    @endif

    {{-- Tabel --}}
    <div class="jp-card">
        <table class="jp-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pelanggaran</th>
                    <th>Tingkat</th>
                    @if (!$showTrash)
                        <th>Status</th>
                    @endif
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $item)
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>
                            {{ $item->nama_pelanggaran }}
                            @if (!$item->is_active && !$showTrash)
                                <span style="font-size:0.7rem; color:#9ca3af; font-style:italic;"> (nonaktif)</span>
                            @endif
                        </td>
                        <td>
                            <span class="jp-badge jp-badge-{{ strtolower($item->tingkat_pelanggaran) }}">
                                {{ $item->tingkat_pelanggaran }}
                            </span>
                        </td>
                        @if (!$showTrash)
                            <td>
                                {{-- ✅ Badge status aktif/nonaktif --}}
                                @if ($item->is_active)
                                    <span class="jp-badge jp-badge-aktif">Aktif</span>
                                @else
                                    <span class="jp-badge jp-badge-nonaktif">Nonaktif</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            <div class="jp-actions">
                                @if ($showTrash)
                                    <button wire:click="restoreItem({{ $item->id_jenispelanggaran }})"
                                        class="jp-icon-btn restore" title="Pulihkan">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button wire:click="confirmForceDeleteItem({{ $item->id_jenispelanggaran }})"
                                        class="jp-icon-btn destroy" title="Hapus Permanen">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    {{-- ✅ Tombol toggle aktif/nonaktif --}}
                                    <button wire:click="bukaKonfirmasiToggle({{ $item->id_jenispelanggaran }})"
                                        class="jp-icon-btn {{ $item->is_active ? 'nonaktif' : 'aktifkan' }}"
                                        title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-{{ $item->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                    <button wire:click="openEdit({{ $item->id_jenispelanggaran }})"
                                        class="jp-icon-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDeleteItem({{ $item->id_jenispelanggaran }})"
                                        class="jp-icon-btn delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showTrash ? 4 : 5 }}">
                            <div class="jp-empty">
                                <i class="fas fa-{{ $showTrash ? 'trash-alt' : 'inbox' }}"></i>
                                <p>
                                    @if ($showTrash)
                                        Tong sampah kosong.
                                    @elseif ($search || $filterTingkat || $filterStatus !== '')
                                        Tidak ada data yang cocok dengan filter.
                                    @else
                                        Belum ada data jenis pelanggaran.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($data->hasPages())
            <div class="jp-pagination-wrap">
                <span>
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </span>
                {{ $data->links() }}
            </div>
        @else
            <div class="jp-pagination-wrap">
                <span>Total: {{ $data->total() }} data</span>
            </div>
        @endif
    </div>

    {{-- Modal: Form Tambah / Edit --}}
    @if ($showModal)
        <div class="jp-overlay" wire:click.self="closeModal">
            <div class="jp-modal">
                <div class="jp-modal-header">
                    <h3>
                        <i class="fas fa-{{ $editId ? 'edit' : 'plus-circle' }}"></i>
                        {{ $editId ? 'Edit' : 'Tambah' }} Jenis Pelanggaran
                    </h3>
                    <button wire:click="closeModal" class="jp-modal-close">&times;</button>
                </div>
                <div class="jp-modal-body">
                    <div class="jp-form-group">
                        <label>Nama Pelanggaran</label>
                        <input wire:model.live="nama_pelanggaran" type="text"
                            placeholder="Contoh: Tidak memakai seragam lengkap">
                        @error('nama_pelanggaran')
                            <span class="jp-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="jp-form-group">
                        <label>Tingkat Pelanggaran</label>
                        <select wire:model.live="tingkat_pelanggaran">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Ringan">🟡 Ringan</option>
                            <option value="Sedang">🟠 Sedang</option>
                            <option value="Berat">🔴 Berat</option>
                        </select>
                        @error('tingkat_pelanggaran')
                            <span class="jp-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="jp-modal-footer">
                    <button wire:click="closeModal" class="jp-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button wire:click="save" class="jp-btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ✅ Modal: Konfirmasi Toggle Aktif/Nonaktif --}}
    @if ($confirmToggleAktif)
        <div class="jp-overlay">
            <div class="jp-modal jp-modal-sm">
                <div class="jp-modal-header warning">
                    <h3>
                        <i class="fas fa-{{ $toggleAktifTarget?->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                        {{ $toggleAktifTarget?->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Jenis Pelanggaran
                    </h3>
                    <button wire:click="closeModal" class="jp-modal-close">&times;</button>
                </div>
                <div class="jp-modal-body">
                    <p style="font-size:0.88rem; color:#1e3a6e; margin:0;">
                        Yakin ingin
                        <strong>{{ $toggleAktifTarget?->is_active ? 'menonaktifkan' : 'mengaktifkan' }}</strong>
                        jenis pelanggaran
                        <strong>"{{ $toggleAktifTarget?->nama_pelanggaran }}"</strong>?
                    </p>
                    <div class="jp-info-warning">
                        <i class="fas fa-info-circle" style="margin-top:1px;flex-shrink:0;"></i>
                        <span>
                            @if ($toggleAktifTarget?->is_active)
                                Jenis pelanggaran yang dinonaktifkan <strong>tidak akan muncul</strong>
                                saat pencatatan pelanggaran baru, namun data historis tetap aman.
                            @else
                                Jenis pelanggaran akan <strong>aktif kembali</strong>
                                dan dapat digunakan saat pencatatan pelanggaran baru.
                            @endif
                        </span>
                    </div>
                </div>
                <div class="jp-modal-footer">
                    <button wire:click="closeModal" class="jp-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button wire:click="toggleAktif" class="jp-btn-primary">
                        <i class="fas fa-{{ $toggleAktifTarget?->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                        Ya, {{ $toggleAktifTarget?->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Konfirmasi Soft Delete --}}
    @if ($confirmDelete)
        <div class="jp-overlay">
            <div class="jp-modal jp-modal-sm">
                <div class="jp-modal-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
                    <button wire:click="closeModal" class="jp-modal-close">&times;</button>
                </div>
                <div class="jp-modal-body">
                    <p style="font-size:0.88rem; color:#1e3a6e; margin:0;">
                        Yakin ingin menghapus jenis pelanggaran ini?
                    </p>
                    <div class="jp-delete-warning">
                        <i class="fas fa-triangle-exclamation" style="margin-top:1px;flex-shrink:0;"></i>
                        <span>
                            Data akan dipindahkan ke <strong>tong sampah</strong> dan masih bisa dipulihkan kapan saja.
                        </span>
                    </div>
                </div>
                <div class="jp-modal-footer">
                    <button wire:click="closeModal" class="jp-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button wire:click="deleteItem" class="jp-btn-danger">
                        <i class="fas fa-trash"></i> Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Konfirmasi Hapus Permanen --}}
    @if ($confirmForceDelete)
        <div class="jp-overlay">
            <div class="jp-modal jp-modal-sm">
                <div class="jp-modal-header danger">
                    <h3><i class="fas fa-skull-crossbones"></i> Hapus Permanen</h3>
                    <button wire:click="closeModal" class="jp-modal-close">&times;</button>
                </div>
                <div class="jp-modal-body">
                    <p style="font-size:0.88rem; color:#1e3a6e; margin:0;">
                        Yakin ingin menghapus <strong>permanen</strong>? Tindakan ini <strong>tidak bisa
                            dibatalkan</strong>.
                    </p>
                    <div class="jp-danger-warning">
                        <i class="fas fa-triangle-exclamation" style="margin-top:1px;flex-shrink:0;"></i>
                        <span>
                            Data akan hilang selamanya dari database. Riwayat pelanggaran siswa yang terkait
                            bisa terdampak.
                        </span>
                    </div>
                </div>
                <div class="jp-modal-footer">
                    <button wire:click="closeModal" class="jp-btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button wire:click="forceDeleteItem" class="jp-btn-danger">
                        <i class="fas fa-skull-crossbones"></i> Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
