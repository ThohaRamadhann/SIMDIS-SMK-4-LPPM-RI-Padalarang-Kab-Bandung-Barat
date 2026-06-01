<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token'    => ['required'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password'       => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            // Pesan error custom bahasa Indonesia
            $pesanError = match($status) {
                Password::INVALID_TOKEN => 'Link reset password tidak valid atau sudah kadaluarsa. Silakan minta link baru.',
                Password::INVALID_USER  => 'Email tidak terdaftar dalam sistem.',
                default                 => 'Terjadi kesalahan. Silakan coba lagi.',
            };

            $this->addError('email', $pesanError);
            return;
        }

        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        // Hardcode pesan sukses bahasa Indonesia
        Session::flash('status', 'password-reset-success');

        $this->redirect(route('login'), navigate: true);
    }
};
?>

<div>
    <style>
        .reset-wrap {
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

        .reset-logo {
            margin-bottom: 4px;
        }

        .reset-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .reset-wrap h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0D2D6B;
            margin: 0 0 4px;
        }

        .reset-wrap p.reset-sub {
            font-size: 13px;
            color: #6B7280;
            margin: 0 0 16px;
            text-align: center;
        }

        .reset-field {
            width: 100%;
            margin-bottom: 14px;
        }

        .reset-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 5px;
        }

        .reset-field input {
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

        .reset-field input:focus {
            border-color: #F5B800;
            box-shadow: 0 0 0 3px rgba(245, 184, 0, .15);
            background: #fff;
        }

        .reset-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
        }

        .reset-btn {
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

        .reset-btn:hover {
            background: #163580;
            transform: translateY(-1px);
        }

        .reset-btn:active {
            transform: scale(.98);
        }

        .reset-btn--loading {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none !important;
        }

        .reset-spinner {
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

        .reset-back {
            font-size: 13px;
            color: #0D2D6B;
            text-decoration: none;
            font-weight: 500;
            margin-top: 16px;
        }

        .reset-back:hover {
            color: #F5B800;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .reset-wrap {
                padding: 24px 20px;
                border-radius: 24px;
            }
        }
    </style>

    <div class="reset-wrap">

        {{-- Logo --}}
        <div class="reset-logo">
            <a href="/" wire:navigate>
                <img src="{{ asset('images/logo_simdis.png') }}" alt="Logo SIMDIS">
            </a>
        </div>

        <h1>Reset Password</h1>
        <p class="reset-sub">Masukkan password baru Anda.</p>

        <form wire:submit.prevent="resetPassword" style="width:100%">

            {{-- Email --}}
            <div class="reset-field">
                <label for="email">Email</label>
                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Masukkan email"
                >
                @error('email')
                    <p class="reset-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div class="reset-field">
                <label for="password">Password Baru</label>
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="reset-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="reset-field">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                >
                @error('password_confirmation')
                    <p class="reset-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="reset-btn"
                wire:loading.attr="disabled"
                wire:loading.class="reset-btn--loading"
            >
                <span wire:loading.remove>Simpan Password Baru</span>
                <span wire:loading style="display:none">
                    <span class="reset-spinner"></span> Menyimpan...
                </span>
            </button>

        </form>

        <a href="{{ route('login') }}" wire:navigate class="reset-back">
            ← Kembali ke halaman login
        </a>

    </div>
</div>