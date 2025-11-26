<x-auth-layout>
    <x-auth-card>

        <x-auth-header :title="__('Thank You')" :description="__('Verification was successful.')" />

        <div class="space-x-1 text-center text-sm">
            <span class="text-zinc-600 dark:text-zinc-400">{{ __('Your email has been verified.') }}</span>
            <a href="{{ route('home') }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Go to home') }}</a>.
        </div>

    </x-auth-card>
</x-auth-layout>
