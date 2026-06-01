<?php

use App\Models\Pengguna;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(Pengguna::class)->ignore($user->id_pengguna, 'id_pengguna')],
        ]);

        $user->fill($validated);

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }
}; ?>

<section>
    <style>
        .profile-section header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #0D2D6B;
            margin: 0 0 4px;
        }

        .profile-section header p {
            font-size: 13px;
            color: #4A5E8A;
            margin: 0 0 20px;
        }

        .profile-field {
            margin-bottom: 16px;
        }

        .profile-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 5px;
        }

        .profile-field input {
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

        .profile-field input:focus {
            border-color: #F5B800;
            box-shadow: 0 0 0 3px rgba(245, 184, 0, .15);
            background: #fff;
        }

        .profile-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
        }

        .profile-btn {
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

        .profile-btn:hover {
            background: #163580;
            transform: translateY(-1px);
        }

        .profile-btn:active {
            transform: scale(.98);
        }

        .profile-saved {
            font-size: 13px;
            color: #065F46;
            font-weight: 500;
        }
    </style>

    <div class="profile-section">
        <header>
            <h2>Informasi Profil</h2>
            <p>Perbarui informasi profil dan alamat email akun Anda.</p>
        </header>

        <form wire:submit="updateProfileInformation">

            <div class="profile-field">
                <label for="name">Nama Lengkap</label>
                <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name"
                    placeholder="Masukkan nama lengkap">
                @error('name')
                    <p class="profile-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div class="profile-field">
                <label for="email">Email</label>
                <input wire:model="email" id="email" type="email" name="email" required autocomplete="username"
                    placeholder="Masukkan email">
                @error('email')
                    <p class="profile-error">⚠ {{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex; align-items:center; gap:16px; margin-top:8px;">
                <button type="submit" class="profile-btn" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading style="display:none">Menyimpan...</span>
                </button>

                <span x-data="{ show: false }"
                    x-on:profile-updated.window="show = true; setTimeout(() => show = false, 3000)" x-show="show"
                    x-transition class="profile-saved">
                    ✅ Berhasil disimpan
                </span>
            </div>

        </form>
    </div>
</section>
