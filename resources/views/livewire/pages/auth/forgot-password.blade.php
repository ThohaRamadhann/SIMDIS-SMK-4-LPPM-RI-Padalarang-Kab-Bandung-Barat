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
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div>
    <style>
        .forgot-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 100%;
            padding: 32px;
            border-radius: 28px;
            background: #ffffff;
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
            padding: 10px 14px;
            background: #ECFDF5;
            border: 1px solid #6EE7B7;
            border-radius: 12px;
            font-size: 13px;
            color: #065F46;
            margin-bottom: 12px;
            text-align: center;
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
                padding: 22px 18px;
                border-radius: 24px;
            }
        }
    </style>

    <div class="forgot-wrap">

        <h1>Lupa Password</h1>
        <p class="forgot-sub">Masukkan email Anda untuk menerima link reset password.</p>

        {{-- Notif sukses --}}
        @if (session('status'))
            <div class="forgot-success">
                ✅ {{ __('passwords.sent') }}
            </div>
        @endif

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
                    <p class="forgot-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="forgot-btn">
                Kirim Link Reset Password
            </button>

        </form>

        <a href="{{ route('login') }}" wire:navigate class="forgot-back">
            ← Kembali ke halaman login
        </a>

    </div>
</div>