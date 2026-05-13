<?php

namespace App\Livewire\Pelanggaran;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\JenisPelanggaran;
use App\Services\EarlyWarningService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    // protected $paginationTheme = 'tailwind';

    // // FORM
    // public $id_pelanggaran;
    // public $id_siswa;
    // public $id_walikelas;
    // public $id_jenispelanggaran;
    // public $waktu_kejadian;
    // public $deskripsi;
    // public $status = 'Belum Dibina';

    // // FILTER
    // public $search = '';
    // public $filterJenis = '';
    // public $filterTingkat = '';
    // public $filterStatus = '';
    // public $filterWaliKelas = '';

    // // SORT
    // public $sortBy = 'terbaru';

    // // PAGINATION
    // public $perPage = 10;

    // // MODE
    // public $isEdit = false;
    // public $showTrash = false;

    // protected $queryString = [
    //     'search' => ['except' => ''],
    //     'sortBy' => ['except' => 'terbaru'],
    // ];

    // public function updatingSearch()
    // {
    //     $this->resetPage();
    // }

    // public function updatingFilterJenis()
    // {
    //     $this->resetPage();
    // }

    // public function updatingFilterTingkat()
    // {
    //     $this->resetPage();
    // }

    // public function updatingFilterStatus()
    // {
    //     $this->resetPage();
    // }

    // public function updatingFilterWaliKelas()
    // {
    //     $this->resetPage();
    // }

    // public function updatingSortBy()
    // {
    //     $this->resetPage();
    // }

    // public function updatingPerPage()
    // {
    //     $this->resetPage();
    // }

    // public function updatingShowTrash()
    // {
    //     $this->resetPage();
    // }

    // public function resetForm()
    // {
    //     $this->reset([
    //         'id_pelanggaran',
    //         'id_siswa',
    //         'id_walikelas',
    //         'id_jenispelanggaran',
    //         'waktu_kejadian',
    //         'deskripsi',
    //         'status',
    //     ]);

    //     $this->status = 'Belum Dibina';

    //     $this->isEdit = false;
    //     $this->resetErrorBag();
    // }

    // public function simpan()
    // {
    //     $this->validate([
    //         'id_siswa' => 'required',
    //         'id_walikelas' => 'required',
    //         'id_jenispelanggaran' => 'required',
    //         'waktu_kejadian' => 'required|date',
    //         'deskripsi' => 'required',
    //     ]);

    //     $pelanggaran = Pelanggaran::create([
    //         'id_siswa' => $this->id_siswa,
    //         'id_walikelas' => $this->id_walikelas,
    //         'id_jenispelanggaran' => $this->id_jenispelanggaran,
    //         'waktu_kejadian' => $this->waktu_kejadian,
    //         'deskripsi' => $this->deskripsi,
    //         'status' => $this->status,
    //     ]);

    //     $pelanggaran->load([
    //         'siswa.waliMurid.pengguna',
    //         'waliKelas.pengguna',
    //         'jenisPelanggaran'
    //     ]);

    //     app(EarlyWarningService::class)->check($pelanggaran);

    //     session()->flash('success', 'Pelanggaran berhasil ditambahkan.');

    //     $this->resetForm();
    // }

    // public function edit($id)
    // {
    //     $data = Pelanggaran::findOrFail($id);

    //     $this->id_pelanggaran = $data->id_pelanggaran;
    //     $this->id_siswa = $data->id_siswa;
    //     $this->id_walikelas = $data->id_walikelas;
    //     $this->id_jenispelanggaran = $data->id_jenispelanggaran;
    //     $this->waktu_kejadian = $data->waktu_kejadian;
    //     $this->deskripsi = $data->deskripsi;
    //     $this->status = $data->status;

    //     $this->isEdit = true;
    // }

    // public function update()
    // {
    //     $this->validate([
    //         'id_siswa' => 'required',
    //         'id_walikelas' => 'required',
    //         'id_jenispelanggaran' => 'required',
    //         'waktu_kejadian' => 'required|date',
    //         'deskripsi' => 'required',
    //     ]);

    //     Pelanggaran::findOrFail($this->id_pelanggaran)->update([
    //         'id_siswa' => $this->id_siswa,
    //         'id_walikelas' => $this->id_walikelas,
    //         'id_jenispelanggaran' => $this->id_jenispelanggaran,
    //         'waktu_kejadian' => $this->waktu_kejadian,
    //         'deskripsi' => $this->deskripsi,
    //         'status' => $this->status,
    //     ]);

    //     session()->flash('success', 'Pelanggaran berhasil diperbarui.');

    //     $this->resetForm();
    // }

    // public function hapus($id)
    // {
    //     Pelanggaran::findOrFail($id)->delete();

    //     session()->flash('success', 'Data dipindahkan ke sampah.');
    // }

    // public function restore($id)
    // {
    //     Pelanggaran::onlyTrashed()->findOrFail($id)->restore();

    //     session()->flash('success', 'Data berhasil dipulihkan.');
    // }

    // public function forceDelete($id)
    // {
    //     Pelanggaran::onlyTrashed()->findOrFail($id)->forceDelete();

    //     session()->flash('success', 'Data dihapus permanen.');
    // }

    // public function emptyTrash()
    // {
    //     Pelanggaran::onlyTrashed()->forceDelete();

    //     session()->flash('success', 'Tong sampah dikosongkan.');
    // }

    // public function render()
    // {
    //     $user = Auth::user();
    //     $role = optional($user->role)->nama_role;

    //     $query = Pelanggaran::with([
    //         'siswa',
    //         'waliKelas.pengguna',
    //         'jenisPelanggaran'
    //     ]);

    //     // ROLE FILTER
    //     if ($role === 'wali_kelas') {
    //         $query->where(
    //             'id_walikelas',
    //             optional($user->waliKelas)->id_walikelas
    //         );
    //     }

    //     if ($role === 'orang_tua') {
    //         $query->whereHas('siswa', function ($q) use ($user) {
    //             $q->where(
    //                 'id_walimurid',
    //                 optional($user->waliMurid)->id_walimurid
    //             );
    //         });
    //     }

    //     // SOFT DELETE
    //     if ($this->showTrash) {
    //         $query->onlyTrashed();
    //     }

    //     // SEARCH
    //     if ($this->search) {
    //         $query->where(function ($q) {
    //             $q->whereHas('siswa', function ($s) {
    //                 $s->where('nama', 'like', '%' . $this->search . '%')
    //                   ->orWhere('nis', 'like', '%' . $this->search . '%');
    //             });
    //         });
    //     }

    //     // FILTER JENIS
    //     if ($this->filterJenis) {
    //         $query->where('id_jenispelanggaran', $this->filterJenis);
    //     }

    //     // FILTER STATUS
    //     if ($this->filterStatus) {
    //         $query->where('status', $this->filterStatus);
    //     }

    //     // FILTER TINGKAT
    //     if ($this->filterTingkat) {
    //         $query->whereHas('jenisPelanggaran', function ($q) {
    //             $q->where('tingkat', $this->filterTingkat);
    //         });
    //     }

    //     // FILTER WALIKELAS
    //     if ($this->filterWaliKelas) {
    //         $query->where('id_walikelas', $this->filterWaliKelas);
    //     }

    //     // SORT
    //     match ($this->sortBy) {

    //         'terlama' =>
    //             $query->orderBy('created_at', 'asc'),

    //         'az' =>
    //             $query->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id_siswa')
    //                 ->orderBy('siswa.nama', 'asc')
    //                 ->select('pelanggaran.*'),

    //         'za' =>
    //             $query->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id_siswa')
    //                 ->orderBy('siswa.nama', 'desc')
    //                 ->select('pelanggaran.*'),

    //         default =>
    //             $query->orderBy('created_at', 'desc'),
    //     };

    //     return view('livewire.pelanggaran.index', [
    //         'pelanggarans' => $query->paginate($this->perPage),

    //         'siswaList' => Siswa::orderBy('nama')->get(),

    //         'waliKelasList' => WaliKelas::with('pengguna')->get(),

    //         'jenisList' => JenisPelanggaran::orderBy('nama_pelanggaran')->get(),

    //         'trashCount' => Pelanggaran::onlyTrashed()->count(),

    //         'role' => $role,
    //     ]);
    // }
}