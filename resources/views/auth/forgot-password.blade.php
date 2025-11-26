<x-auth-layout>
    <x-auth-card>

        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link.')" />

        <x-auth-alert :status="session('status')" class="alert-success" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-2">
        @csrf

            <x-input
                label="Email Address"
                wire:model="email"
                name="email"
                icon="o-envelope"
                placeholder="email@example.com"
            />

            <div class="space-y-6 my-4">
                <x-button label="Email password reset link" type="submit" class="btn-primary w-full" />

                <div class="space-x-1 text-center text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Back to') }}</span>
                    <a href="{{ route('login') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('log in') }}</a>
                </div>
            </div>
        </form>

    </x-auth-card>
</x-auth-layout>
