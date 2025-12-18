<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - {{ $receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .container {
            padding: 30px;
        }
        .receipt-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .receipt-info h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .details {
            margin: 30px 0;
        }
        .details h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .details-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #667eea;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $congress->title }}</h1>
        <p>Recibo de Pago</p>
    </div>

    <div class="container">
        <div class="receipt-info">
            <h2>Información del Recibo</h2>
            <div class="info-row">
                <span class="info-label">Número de Recibo:</span>
                <span class="info-value">{{ $receipt_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Emisión:</span>
                <span class="info-value">{{ $date }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    <span class="status-badge status-completed">Completado</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value">{{ ucfirst($payment->payment_method) }}</span>
            </div>
        </div>

        <div class="details">
            <h3>Información del Cliente</h3>
            <div class="info-row">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            @if($registration)
            <div class="info-row">
                <span class="info-label">Tipo de Registro:</span>
                <span class="info-value">{{ ucfirst($registration->role ?? 'Asistente') }}</span>
            </div>
            @endif
        </div>

        <div class="details">
            <h3>Detalles del Pago</h3>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->description ?? 'Inscripción al Congreso' }}</td>
                        <td>1</td>
                        <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                        <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total">
            <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Total a Pagar:</div>
            <div class="total-amount">
                {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
            </div>
        </div>

        <div class="footer">
            <p>Este es un recibo generado automáticamente por el sistema de gestión de congresos.</p>
            <p>Para consultas, contacte a: {{ $congress->creator->email ?? 'admin@congresos.com' }}</p>
            <p>Recibo generado el {{ $date }}</p>
        </div>
    </div>
</body>
</html>

