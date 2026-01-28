<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #{{ $bid->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h2 {
            color: #2563eb;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 150px;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .amount-section {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }
        .message-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
        }
        .message-section h3 {
            color: #2563eb;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-submitted {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-accepted {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-expired {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cotización de Servicio</h1>
            <div class="subtitle">IntegralTech Services</div>
        </div>

        <div class="info-section">
            <h2>Información de la Cotización</h2>
            <div class="info-row">
                <span class="info-label">Número de Cotización:</span>
                <span class="info-value">#{{ $bid->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Emisión:</span>
                <span class="info-value">{{ $bid->created_at->format('d/m/Y H:i') }}</span>
            </div>
            @if($bid->valid_until)
            <div class="info-row">
                <span class="info-label">Válida hasta:</span>
                <span class="info-value">{{ $bid->valid_until->format('d/m/Y') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    <span class="status-badge status-{{ $bid->status->value }}">
                        {{ ucfirst($bid->status->value) }}
                    </span>
                </span>
            </div>
        </div>

        <div class="info-section">
            <h2>Información del Servicio</h2>
            <div class="info-row">
                <span class="info-label">Título:</span>
                <span class="info-value">{{ $bid->serviceRequest->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Categoría:</span>
                <span class="info-value">{{ $bid->serviceRequest->category?->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Descripción:</span>
                <span class="info-value">{{ $bid->serviceRequest->description }}</span>
            </div>
            @if($bid->serviceRequest->location_display)
            <div class="info-row">
                <span class="info-label">Ubicación:</span>
                <span class="info-value">{{ $bid->serviceRequest->location_display }}</span>
            </div>
            @endif
        </div>

        <div class="info-section">
            <h2>Información del Profesional</h2>
            <div class="info-row">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ $bid->user->name }}</span>
            </div>
            @if($bid->user->fantasy_name)
            <div class="info-row">
                <span class="info-label">Razón Social:</span>
                <span class="info-value">{{ $bid->user->fantasy_name }}</span>
            </div>
            @endif
            @if($bid->user->rut)
            <div class="info-row">
                <span class="info-label">RUT:</span>
                <span class="info-value">{{ $bid->user->rut }}</span>
            </div>
            @endif
            @if($bid->user->giro_sii)
            <div class="info-row">
                <span class="info-label">Giro SII:</span>
                <span class="info-value">{{ $bid->user->giro_sii }}</span>
            </div>
            @endif
        </div>

        <div class="amount-section">
            <div class="amount-label">Monto Total</div>
            <div class="amount-value">${{ $bid->formatted_amount }} {{ $bid->currency }}</div>
        </div>

        @if($bid->message)
        <div class="message-section">
            <h3>Mensaje del Profesional</h3>
            <p>{{ $bid->message }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Este documento fue generado automáticamente por IntegralTech Services</p>
            <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
