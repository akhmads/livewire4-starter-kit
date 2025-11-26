<x-auth-layout>
    <x-auth-card>
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-2">
        @csrf

            <x-input
                label="Name"
                wire:model="name"
                name="name"
                icon="o-user"
                placeholder="Full name"
                value="{{ old('name') }}"
            />

            <x-input
                label="E-mail address"
                wire:model="email"
                name="email"
                icon="o-envelope"
                placeholder="email@example.com"
                value="{{ old('email') }}"
            />

            <x-input
                label="Password"
                wire:model="password"
                name="password"
                type="password"
                icon="o-key"
                placeholder="Password"
                value="{{ old('password') }}"
            />

            <x-input
                label="Confirm password"
                wire:model="password_confirmation"
                name="password_confirmation"
                type="password"
                icon="o-key"
                placeholder="Confirm password"
                value="{{ old('password_confirmation') }}"
            />

            <div class="space-y-4 mt-4">
                <x-button label="Create account" type="submit" class="btn-primary w-full" />

                <div class="space-x-1 text-center text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Already have an account?') }}</span>
                    <a href="{{ route('login') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Log in') }}</a>
                </div>

                {{-- <div class="space-x-1 text-center text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Back to') }}</span>
                    <a href="{{ route('home') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('home page') }}</a>
                </div> --}}
            </div>
        </form>
    </x-auth-card>
</x-auth-layout>
