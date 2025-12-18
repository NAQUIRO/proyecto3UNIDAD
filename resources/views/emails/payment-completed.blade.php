<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Completado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-top: none;
        }
        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pago Completado</h1>
    </div>
    <div class="content">
        <p>Hola {{ $user->name }},</p>
        
        <p>Tu pago ha sido procesado exitosamente.</p>

        <div class="payment-info">
            <h3>Detalles del Pago</h3>
            <p><strong>Congreso:</strong> {{ $congress->title }}</p>
            <p><strong>Monto:</strong> {{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
            <p><strong>Método de Pago:</strong> {{ ucfirst($payment->payment_method) }}</p>
            <p><strong>Fecha:</strong> {{ $payment->paid_at->format('d/m/Y H:i:s') }}</p>
        </div>

        @if($payment->receipt_url)
        <p>
            <a href="{{ url($payment->receipt_url) }}" class="button">Descargar Recibo</a>
        </p>
        @endif

        <p>Gracias por tu inscripción. Te esperamos en el congreso.</p>
    </div>
    <div class="footer">
        <p>Este correo fue enviado desde el sistema de gestión de congresos EventHub.</p>
    </div>
</body>
</html>

