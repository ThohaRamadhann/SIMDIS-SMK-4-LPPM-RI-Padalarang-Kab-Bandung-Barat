<div class="p-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Wali Murid</h1>
        <p class="text-gray-600">Kelola data hubungan wali murid</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white shadow rounded-lg border p-6 mb-6">
        <h2 class="text-lg font-bold mb-4"> Edit Data Wali Murid
        </h2>

        <form wire:submit.prevent="simpan" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Pilih Wali Murid --}}
                <div>
                    <label class="block font-medium text-gray-700 mb-2">Pilih Wali Murid *</label>
                    <select wire:model.live="id_pengguna" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Wali Murid --</option>
                        @foreach ($pengguna as $p)
                            <option value="{{ $p->id_pengguna }}">
                                {{ $p->name }} ({{ $p->username }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_pengguna') 
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        Hanya menampilkan pengguna dengan role "Orang Tua"
                    </p>
                </div>

                {{-- Hubungan --}}
                <div>
                    <label class="block font-medium text-gray-700 mb-2">Hubungan *</label>
                    <input type="text" 
                           wire:model="hubungan" 
                           placeholder="{{ $isEdit ? 'Data akan otomatis muncul' : 'Contoh: Ayah, Ibu, Wali, dll' }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('hubungan') 
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        @if($isEdit)
                            <span class="text-green-600">✓ Data dari database</span>
                        @else
                            Isi manual data hubungan
                        @endif
                    </p>
                </div>
            </div>

            {{-- STATUS INFO --}}
            @if($id_pengguna)
            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            @if($isEdit)
                                <strong>Mode Edit:</strong> Data untuk wali murid ini sudah ada di database.
                            @else
                                <strong>Mode Tambah:</strong> Data untuk wali murid ini belum ada di database.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- BUTTONS --}}
            <div class="flex space-x-3">
                @if ($isEdit)
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg flex items-center">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                    <button type="button" 
                            wire:click="resetForm"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-6 py-3 rounded-lg flex items-center">
                        <i class="fas fa-times mr-2"></i> Batal
                    </button>
                @else
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-3 rounded-lg flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white shadow rounded-lg border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800">Daftar Wali Murid</h3>
        </div>
        
        @if($data->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-6 text-left font-semibold text-gray-700">No</th>
                        <th class="py-3 px-6 text-left font-semibold text-gray-700">Nama Wali Murid</th>
                        <th class="py-3 px-6 text-left font-semibold text-gray-700">Username</th>
                        <th class="py-3 px-6 text-left font-semibold text-gray-700">Email</th>
                        <th class="py-3 px-6 text-left font-semibold text-gray-700">Hubungan</th>
                        <th class="py-3 px-6 text-left font-semibold text-gray-700 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($data as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $loop->iteration }}</td>
                        <td class="py-4 px-6 font-medium text-gray-900">
                            {{ $item->pengguna->name ?? '-' }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $item->pengguna->username ?? '-' }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $item->pengguna->email ?? '-' }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ $item->hubungan == 'Ayah' ? 'bg-blue-100 text-blue-800' : 
                                   ($item->hubungan == 'Ibu' ? 'bg-pink-100 text-pink-800' : 
                                   'bg-gray-100 text-gray-800') }}">
                                {{ $item->hubungan }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex space-x-3">
                                <button wire:click="edit({{ $item->id_walimurid }})"
                                        class="text-blue-600 hover:text-blue-800 flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button wire:click="hapus({{ $item->id_walimurid }})"
                                        onclick="return confirm('Hapus data wali murid ini?')"
                                        class="text-red-600 hover:text-red-800 flex items-center">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-8 text-center">
                <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data wali murid</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-6">
                    Data wali murid akan muncul di sini setelah Anda menambahkan melalui form di atas.
                </p>
            </div>
        @endif
    </div>

</div>