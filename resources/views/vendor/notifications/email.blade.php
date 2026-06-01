<x-mail::message>

{{-- Greeting --}}
# Halo!

Kami menerima permintaan untuk mereset password akun **{{ config('app.name') }}** Anda.

Klik tombol di bawah ini untuk membuat password baru:

<x-mail::button :url="$actionUrl" color="primary">
🔐 Reset Password Saya
</x-mail::button>

---

**Langkah setelah klik tombol:**
1. Anda akan diarahkan ke halaman reset password
2. Masukkan password baru Anda
3. Konfirmasi password baru
4. Klik **Simpan** — selesai!

---

> ⏱ **Link ini hanya berlaku selama 60 menit** sejak email ini dikirim.
>
> 🔒 Jika Anda **tidak merasa meminta reset password**, abaikan email ini. Password Anda tidak akan berubah.

Salam,<br>
Tim {{ config('app.name') }}

<x-slot:subcopy>
Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>

</x-mail::message>