<x-settings.layout heading="{{ __('Sesiones Activas') }}" subheading="{{ __('Lista de sesiones activas. Puedes terminarlas haciendo clic en el botón de eliminar.') }}">
    <div class="space-y-6">
        {{-- Sesión Actual --}}
        @if($currentSession)
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <flux:heading size="md" class="mb-4">{{ __('Sesión Actual') }}</flux:heading>
                <div class="flex items-center gap-4 rounded-lg bg-purple-50/50 p-4 dark:bg-purple-900/10">
                    <div class="grid size-12 shrink-0 place-items-center rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                        @if($currentSession->device_type === 'mobile')
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        @elseif($currentSession->device_type === 'tablet')
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        @else
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <flux:heading size="sm" class="font-semibold">{{ $currentSession->device_name ?? __('Dispositivo Desconocido') }}</flux:heading>
                        <flux:text class="mt-1 text-sm text-app-muted">
                            {{ $currentSession->browser_name }} {{ $currentSession->browser_version }}
                        </flux:text>
                        <flux:text class="mt-1 text-xs text-app-muted">
                            {{ $currentSession->location ?? 'Ubicación no disponible' }} | {{ __('En línea') }}
                        </flux:text>
                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-app-muted">
                            <span>{{ __('IP') }}: {{ $currentSession->ip_address }}</span>
                            <span>{{ $currentSession->operating_system }} {{ $currentSession->os_version }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Otras Sesiones Activas --}}
        @if($otherSessions->count() > 0)
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <flux:heading size="md" class="mb-4">{{ __('Otras Sesiones Activas') }}</flux:heading>
                <div class="space-y-0 divide-y divide-app-border">
                    @foreach($otherSessions as $session)
                        <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-app-muted/20">
                                    @if($session->operating_system === 'iOS' || $session->operating_system === 'macOS')
                                        <svg class="size-5 text-app-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.5 1.74-1.66 3.43zM13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                        </svg>
                                    @elseif($session->operating_system === 'Android')
                                        <svg class="size-5 text-app-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997 0-.5511.4482-.9993.9993-.9993.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997 0-.5511.4482-.9993.9993-.9993.551 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2439 13.8533 7.8508 12 7.8508s-3.5902.3931-5.1349 1.0989L4.8429 5.4467a.4161.4161 0 00-.5676-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.186.8533 12.3062 0 13.8176v1.4307h24v-1.4307c-.8533-1.5114-2.6889-2.6316-5.1225-3.4963m-1.5324 2.7294H4.6558c.4056-.8919 1.1284-1.6877 2.0818-2.2516 1.7838-1.0872 4.0124-1.0872 5.7962 0 .9534.5639 1.6762 1.3597 2.0818 2.2516"/>
                                        </svg>
                                    @elseif(str_contains(strtolower($session->operating_system ?? ''), 'linux') || str_contains(strtolower($session->operating_system ?? ''), 'ubuntu') || str_contains(strtolower($session->operating_system ?? ''), 'debian') || str_contains(strtolower($session->operating_system ?? ''), 'fedora') || str_contains(strtolower($session->operating_system ?? ''), 'centos'))
                                        <svg class="size-5 text-app-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.504 0c-.155 0-.315.008-.48.021-4.226.333-3.105 4.807-3.17 6.298-.076 1.092-.3 1.953-1.05 3.02-.885 1.051-2.127 2.75-2.716 4.521-.278.832-.41 1.684-.287 2.489a.424.424 0 00-.11.135c-.26.26-.495.61-.71 1.03-.797 1.592-1.186 3.315-1.187 5.084-.002 3.854 3.18 6.974 7.052 6.978 3.873.004 7.05-3.116 7.053-6.97.002-1.769-.39-3.492-1.188-5.085a5.783 5.783 0 00-.708-1.028.424.424 0 00-.11-.135c.123-.805-.01-1.657-.287-2.49-.59-1.77-1.83-3.47-2.716-4.52-.75-1.068-1.074-1.93-1.05-3.021.065-1.491 1.056-5.965-3.17-6.298A3.567 3.567 0 0012.504 0zm-.97 5.407c.893.032 1.705.603 2.111 1.402.407.8.327 1.714-.2 2.446-.527.733-1.413 1.146-2.306 1.114-.893-.032-1.705-.603-2.111-1.402-.407-.8-.327-1.714.2-2.446.527-.732 1.413-1.145 2.306-1.114zm6.597 3.147c.695.025 1.353.405 1.717 1.011.365.606.39 1.357.066 1.988-.324.63-.916 1.05-1.611 1.025-.695-.025-1.353-.405-1.717-1.011-.365-.606-.39-1.357-.066-1.988.324-.63.916-1.05 1.611-1.025zm-11.188 0c.695.025 1.353.405 1.717 1.011.365.606.39 1.357.066 1.988-.324.63-.916 1.05-1.611 1.025-.695-.025-1.353-.405-1.717-1.011-.365-.606-.39-1.357-.066-1.988.324-.63.916-1.05 1.611-1.025zm11.228 4.665c.883.032 1.721.405 2.333.998 2.024 1.917 2.08 5.044.125 7.032-1.956 1.988-5.136 2.045-7.16.128-.883-.836-1.328-1.957-1.276-3.104-.008-.155.008-.315.021-.48.333-2.226 4.807-1.115 6.298-1.05 1.092.3 1.953 1.05 3.02.832.59 1.77 1.83 2.49 2.716.46.59.832 1.23 1.105 1.9.123.805.01 1.657-.287 2.49-.59 1.77-1.83 3.47-2.716 4.52-.75 1.068-1.074 1.93-1.05 3.021.065 1.491 1.056 5.965-3.17 6.298a3.567 3.567 0 01-.48.021c-1.769.002-3.492-.39-5.085-1.188a5.783 5.783 0 01-1.028-.708.424.424 0 01-.135-.11c-.805.123-1.657-.01-2.49-.287-1.77-.59-3.47-1.83-4.52-2.716-1.068-.75-1.93-1.074-3.021-1.05-1.491-.065-5.965-1.056-6.298 3.17a3.567 3.567 0 01-.021.48c-.004 3.873 3.116 7.05 6.97 7.053 1.769.002 3.492-.39 5.085-1.188.42-.215.77-.45 1.03-.71a.424.424 0 01.135-.11c.63.324 1.357.39 1.988.066.63-.324 1.05-.916 1.025-1.611-.025-.695-.405-1.353-1.011-1.717-.606-.365-1.357-.39-1.988-.066-.63.324-1.05.916-1.025 1.611.025.695.405 1.353 1.011 1.717.606.365 1.357.39 1.988.066.63-.324 1.05-.916 1.025-1.611-.025-.695-.405-1.353-1.011-1.717-.606-.365-1.357-.39-1.988-.066z"/>
                                        </svg>
                                    @elseif($session->operating_system === 'Windows' || $session->operating_system === 'Windows Phone')
                                        <svg class="size-5 text-app-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 12V6.75l6-1.313v6.187L3 12zm17-9v8.75l-10 .15V5.21L20 3zM3 13l6 .094v6.531L3 18.75V13zm17 .25V22l-10-1.813v-7.156l10-.15z"/>
                                        </svg>
                                    @elseif($session->operating_system === 'Unix' || $session->operating_system === 'Solaris' || $session->operating_system === 'FreeBSD' || $session->operating_system === 'OpenBSD' || $session->operating_system === 'NetBSD')
                                        <svg class="size-5 text-app-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.504 0c-.155 0-.315.008-.48.021-4.226.333-3.105 4.807-3.17 6.298-.076 1.092-.3 1.953-1.05 3.02-.885 1.051-2.127 2.75-2.716 4.521-.278.832-.41 1.684-.287 2.489a.424.424 0 00-.11.135c-.26.26-.495.61-.71 1.03-.797 1.592-1.186 3.315-1.187 5.084-.002 3.854 3.18 6.974 7.052 6.978 3.873.004 7.05-3.116 7.053-6.97.002-1.769-.39-3.492-1.188-5.085a5.783 5.783 0 00-.708-1.028.424.424 0 00-.11-.135c.123-.805-.01-1.657-.287-2.49-.59-1.77-1.83-3.47-2.716-4.52-.75-1.068-1.074-1.93-1.05-3.021.065-1.491 1.056-5.965-3.17-6.298A3.567 3.567 0 0012.504 0z"/>
                                        </svg>
                                    @elseif($session->operating_system === 'Chrome OS')
                                        <svg class="size-5 text-app-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm1.193 5.982l4.69 2.343v6.348l-4.69 2.343V5.982zm-2.386 0v10.034L6.117 14.673V8.325l4.69-2.343z"/>
                                        </svg>
                                    @else
                                        <svg class="size-5 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <flux:heading size="sm" class="font-semibold">{{ $session->device_name ?? __('Dispositivo Desconocido') }}</flux:heading>
                                    <flux:text class="mt-1 text-sm text-app-muted">
                                        {{ $session->browser_name }} {{ $session->browser_version }}
                                    </flux:text>
                                    <flux:text class="mt-1 text-xs text-app-muted">
                                        {{ $session->location ?? 'Ubicación no disponible' }} | 
                                        {{ $session->last_activity ? $session->last_activity->format('d/m/Y') : __('Fecha no disponible') }}
                                    </flux:text>
                                    <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-app-muted">
                                        <span>{{ __('IP') }}: {{ $session->ip_address }}</span>
                                        <span>{{ $session->operating_system }} {{ $session->os_version }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button 
                                    type="button"
                                    class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors dark:text-red-400 dark:hover:text-red-300"
                                    wire:click="terminateSession('{{ $session->session_id }}')"
                                    wire:confirm="{{ __('¿Estás seguro de que deseas terminar esta sesión? Esta acción cerrará la sesión en ese dispositivo.') }}"
                                >
                                    {{ __('Terminar') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-xl border border-app-border bg-app-surface p-6 text-center">
                <flux:text class="text-app-muted">{{ __('No hay otras sesiones activas.') }}</flux:text>
            </div>
        @endif

        {{-- Disclaimer --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800 dark:bg-amber-900/10">
            <flux:text class="text-sm text-amber-800 dark:text-amber-200">
                <strong>{{ __('Importante:') }}</strong> {{ __('Los datos borrados temporalmente pero almacenados y custodiados con altos estándares de seguridad exclusivamente para fines Forenses o legales que estime alguna autoridad en Chile por IntegralTech Solutions.') }}
            </flux:text>
        </div>
    </div>
</x-settings.layout>
