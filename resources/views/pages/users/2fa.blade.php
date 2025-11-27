<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Symfony\Component\HttpFoundation\Response;

new class extends Component {
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;

    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);

        if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
            $disableTwoFactorAuthentication(auth()->user());
        }

        $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->loadSetupData();

        $this->showModal = true;
    }

    /**
     * Load the two-factor authentication setup data for the user.
     */
    private function loadSetupData(): void
    {
        $user = auth()->user();

        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->closeModal();

        $this->twoFactorEnabled = true;
    }

    /**
     * Reset two-factor verification state.
     */
    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }

    /**
     * Close the two-factor authentication modal.
     */
    public function closeModal(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'showModal',
            'showVerificationStep',
        );

        $this->resetErrorBag();

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    /**
     * Get the current modal configuration state.
     */
    public function getModalConfigProperty(): array
    {
        if ($this->twoFactorEnabled) {
            return [
                'title' => __('Two-Factor Authentication Enabled'),
                'description' => __('Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.'),
                'buttonText' => __('Close'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify Authentication Code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Continue'),
            ];
        }

        return [
            'title' => __('Enable Two-Factor Authentication'),
            'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
} ?>

<section class="w-full">
    <x-header title="Two Factor Authentication" subtitle="Manage your two-factor authentication settings" separator />

    <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
        @if ($twoFactorEnabled)
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <x-badge value="{{ __('Enabled') }}" class="badge-success" />
                </div>

                <p>
                    {{ __('With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                </p>

                <livewire:recovery-codes :$requiresConfirmation/>

                <div class="flex justify-start">
                    <x-button
                        label="{{ __('Disable 2FA') }}"
                        class="btn-error"
                        icon="o-shield-exclamation"
                        wire:click="disable"
                    />
                </div>
            </div>
        @else
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <x-badge value="{{ __('Disabled') }}" class="badge-error" />
                </div>

                <p class="text-gray-500">
                    {!! __('When you enable two-factor authentication, you will be prompted for a secure pin during login. <br />This pin can be retrieved from a TOTP-supported application on your phone.') !!}
                </p>

                <x-button
                    label="{{ __('Enable 2FA') }}"
                    class="btn-primary"
                    icon="o-shield-check"
                    wire:click="enable"
                />
            </div>
        @endif
    </div>

    <x-modal wire:model="showModal" class="backdrop-blur">
        <div class="mb-5">
             <div class="flex flex-col items-center space-y-4">
                 <div class="p-4 bg-gray-100 rounded-full dark:bg-gray-800">
                    <x-icon name="o-qr-code" class="w-12 h-12 text-gray-500 dark:text-gray-400" />
                 </div>
                 <div class="space-y-2 text-center">
                    <h3 class="text-lg font-bold">{{ $this->modalConfig['title'] }}</h3>
                    <p>{{ $this->modalConfig['description'] }}</p>
                </div>
             </div>
        </div>

        @if ($showVerificationStep)
            <div class="space-y-6">
                <div class="flex flex-col items-center space-y-3">
                    <x-pin size="6" wire:model="code" />
                    @error('code')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center space-x-3 justify-between gap-4">
                    <x-button
                        label="{{ __('Back') }}"
                        wire:click="resetVerification"
                        class="flex-1"
                    />

                    <x-button
                        label="{{ __('Confirm') }}"
                        class="btn-primary flex-1"
                        wire:click="confirmTwoFactor"
                    />
                </div>
            </div>
        @else
            @error('setupData')
                <x-alert icon="o-x-circle" class="alert-error mb-4" title="{{ $message }}" />
            @enderror

            <div class="flex justify-center my-4">
                <div class="relative w-64 overflow-hidden border rounded-lg border-gray-200 dark:border-gray-700 aspect-square">
                    @empty($qrCodeSvg)
                        <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-700 animate-pulse">
                            <span class="loading loading-spinner"></span>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full p-4">
                            <div class="bg-white p-3 rounded">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    @endempty
                </div>
            </div>

            <div>
                <x-button
                    class="w-full btn-primary"
                    wire:click="showVerificationIfNecessary"
                    label="{{ $this->modalConfig['buttonText'] }}"
                    :disabled="$errors->has('setupData')"
                />
            </div>

            <div class="space-y-4 mt-4">
                <div class="relative flex items-center justify-center w-full">
                    <div class="absolute inset-0 w-full h-px top-1/2 bg-gray-200 dark:bg-gray-600"></div>
                    <span class="relative px-2 text-sm bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        {{ __('or, enter the code manually') }}
                    </span>
                </div>

                <div
                    class="flex items-center space-x-2"
                    x-data="{
                        copied: false,
                        async copy() {
                            try {
                                await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                this.copied = true;
                                setTimeout(() => this.copied = false, 1500);
                            } catch (e) {
                                console.warn('Could not copy to clipboard');
                            }
                        }
                    }"
                >
                    <div class="flex items-stretch w-full border rounded-xl dark:border-gray-700">
                        @empty($manualSetupKey)
                            <div class="flex items-center justify-center w-full p-3 bg-gray-100 dark:bg-gray-700">
                                <span class="loading loading-spinner loading-xs"></span>
                            </div>
                        @else
                            <input
                                type="text"
                                readonly
                                value="{{ $manualSetupKey }}"
                                class="w-full p-3 bg-transparent outline-none text-gray-900 dark:text-gray-100"
                            />

                            <button
                                @click="copy()"
                                class="px-3 transition-colors border-l cursor-pointer border-gray-200 dark:border-gray-600"
                            >
                                <x-icon name="o-document-duplicate" x-show="!copied" />
                                <x-icon name="o-check" x-show="copied" class="text-green-500" />
                            </button>
                        @endempty
                    </div>
                </div>
            </div>
        @endif
    </x-modal>
</section>
