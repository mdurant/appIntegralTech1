<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-app-bg text-app-text antialiased">
        {{ $slot }}

        {{-- Cookie Consent Modal --}}
        <livewire:cookie-consent-modal />

        @fluxScripts
    </body>
</html>

