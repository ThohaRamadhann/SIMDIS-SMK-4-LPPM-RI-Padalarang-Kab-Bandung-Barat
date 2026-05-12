<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
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

<style>
    :root{
        --navy: #0D2D6B;
        --yellow: #F5B800;
    }

    .input-custom{
        border: 2px solid #d1d5db;
        transition: 0.3s;
    }

    .input-custom:focus{
        border-color: var(--yellow) !important;
        box-shadow: 0 0 0 3px rgba(245,184,0,0.25) !important;
    }

    .btn-navy{
        background: var(--navy);
        transition: 0.3s;
    }

    .btn-navy:hover{
        background: #133b8a;
    }
</style>

<div>

    {{-- Heading --}}
    <div class="text-center mb-8">

        <h1 class="text-3xl font-bold mb-2" style="color: #0D2D6B;">
            Reset Password
        </h1>

        <p class="text-gray-500 text-sm leading-relaxed">
            Masukkan email Anda untuk menerima link reset password.
        </p>

    </div>

    {{-- Session Status --}}
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    {{-- Form --}}
    <form wire:submit="sendPasswordResetLink">

        {{-- Email --}}
        <div>

            <label
                for="email"
                class="block font-semibold mb-2"
                style="color: #0D2D6B;"
            >
                Email
            </label>

            <x-text-input
                wire:model="email"
                id="email"
                class="block mt-1 w-full rounded-xl input-custom"
                type="email"
                name="email"
                required
                autofocus
                placeholder="Masukkan email"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />

        </div>

        {{-- Button --}}
        <div class="mt-6">

            <button
                type="submit"
                class="w-full text-white font-semibold py-3 rounded-xl shadow-lg btn-navy"
            >
                Kirim Link Reset Password
            </button>

        </div>

    </form>

</div>