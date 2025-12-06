<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="bg-white shadow p-4 rounded">
        <h3 class="font-semibold mb-2">{{ $editingId ? 'Edit Kelas' : 'Tambah Kelas' }}</h3>

        <input class="w-full border rounded px-2 py-1 mb-2"
               placeholder="Nama Kelas"
               wire:model.defer="nama_kelas">

        <input class="w-full border rounded px-2 py-1 mb-2"
               placeholder="Tingkat"
               wire:model.defer="tingkat">

        <input class="w-full border rounded px-2 py-1 mb-2"
               placeholder="Jurusan"
               wire:model.defer="jurusan">
        
               <input class="w-full border rounded px-2 py-1 mb-2"
               placeholder="Tahun Ajaran"
               wire:model.defer="tahun_ajaran">

        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-3 py-1 rounded"
                    wire:click="save">Simpan</button>
            <button class="border px-3 py-1 rounded"
                    wire:click="resetForm">Reset</button>
        </div>
    </div>

    <div class="col-span-2 bg-white shadow p-4 rounded">
        <h3 class="font-semibold mb-2">Daftar Kelas</h3>

        <table class="table-auto w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">Nama</th>
                    <th class="p-2">Tingkat</th>
                    <th class="p-2">Jurusan</th>
                    <th class="p-2">Tahun Ajaran</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kelas as $k)
                    <tr class="border-b">
                        <td class="p-2">{{ $k->id_kelas }}</td>
                        <td class="p-2">{{ $k->nama_kelas }}</td>
                        <td class="p-2">{{ $k->tingkat }}</td>
                        <td class="p-2">{{ $k->jurusan }}</td>
                        <td class="p-2">{{ $k->tahun_ajaran }}</td>
                        <td class="p-2">
                            <button wire:click="edit({{ $k->id_kelas }})"
                                    class="text-blue-600">Edit</button>
                            <button wire:click="delete({{ $k->id_kelas }})"
                                    class="text-red-600 ml-2">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
