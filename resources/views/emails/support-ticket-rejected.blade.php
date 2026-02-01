<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Solicitud de reversa rechazada') }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1>{{ __('Solicitud de reversa rechazada') }}</h1>

    <p>{{ __('Hola') }}, {{ $user->name }}:</p>

    <p>{{ __('Te informamos que tu solicitud de reembolso ha sido rechazada.') }}</p>
    <ul>
        <li><strong>{{ __('Compra referida') }}:</strong> {{ $payment->serviceRequest?->title ?? __('Solicitud #:id', ['id' => $payment->service_request_id]) }}</li>
    </ul>

    @if (! empty($adminComment))
        <p><strong>{{ __('Comentario del administrador') }}:</strong> {{ $adminComment }}</p>
    @endif

    <p>{{ __('Si tienes dudas, puedes contactar a soporte.') }}</p>

    <p>{{ __('Saludos') }},<br>{{ config('app.name') }}</p>
</body>
</html>
