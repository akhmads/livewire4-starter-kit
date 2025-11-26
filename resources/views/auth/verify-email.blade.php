<x-auth-layout>
    <x-auth-card>

        <x-auth-header :title="__('Re-send Email Verification')" :description="__('Please verify your email address by clicking on the link we just emailed to you.')" />

        @if (session('status') == 'verification-link-sent')
            <x-auth-alert :status="__('A new verification link has been sent to the email address you provided during registration.')" class="alert-success" />
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button :label="__('Resend verification email')" wire:click="sendVerification" spinner="sendVerification" class="w-full btn btn-primary" />
            {{-- <div class="divider">Or</div><x-button : label="__('Log out')" wire: click="logout" spinner="logout" class="w-full" /> --}}
            </form>
        </div>

        <div class="space-x-1 text-center text-sm">
            <span class="text-zinc-600 dark:text-zinc-400">{{ __('Back to') }}</span>
            <a href="{{ route('home') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Home') }}</a>
        </div>

    </x-auth-card>
</x-auth-layout>
