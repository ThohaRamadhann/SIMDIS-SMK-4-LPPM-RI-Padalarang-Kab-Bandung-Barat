<div class="p-4">
    <h2 class="text-xl font-semibold mb-4">Manajemen Wali Murid</h2>

    @if (session()->has('success'))
        <div class="p-2 bg-green-200 text-green-700 mb-3">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="simpan" class="mb-4 flex gap-2">
        
        {{-- pilih pengguna --}}
        <select wire:model.defer="id_pengguna" class="border p-2 rounded">
            <option value="">-- Pilih Pengguna --</option>
            @foreach ($pengguna as $p)
                <option value="{{ $p->id_pengguna }}">
                    {{ $p->name }} ({{ $p->username }})
                </option>
            @endforeach
        </select>

        {{-- hubungan --}}
        <input type="text" 
               wire:model.defer="hubungan" 
               placeholder="Hubungan (Ayah / Ibu / Wali)"
               class="border p-2 rounded">

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
    </form>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Pengguna</th>
                <th class="p-2 border">Hubungan</th>
                <th class="p-2 border w-32">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td class="p-2 border">
                        {{ $item->pengguna->name ?? '-' }}
                    </td>
                    <td class="p-2 border">{{ $item->hubungan }}</td>
                    <td class="p-2 border">
                        <button wire:click="hapus({{ $item->id_walimurid }})"
                                class="text-red-600"
                                onclick="return confirm('Hapus data ini?')">
                                Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-2 text-center text-gray-500">Belum ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
