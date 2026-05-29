<?php

use App\Livewire\Forms\LoginForm;
use App\Models\LogAktivitas;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        LogAktivitas::catat(
            aksi: 'login',
            modul: 'auth',
            keterangan: 'User login ke sistem',
        );

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
};
?>

<div>
    <style>
        .simdis-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 100%;
            padding: 24px;
            border-radius: 28px;
            background: #ffffff;
        }

        .simdis-wrap h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0D2D6B;
            margin: 0;
        }

        .simdis-wrap p.simdis-sub {
            font-size: 13px;
            color: #6B7280;
            margin: 0 0 12px;
        }

        .simdis-wrap label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 5px;
            text-align: left;
            width: 100%;
        }

        .simdis-wrap input[type="email"],
        .simdis-wrap input[type="password"] {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1.5px solid #D1D5DB;
            border-radius: 14px;
            font-size: 14px;
            color: #111827;
            background: #F9FAFB;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            margin-bottom: 4px;
        }

        .simdis-wrap input[type="email"]:focus,
        .simdis-wrap input[type="password"]:focus {
            border-color: #F5B800;
            box-shadow: 0 0 0 3px rgba(245,184,0,.15);
            background: #fff;
        }

        .simdis-field {
            width: 100%;
            margin-bottom: 14px;
        }

        .simdis-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 18px;
            gap: 10px;
        }

        .simdis-remember {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6B7280;
            cursor: pointer;
        }

        .simdis-remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #0D2D6B;
            cursor: pointer;
        }

        .simdis-forgot {
            font-size: 13px;
            color: #0D2D6B;
            text-decoration: none;
            font-weight: 500;
        }

        .simdis-forgot:hover {
            color: #F5B800;
            text-decoration: underline;
        }

        .simdis-btn {
            width: 100%;
            height: 46px;
            background: #0D2D6B;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .3px;
            transition: background .2s, transform .15s;
        }

        .simdis-btn:hover {
            background: #163580;
            transform: translateY(-1px);
        }

        .simdis-btn:active {
            transform: scale(.98);
        }

        .simdis-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
        }

        @media (max-width: 640px) {
            .simdis-wrap {
                padding: 22px 18px;
                margin-left: 14px;
                margin-right: 14px;
                border-radius: 24px;
            }

            .simdis-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .simdis-forgot {
                margin-top: 4px;
            }
        }
    </style>

    <div class="simdis-wrap">

        <h1>Login</h1>

        <p class="simdis-sub">Masuk ke Sistem</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" style="width:100%">

            {{-- Email --}}
            <div class="simdis-field">
                <label for="email">Email</label>
                <input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    name="email"
                    placeholder="masukkan email dengan benar!"
                    required
                    autofocus
                    autocomplete="username"
                >
                @error('form.email')
                    <p class="simdis-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="simdis-field">
                <label for="password">Password</label>
                <input
                    wire:model="form.password"
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                @error('form.password')
                    <p class="simdis-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ingat saya --}}
            <div class="simdis-row">
                <label class="simdis-remember">
                    <input
                        wire:model="form.remember"
                        id="remember"
                        type="checkbox"
                        name="remember"
                    >
                    Ingatkan saya
                </label>
            </div>

            {{-- Tombol Masuk --}}
            <button type="submit" class="simdis-btn">
                Masuk
            </button>

            {{-- Lupa Password --}}
            <div style="width:100%; text-align:center; margin-top:16px;">
                @if (Route::has('password.request'))
                    <a class="simdis-forgot" href="{{ route('password.request') }}" wire:navigate>
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

        </form>

    </div>
</div>