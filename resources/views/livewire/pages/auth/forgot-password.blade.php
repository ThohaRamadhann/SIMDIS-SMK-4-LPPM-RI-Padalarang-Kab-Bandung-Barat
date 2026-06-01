<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email',
                $status === Password::INVALID_USER
                    ? 'Email tidak terdaftar dalam sistem.'
                    : 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.'
            );
            return;
        }

        $this->reset('email');
        session()->flash('status', 'sent');
    }
};
?>

<div>
    <style>
        .forgot-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 100%;
            padding: 28px 24px 24px;
            border-radius: 28px;
            background: #ffffff;
            box-sizing: border-box;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }

        .forgot-logo {
            margin-bottom: 4px;
        }

        .forgot-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .forgot-wrap h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0D2D6B;
            margin: 0 0 4px;
        }

        .forgot-wrap p.forgot-sub {
            font-size: 13px;
            color: #6B7280;
            margin: 0 0 16px;
            text-align: center;
        }

        .forgot-field {
            width: 100%;
            margin-bottom: 14px;
        }

        .forgot-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 5px;
        }

        .forgot-field input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1.5px solid #D1D5DB;
            border-radius: 14px;
            font-size: 14px;
            color: #111827;
            background: #F9FAFB;
            outline: none;
            box-sizing: border-box;
            transition: border-color .2s, box-shadow .2s;
        }

        .forgot-field input:focus {
            border-color: #F5B800;
            box-shadow: 0 0 0 3px rgba(245, 184, 0, .15);
            background: #fff;
        }

        .forgot-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
        }

        .forgot-success {
            width: 100%;
            padding: 14px 16px;
            background: #ECFDF5;
            border: 1px solid #6EE7B7;
            border-radius: 14px;
            font-size: 13px;
            color: #065F46;
            margin-bottom: 12px;
            box-sizing: border-box;
        }

        .forgot-success .fs-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .forgot-success .fs-list {
            margin: 8px 0 0 0;
            padding: 0 0 0 18px;
            line-height: 1.8;
        }

        .forgot-success .fs-note {
            margin-top: 8px;
            font-size: 12px;
            color: #047857;
            border-top: 1px solid #A7F3D0;
            padding-top: 8px;
        }

        .forgot-btn {
            width: 100%;
            height: 46px;
            background: #0D2D6B;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .15s;
            margin-top: 6px;
        }

        .forgot-btn:hover {
            background: #163580;
            transform: translateY(-1px);
        }

        .forgot-btn:active {
            transform: scale(.98);
        }

        .forgot-btn--loading {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none !important;
        }

        .forgot-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            vertical-align: middle;
            margin-right: 4px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .forgot-back {
            font-size: 13px;
            color: #0D2D6B;
            text-decoration: none;
            font-weight: 500;
            margin-top: 16px;
        }

        .forgot-back:hover {
            color: #F5B800;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .forgot-wrap {
                padding: 24px 20px;
                border-radius: 24px;
            }
        }
    </style>

    <div class="forgot-wrap">

        {{-- Logo --}}
        <div class="forgot-logo">
            <a href="/" wire:navigate>
                <img src="{{ asset('images/logo_simdis.png') }}" alt="Logo SIMDIS">
            </a>
        </div>

        <h1>Lupa Password</h1>
        <p class="forgot-sub">Masukkan email Anda untuk menerima link reset password.</p>

        {{-- Notif sukses --}}
        @if (session('status') === 'sent')
            <div class="forgot-success">
                <div class="fs-title">
                    ✅ Link reset password telah dikirim!
                </div>
                <div>Silakan ikuti langkah berikut:</div>
                <ul class="fs-list">
                    <li>Buka aplikasi email Anda</li>
                    <li>Cari email dari <strong>{{ config('app.name') }}</strong></li>
                    <li>Klik tombol <strong>"Reset Password"</strong> di dalam email</li>
                    <li>Buat password baru Anda</li>
                </ul>
                <div class="fs-note">
                    ⏱ Link berlaku selama <strong>60 menit</strong>. Periksa folder <strong>Spam</strong> jika email tidak masuk ke inbox.
                </div>
            </div>
        @endif

        {{-- Sembunyikan form setelah sukses --}}
        @if (session('status') !== 'sent')
            <form wire:submit.prevent="sendPasswordResetLink" style="width:100%">

                <div class="forgot-field">
                    <label for="email">Email</label>
                    <input
                        wire:model="email"
                        id="email"
                        type="email"
                        name="email"
                        placeholder="Masukkan email Anda"
                        required
                        autofocus
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="forgot-error">⚠ {{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="forgot-btn"
                    wire:loading.attr="disabled"
                    wire:loading.class="forgot-btn--loading"
                >
                    <span wire:loading.remove>Kirim Link Reset Password</span>
                    <span wire:loading style="display:none">
                        <span class="forgot-spinner"></span> Mengirim...
                    </span>
                </button>

            </form>
        @endif

        <a href="{{ route('login') }}" wire:navigate class="forgot-back">
            ← Kembali ke halaman login
        </a>

    </div>
</div>