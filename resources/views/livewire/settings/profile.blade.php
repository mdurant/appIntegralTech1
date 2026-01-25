<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex items-center gap-4">
                <div class="size-12 overflow-hidden rounded-full bg-zinc-100 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                    @if (auth()->user()->avatarUrl())
                        <img
                            src="{{ auth()->user()->avatarUrl() }}"
                            alt="{{ auth()->user()->name }}"
                            class="size-full object-cover"
                        />
                    @else
                        <div class="grid size-full place-items-center text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-1">
                    <flux:text class="text-sm font-medium">{{ __('Avatar') }}</flux:text>
                    <input
                        type="file"
                        wire:model="avatar"
                        accept="image/*"
                        class="block w-full text-sm text-zinc-700 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-200 dark:hover:file:bg-zinc-700"
                    />
                    @error('avatar')
                        <flux:text class="text-sm !text-danger">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification code.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-code-sent')
                            <flux:text class="mt-2 font-medium !text-success">
                                {{ __('A new verification code has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
