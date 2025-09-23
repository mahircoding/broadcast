<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', 'Link reset password telah dikirim ke email Anda!');
            $this->email = '';
        } else {
            session()->flash('error', 'Gagal mengirim link reset password. Silakan coba lagi.');
        }
    }
}; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 auth-header">Lupa Password</h2>
        <p class="text-gray-600 mt-2">Masukkan email Anda untuk menerima link reset password</p>
    </div>

    <!-- Alert Messages -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Forgot Password Form -->
    <form wire:submit="sendPasswordResetLink" class="space-y-6">
        <!-- Email Input -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
            </label>
            <input
                wire:model="email"
                type="email"
                id="email"
                name="email"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none @error('email') border-red-500 @enderror"
                placeholder="Masukkan email Anda"
                required
                autofocus
            >
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="auth-button w-full py-3 px-4 text-white font-semibold rounded-lg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition duration-200"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Link Reset Password
            </span>
            <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Mengirim...
            </span>
        </button>
    </form>

    <!-- Back to Login -->
    <div class="text-center">
        <p class="text-gray-600">
            Ingat password Anda?
            <a href="{{ route('login') }}" wire:navigate class="text-purple-600 hover:text-purple-800 font-medium transition duration-200">
                Kembali ke Login
            </a>
        </p>
    </div>
</div>
