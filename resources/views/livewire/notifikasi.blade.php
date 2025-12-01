<div>
    <h3 class="font-bold mb-2">Notifikasi</h3>
    <ul>
        @forelse($notifikasi as $item)
            <li class="border-b py-2 px-2">{{ $item['judul'] ?? 'Notifikasi baru' }} - {{ $item['created_at'] }}</li>
        @empty
            <li class="py-2 px-2 text-gray-500">Belum ada notifikasi</li>
        @endforelse
    </ul>
</div>
