<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password'         => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <style>
        .password-section header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #0D2D6B;
            margin: 0 0 4px;
        }

        .password-section header p {
            font-size: 13px;
            color: #4A5E8A;
            margin: 0 0 20px;
        }

        .password-field {
            margin-bottom: 16px;
        }

        .password-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 5px;
        }

        .password-field input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1.5px solid #D1D5DB;
            border-radius: 12px;
            font-size: 14px;
            color: #111827;
            background: #F9FAFB;
            outline: none;
            box-sizing: border-box;
            transition: border-color .2s, box-shadow .2s;
        }

        .password-field input:focus {
            border-color: #F5B800;
            box-shadow: 0 0 0 3px rgba(245, 184, 0, .15);
            background: #fff;
        }

        .password-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
        }

        .password-btn {
            height: 40px;
            padding: 0 24px;
            background: #0D2D6B;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }

        .password-btn:hover {
            background: #163580;
            transform: translateY(-1px);
        }

        .password-btn:active {
            transform: scale(.98);
        }

        .password-saved {
            font-size: 13px;
            color: #065F46;
            font-weight: 500;
        }
    </style>

    <div class="password-section">
        <header>
            <h2>Ubah Password</h2>
            <p>Gunakan password yang panjang dan acak agar akun Anda tetap aman.</p>
        </header>

        <form wire:submit="updatePassword">

            <div class="password-field">
                <label for="current_password">Password Saat Ini</label>
                <input
                    wire:model="current_password"
                    id="current_password"
                    type="password"
                    name="current_password"
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
                @error('current_password')
                    <p class="password-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div class="password-field">
                <label for="update_password">Password Baru</label>
                <input
                    wire:model="password"
                    id="update_password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="password-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div class="password-field">
                <label for="update_password_confirmation">Konfirmasi Password Baru</label>
                <input
                    wire:model="password_confirmation"
                    id="update_password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="••••••••"
                >
                @error('password_confirmation')
                    <p class="password-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex; align-items:center; gap:16px; margin-top:8px;">
                <button
                    type="submit"
                    class="password-btn"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Simpan Password</span>
                    <span wire:loading style="display:none">Menyimpan...</span>
                </button>

                <span
                    x-data="{ show: false }"
                    x-on:password-updated.window="show = true; setTimeout(() => show = false, 3000)"
                    x-show="show"
                    x-transition
                    class="password-saved"
                >
                    ✅ Password berhasil diperbarui
                </span>
            </div>

        </form>
    </div>
</section>