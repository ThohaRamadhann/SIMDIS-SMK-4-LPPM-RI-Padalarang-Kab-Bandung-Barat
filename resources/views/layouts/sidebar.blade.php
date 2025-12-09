
@php
    // Ambil data role user yang sedang login
    $user = Auth::user();
    $role = optional($user->role)->nama_role;
@endphp

<aside class="w-64 bg-gray-800 text-white min-h-screen shadow-lg">
    <div class="p-4 border-b border-gray-700">
        <h3 class="text-xl font-semibold">{{ config('app.name') }}</h3>
        <p class="text-sm text-gray-400 capitalize">Role: {{ str_replace('_', ' ', $role) }}</p>
    </div>

    <nav class="mt-4">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-700 
            {{ request()->routeIs('dashboard') ? 'bg-gray-700 font-bold' : '' }}">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>
        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm hover:bg-gray-700 
            {{ request()->routeIs('profile') ? 'bg-gray-700 font-bold' : '' }}">
            <i class="fas fa-user-circle mr-2"></i> Profile
        </a>

        <div class="mt-4 border-t border-gray-700"></div>

        {{-- PERBAIKAN: Header "Manajemen Data" hanya muncul jika BUKAN admin --}}
        @if ($role !== 'admin')
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">
                Manajemen Data
            </div>
        @endif

        @if (in_array($role, ['guru_bk', 'wali_kelas']))
            <a href="{{ route('pelanggaran.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-700 
                {{ request()->routeIs('pelanggaran.*') ? 'bg-indigo-500 font-bold' : '' }}">
                <i class="fas fa-exclamation-triangle mr-2"></i> Data Pelanggaran
            </a>
        @endif

        @if ($role === 'admin')
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase mt-4">
                Administrasi Sistem
            </div>
            
            <a href="{{ route('users') }}" class="block px-4 py-2 text-sm hover:bg-gray-700">
                <i class="fas fa-users-cog mr-2"></i> Pengguna
            </a>
            <a href="{{ route('siswa') }}" class="block px-4 py-2 text-sm hover:bg-gray-700">
                <i class="fas fa-user-graduate mr-2"></i> Siswa
            </a>
            <a href="{{ route('wali-murid') }}" class="block px-4 py-2 text-sm hover:bg-gray-700">
                <i class="fas fa-user-tie mr-2"></i> Wali Murid
            </a>
            <a href="{{ route('wali-kelas') }}" class="block px-4 py-2 text-sm hover:bg-gray-700">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Wali Kelas
            </a>
            <a href="{{ route('kelas') }}" class="block px-4 py-2 text-sm hover:bg-gray-700">
                <i class="fas fa-school mr-2"></i> Kelas
            </a>
        @endif
        
        </nav>
</aside>