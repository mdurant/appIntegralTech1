@props([
'sidebar' => false,
])

@php
    $user = auth()->user();
    $showFantasyName = $user && $user->shouldShowFantasyName();
    $displayName = $showFantasyName ? $user->fantasy_name : 'Integral Service Tech';
    $showLogo = $showFantasyName && $user->avatarUrl();
@endphp

@if($sidebar)
    @if ($showFantasyName)
        <flux:sidebar.brand :name="$displayName" {{ $attributes }}>
            <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground overflow-hidden">
                @if ($showLogo)
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $displayName }}" class="size-full object-cover" />
                @else
                    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                @endif
            </x-slot>
        </flux:sidebar.brand>
    @else
        {{-- Ocultar para roles guest y client --}}
        @if (!$user || (!$user->isGuest() && !$user->isClient()))
            <flux:sidebar.brand name="Integral Service Tech" {{ $attributes }}>
                <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
                    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                </x-slot>
            </flux:sidebar.brand>
        @endif
    @endif
@else
    @if ($showFantasyName)
        <flux:brand :name="$displayName" {{ $attributes }}>
            <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground overflow-hidden">
                @if ($showLogo)
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $displayName }}" class="size-full object-cover" />
                @else
                    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                @endif
            </x-slot>
        </flux:brand>
    @else
        {{-- Ocultar para roles guest y client --}}
        @if (!$user || (!$user->isGuest() && !$user->isClient()))
            <flux:brand name="Integral Service Tech" {{ $attributes }}>
                <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
                    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                </x-slot>
            </flux:brand>
        @endif
    @endif
@endif
