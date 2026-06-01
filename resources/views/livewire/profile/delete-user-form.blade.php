<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section>
    <style>
        .delete-section header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #DC2626;
            margin: 0 0 4px;
        }

        .delete-section header p {
            font-size: 13px;
            color: #4A5E8A;
            margin: 0 0 20px;
        }

        .delete-btn-open {
            height: 40px;
            padding: 0 20px;
            background: #fff;
            color: #DC2626;
            border: 1.5px solid #DC2626;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .delete-btn-open:hover {
            background: #FEF2F2;
        }

        .delete-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 16px;
        }

        .delete-modal {
            background: #fff;
            border-radius: 20px;
            padding: 28px 24px;
            width: 100%;
            max-width: 440px;
            box-sizing: border-box;
        }

        .delete-modal h3 {
            font-size: 16px;
            font-weight: 700;
            color: #DC2626;
            margin: 0 0 8px;
        }

        .delete-modal p {
            font-size: 13px;
            color: #4A5E8A;
            margin: 0 0 16px;
            line-height: 1.6;
        }

        .delete-modal-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            margin-bottom: 5px;
        }

        .delete-modal-field input {
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

        .delete-modal-field input:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .15);
            background: #fff;
        }

        .delete-modal-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
        }

        .delete-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .delete-btn-cancel {
            height: 40px;
            padding: 0 20px;
            background: #F3F4F6;
            color: #374151;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }

        .delete-btn-cancel:hover {
            background: #E5E7EB;
        }

        .delete-btn-confirm {
            height: 40px;
            padding: 0 20px;
            background: #DC2626;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
        }

        .delete-btn-confirm:hover {
            background: #B91C1C;
        }
    </style>

    <div class="delete-section" x-data="{ showModal: false }">
        <header>
            <h2>Hapus Akun</h2>
            <p>
                Setelah akun dihapus, semua data akan dihapus secara permanen.
                Pastikan Anda sudah menyimpan data penting sebelum melanjutkan.
            </p>
        </header>

        <button
            type="button"
            class="delete-btn-open"
            x-on:click="showModal = true"
        >
            Hapus Akun Saya
        </button>

        {{-- Modal --}}
        <div
            class="delete-modal-overlay"
            x-show="showModal"
            x-transition
            x-on:keydown.escape.window="showModal = false"
            style="display:none"
        >
            <div class="delete-modal" x-on:click.outside="showModal = false">

                <h3>⚠ Hapus Akun Secara Permanen?</h3>
                <p>
                    Tindakan ini <strong>tidak dapat dibatalkan</strong>. Seluruh data akun Anda
                    akan dihapus selamanya. Masukkan password Anda untuk konfirmasi.
                </p>

                <form wire:submit="deleteUser">

                    <div class="delete-modal-field">
                        <label for="delete_password">Password</label>
                        <input
                            wire:model="password"
                            id="delete_password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        @error('password')
                            <p class="delete-modal-error">⚠ {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="delete-modal-actions">
                        <button
                            type="button"
                            class="delete-btn-cancel"
                            x-on:click="showModal = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="delete-btn-confirm"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Ya, Hapus Akun</span>
                            <span wire:loading style="display:none">Menghapus...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</section>