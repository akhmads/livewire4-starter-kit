<x-auth-layout>
    <x-auth-card>

        <x-auth-header :title="__('Re-send Email Verification')" :description="__('Please verify your email address by clicking on the link we just emailed to you.')" />

        @if (session('status') == 'verification-link-sent')
            <x-auth-alert :status="__('A new verification link has been sent to the email address you provided during registration.')" class="alert-success" />
        @endif

        <div class="flex flex-col gap-2 pt-8">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-button
                    :label="__('Resend verification email')"
                    type="submit"
                    class="w-full btn btn-primary"
                />
            </form>

            <div class="divider">Or</div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-button
                    :label="__('Log Out')"
                    type="submit"
                    class="w-full btn btn-error btn-soft"
                    icon="o-power"
                />
            </form>
        </div>

    </x-auth-card>
</x-auth-layout>
