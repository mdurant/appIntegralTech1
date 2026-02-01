<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Solicitud de reversa recibida') }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1>{{ __('Solicitud de reversa recibida') }}</h1>

    <p>{{ __('Hola') }}, {{ $user->name }}:</p>

    <p>{{ __('Hemos recibido tu solicitud de reversa por la siguiente compra:') }}</p>
    <ul>
        <li><strong>{{ __('Compra') }}:</strong> {{ $payment->serviceRequest?->title ?? __('Solicitud #:id', ['id' => $payment->service_request_id]) }}</li>
        <li><strong>{{ __('Monto') }}:</strong> ${{ number_format((float) $payment->amount, 0, ',', '.') }} CLP</li>
    </ul>

    <p>{{ __('Un administrador revisará tu solicitud y te notificaremos el resultado por correo.') }}</p>

    <p>{{ __('Saludos') }},<br>{{ config('app.name') }}</p>
</body>
</html>
