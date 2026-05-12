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

    <style>
        .simdis-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 4px 16px rgba(13,45,107,.06);
            border: 1px solid rgba(13,45,107,.07);
        }

        .simdis-title {
            font-size: 17px;
            font-weight: 700;
            color: #0D2D6B;
            margin-bottom: 2px;
        }

        .simdis-subtitle {
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 14px;
        }

        .simdis-label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #0D2D6B;
        }

        .simdis-input,
        .simdis-select {
            width: 100%;
            height: 40px;
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            padding: 0 12px;
            font-size: 13px;
            background: #F9FAFB;
            transition: .2s ease;
            outline: none;
        }

        .simdis-input:focus,
        .simdis-select:focus {
            border-color: #F5B800;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(245,184,0,.12);
        }

        .simdis-error {
            font-size: 11px;
            color: #DC2626;
            margin-top: 4px;
        }

        .simdis-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #0D2D6B;
            margin-bottom: 10px;
        }

        .simdis-btn-primary {
            background: #0D2D6B;
            color: white;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
        }

        .simdis-btn-primary:hover {
            background: #163580;
            transform: translateY(-1px);
        }

        .simdis-btn-secondary {
            border: 1px solid #D1D5DB;
            background: white;
            color: #374151;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
        }

        .simdis-btn-secondary:hover { background: #F9FAFB; }

        .simdis-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .simdis-table thead th {
            background: #F8FAFC;
            color: #0D2D6B;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 14px;
            border-bottom: 1px solid #E5E7EB;
        }

        .simdis-table tbody td {
            padding: 10px 14px;
            font-size: 13px;
            border-bottom: 1px solid #F1F5F9;
        }

        .simdis-table tbody tr:hover { background: #F8FAFC; }

        .badge-role {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            background: rgba(13,45,107,.08);
            color: #0D2D6B;
            font-size: 11px;
            font-weight: 600;
        }

        .action-btn { font-size: 12px; font-weight: 600; transition: .2s; }
        .action-edit { color: #0D2D6B; }
        .action-edit:hover { color: #163580; }
        .action-delete { color: #DC2626; }
        .action-delete:hover { color: #B91C1C; }
    </style>

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
                                </div>
                                <div>
                                    <label class="simdis-label">Jabatan</label>
                                    <input type="text" wire:model="jabatan" placeholder="Masukkan jabatan" class="simdis-input">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- DETAIL ORTU --}}
                    @if ($this->selectedRoleName === 'orang_tua')
                        <div class="pt-3 border-t">
                            <h4 class="simdis-section-title">Detail Wali Murid</h4>
                            <input type="text" wire:model="hubungan" placeholder="Ayah / Ibu / Wali" class="simdis-input">
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

                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="simdis-title">Daftar Pengguna</h2>
                        <p class="simdis-subtitle mb-0">Data seluruh pengguna sistem SIMDIS.</p>
                    </div>
                    <span style="font-size:11px; font-weight:700; color:#4A5E8A; background:#F0F4FB;
                                 padding:4px 10px; border-radius:20px;">
                        {{ count($users) }} pengguna
                    </span>
                </div>

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
                                @php $isEditing = $editingId == $u->id_pengguna; @endphp
                                <tr wire:key="user-{{ $u->id_pengguna }}"
                                    style="{{ $isEditing ? 'background: rgba(245,184,0,0.07); outline: 1.5px solid rgba(245,184,0,0.35); outline-offset: -1px;' : '' }}">
                                    <td style="color:#4A5E8A; font-size:12px;">{{ $u->id_pengguna }}</td>
                                    <td class="font-medium" style="color:#0D2D6B;">{{ $u->name }}</td>
                                    <td style="color:#4A5E8A;">{{ $u->username }}</td>
                                    <td>
                                        <span class="badge-role">{{ optional($u->role)->nama_role }}</span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <button wire:click="editUser({{ $u->id_pengguna }})"
                                            class="action-btn action-edit mr-3">
                                            {{ $isEditing ? '✎ Diedit' : 'Edit' }}
                                        </button>
                                        @if (!$isEditing)
                                            <button wire:click="deleteUser({{ $u->id_pengguna }})"
                                                wire:confirm="Yakin ingin menghapus pengguna ini?"
                                                class="action-btn action-delete">
                                                Hapus
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-400" style="font-size:13px;">
                                        Belum ada pengguna
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>