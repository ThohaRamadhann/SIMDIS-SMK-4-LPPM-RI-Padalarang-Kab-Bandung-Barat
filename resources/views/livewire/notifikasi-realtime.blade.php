<div>
    <h3>Notifikasi</h3>
    <ul>
        @foreach($notifikasiList as $n)
            <li>{{ $n['judul'] }} - {{ $n['pesan'] }}</li>
        @endforeach
    </ul>
</div>
