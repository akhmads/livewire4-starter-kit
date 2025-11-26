<x-auth-layout>
    <x-auth-card>

        <x-auth-header :title="__('Reset Password')" :description="__('Please enter your new password below')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-2">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <x-input
                label="E-mail address"
                name="email"
                wire:model="email"
                icon="o-envelope"
                placeholder="email@example.com"
                value="{{ request('email') }}"
             />
            <x-input
                label="Password"
                name="password"
                wire:model="password"
                type="password"
                icon="o-key"
                placeholder="Password"
            />
            <x-input
                label="Confirm password"
                name="password_confirmation"
                wire:model="password_confirmation"
                type="password"
                icon="o-key"
                placeholder="Confirm password"
            />

            <div class="space-y-4 mt-4">
                <x-button label="Reset Password" type="submit" class="btn-primary w-full" />
            </div>
        </form>

    </x-auth-card>
</x-auth-layout>
