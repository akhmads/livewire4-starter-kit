
<x-auth-layout>
    <x-auth-card>
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />
        <x-auth-alert :status="session('status')" class="alert-success" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-2">
        @csrf

            <x-input
                label="E-mail"
                name="email"
                wire:model.defer="email"
                icon="o-envelope"
                placeholder="email@example.com"
                value="{{ old('email') }}"
            />

            <x-input
                label="Password"
                name="password"
                wire:model.defer="password"
                type="password"
                icon="o-key"
                placeholder="Password"
            />

            <div class="space-y-4 my-4">
                <x-checkbox label="Remember me" name="remember" value="1" />
                <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary w-full" />
            </div>

            @if (Route::has('register'))
                <div class="space-x-1 text-center text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Don\'t have an account?') }}</span>
                    <a href="{{  route('register') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Sign up') }}</a>
                </div>
            @endif

            @if (Route::has('password.request'))
                <div class="space-x-1 text-center text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('Forgot your password?') }}</span>
                    <a href="{{  route('password.request') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Reset password') }}</a>
                </div>
            @endif

        </form>
    </x-auth-card>
</x-auth-layout>
