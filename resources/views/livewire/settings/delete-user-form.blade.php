<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Hapus Akun') }}</flux:heading>
        <flux:subheading>{{ __('Menghapus akun dan semua data pada akun tersebut.') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Hapus akun') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Anda yakin untuk menghapus akun?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Akun anda akan dihapus secara permanen dan tidak dapat dipulihkan. Silahkan masukkan password untuk menghapus akun secara permanen.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Password')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Batal') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Hapus akun') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
