<x-layouts::auth>
    <div class="absolute inset-0 -z-10 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('assets/img/gedung1.webp') }}');opacity: 35%"></div>

    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat Akun')" :description="__('Silahkan isi form dibawah untuk membuat akun')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Nama')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Masukkan Nama')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="Masukkan Email"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Konfirmasi Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Konfirmasi Password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Buat Akun') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Sudah mempunyai akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Login') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
