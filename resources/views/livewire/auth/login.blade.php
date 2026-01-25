<x-layouts::auth>
    <div class="absolute inset-0 -z-10 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('assets/img/gedung1.webp') }}');opacity: 35%"></div>

    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Login')" :description="__('Silahkan masukkan akun anda')"/>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')"/>

        <form id="loginForm" method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="Masukkan Email"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Masukkan Password')"
                    viewable
                />

                {{--                @if (Route::has('password.request'))--}}
                {{--                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>--}}
                {{--                        {{ __('Forgot your password?') }}--}}
                {{--                    </flux:link>--}}
                {{--                @endif--}}
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Ingat Saya')" :checked="old('remember')"/>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" id="submitBtn" type="submit"
                             class="w-full">
                    {{ __('Login') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Belum mempunyai akun?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Register') }}</flux:link>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        });
    </script>
</x-layouts::auth>
