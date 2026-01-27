<section class="mx-auto w-full max-w-4xl space-y-6 px-4 py-6 sm:px-6">
    @if (session()->has('payment-success'))
        <flux:callout variant="success" class="mb-4">
            {{ session('payment-success') }}
        </flux:callout>
    @endif

    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Datos de Contacto Completos') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Información completa del cliente para que puedas contactarlo') }}
        </flux:text>
    </div>

    {{-- Información del Servicio --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
        <flux:heading size="md">{{ $serviceRequest->title }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ $serviceRequest->category?->name }}
            · {{ $serviceRequest->location_display }}
        </flux:text>
    </div>

    {{-- Datos de Contacto Completos --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-6">
        <flux:heading size="md">{{ __('Información de Contacto') }}</flux:heading>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <flux:text class="text-xs font-medium text-app-muted mb-1">{{ __('Nombre Completo') }}</flux:text>
                <flux:text class="text-base font-medium">{{ $serviceRequest->contact_name }}</flux:text>
            </div>
            <div>
                <flux:text class="text-xs font-medium text-app-muted mb-1">{{ __('Email') }}</flux:text>
                <a href="mailto:{{ $serviceRequest->contact_email }}" class="text-base text-brand-700 hover:text-brand-800">
                    {{ $serviceRequest->contact_email }}
                </a>
            </div>
            <div>
                <flux:text class="text-xs font-medium text-app-muted mb-1">{{ __('Teléfono') }}</flux:text>
                <a href="tel:{{ $serviceRequest->contact_phone }}" class="text-base text-brand-700 hover:text-brand-800">
                    {{ $serviceRequest->contact_phone }}
                </a>
            </div>
            <div>
                <flux:text class="text-xs font-medium text-app-muted mb-1">{{ __('Dirección Completa') }}</flux:text>
                <flux:text class="text-base">{{ $serviceRequest->address ?? '—' }}</flux:text>
            </div>
        </div>
    </div>

    {{-- Idea del Proyecto --}}
    @if ($serviceRequest->description || $serviceRequest->notes)
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
            <flux:heading size="md">{{ __('Idea del Proyecto') }}</flux:heading>
            @if ($serviceRequest->notes)
                <flux:text class="whitespace-pre-line">{{ $serviceRequest->notes }}</flux:text>
            @else
                <flux:text class="whitespace-pre-line">{{ $serviceRequest->description }}</flux:text>
            @endif
        </div>
    @endif

    {{-- Detalles del Formulario --}}
    @if ($serviceRequest->fieldAnswers->isNotEmpty())
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
            <flux:heading size="md">{{ __('Detalles Adicionales') }}</flux:heading>
            <div class="space-y-3">
                @foreach ($serviceRequest->fieldAnswers as $answer)
                    <div class="grid gap-2 sm:grid-cols-2">
                        <flux:text class="text-sm font-medium text-app-muted">
                            {{ $answer->field?->label ?? 'Campo' }}
                        </flux:text>
                        <flux:text class="text-sm">
                            @if ($answer->field && $answer->field->type?->value === 'select')
                                {{ $answer->field->options->firstWhere('value', $answer->value)?->label ?? $answer->value }}
                            @else
                                {{ $answer->value }}
                            @endif
                        </flux:text>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Imágenes --}}
    @if ($serviceRequest->attachments->isNotEmpty())
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
            <flux:heading size="md">{{ __('Imágenes del Proyecto') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($serviceRequest->attachments as $attachment)
                    <a href="{{ route('attachments.show', $attachment) }}" target="_blank" class="block">
                        <img
                            src="{{ route('attachments.show', $attachment) }}"
                            alt="Imagen {{ $loop->iteration }}"
                            class="h-32 w-full rounded-lg object-cover border border-app-border"
                        >
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Acciones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('services.show', $serviceRequest) }}" wire:navigate>
            <flux:button variant="ghost">{{ __('Volver') }}</flux:button>
        </a>
        <a href="mailto:{{ $serviceRequest->contact_email }}" class="inline-block">
            <flux:button variant="primary">{{ __('Enviar Email') }}</flux:button>
        </a>
        <a href="tel:{{ $serviceRequest->contact_phone }}" class="inline-block">
            <flux:button variant="primary">{{ __('Llamar') }}</flux:button>
        </a>
    </div>
</section>
