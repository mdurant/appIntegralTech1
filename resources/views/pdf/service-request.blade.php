<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ __('Solicitud') }} #{{ $request->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        h2 { font-size: 14px; margin-top: 16px; margin-bottom: 6px; }
        .section { margin-bottom: 16px; }
        .label { font-weight: bold; }
        .images { margin-top: 12px; }
        .images img { max-width: 120px; max-height: 120px; margin-right: 8px; margin-bottom: 8px; vertical-align: top; }
    </style>
</head>
<body>
    <h1>{{ __('Solicitud de servicio') }} #{{ $request->id }}</h1>

    <div class="section">
        <h2>{{ __('Cliente / Contacto') }}</h2>
        <p><span class="label">{{ __('Nombre') }}:</span> {{ $request->contact_name }}</p>
        <p><span class="label">{{ __('Email') }}:</span> {{ $request->contact_email }}</p>
        <p><span class="label">{{ __('Teléfono') }}:</span> {{ $request->contact_phone }}</p>
    </div>

    <div class="section">
        <h2>{{ __('Dirección / Ubicación') }}</h2>
        <p><span class="label">{{ __('Dirección') }}:</span> {{ $request->address ?? '—' }}</p>
        <p><span class="label">{{ __('Localización') }}:</span> {{ $request->location_text ?? ($request->commune?->name ?? '') . ($request->region?->name ? ' - ' . $request->region->name : '') ?: '—' }}</p>
    </div>

    <div class="section">
        <h2>{{ __('Detalle del servicio') }}</h2>
        <p><span class="label">{{ __('Categoría') }}:</span> {{ $request->category?->parent?->name }} / {{ $request->category?->name }}</p>
        <p><span class="label">{{ __('Trabajo a realizar') }}:</span> {{ $request->title }}</p>
        <p><span class="label">{{ __('Descripción') }}:</span></p>
        <p>{{ nl2br(e($request->description ?? '')) }}</p>
        @if ($request->notes && $request->notes !== $request->description)
            <p><span class="label">{{ __('Nota del particular') }}:</span></p>
            <p>{{ nl2br(e($request->notes)) }}</p>
        @endif
    </div>

    @if ($request->attachments->isNotEmpty())
        <div class="section images">
            <h2>{{ __('Imágenes adjuntas') }}</h2>
            @foreach ($request->attachments as $att)
                @php
                    $fullPath = storage_path('app/public/' . $att->path);
                @endphp
                @if (is_file($fullPath))
                    <img src="{{ $fullPath }}" alt="">
                @endif
            @endforeach
        </div>
    @endif

    <p style="margin-top: 24px; font-size: 10px; color: #666;">
        {{ __('Generado el') }} {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>
