<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Cartola Wallet') }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1>{{ __('Cartola Wallet') }} — {{ $periodLabel }}</h1>

    <p>{{ __('Hola') }}, {{ $user->name }}:</p>

    <p>{{ __('Adjuntamos tu cartola mensual de Wallet con el detalle de movimientos del período indicado.') }}</p>

    @if ($rutCode)
        <p><strong>{{ __('Código de acceso para la cartola') }}:</strong> {{ $rutCode }}</p>
        <p style="font-size: 12px; color: #666;">{{ __('Corresponde a los últimos 4 dígitos de tu RUT antes del dígito verificador. Utiliza este código para abrir el PDF adjunto si en el futuro se protege con contraseña.') }}</p>
    @endif

    <p>{{ __('Saludos') }},<br>{{ config('app.name') }}</p>
</body>
</html>
