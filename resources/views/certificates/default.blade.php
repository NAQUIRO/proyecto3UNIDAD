<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado - {{ $certificate->certificate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
        }
        .certificate-container {
            background: white;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            position: relative;
            min-height: 800px;
        }
        .certificate-border {
            border: 8px solid #667eea;
            border-radius: 10px;
            padding: 50px;
            position: relative;
            min-height: 700px;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        .certificate-header h1 {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .certificate-header p {
            font-size: 18px;
            color: #666;
            font-style: italic;
        }
        .certificate-body {
            text-align: center;
            padding: 40px 0;
        }
        .certificate-body p {
            font-size: 20px;
            line-height: 1.8;
            margin-bottom: 20px;
            color: #333;
        }
        .user-name {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin: 30px 0;
            text-decoration: underline;
            text-decoration-color: #764ba2;
            text-underline-offset: 10px;
        }
        .congress-name {
            font-size: 28px;
            font-weight: bold;
            color: #764ba2;
            margin: 20px 0;
        }
        .certificate-type {
            font-size: 22px;
            color: #666;
            font-style: italic;
            margin: 20px 0;
        }
        .certificate-footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-section {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 60px auto 10px;
        }
        .signature-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .signature-title {
            font-size: 14px;
            color: #666;
        }
        .certificate-number {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .certificate-number p {
            font-size: 12px;
            color: #999;
        }
        .certificate-number strong {
            font-size: 14px;
            color: #667eea;
            letter-spacing: 2px;
        }
        .date-section {
            text-align: center;
            margin-top: 30px;
            font-size: 16px;
            color: #666;
        }
        .decorative-element {
            position: absolute;
            opacity: 0.1;
        }
        .decorative-top-left {
            top: 20px;
            left: 20px;
            font-size: 120px;
            color: #667eea;
        }
        .decorative-bottom-right {
            bottom: 20px;
            right: 20px;
            font-size: 120px;
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <!-- Elementos decorativos -->
            <div class="decorative-element decorative-top-left">★</div>
            <div class="decorative-element decorative-bottom-right">★</div>

            <!-- Encabezado -->
            <div class="certificate-header">
                <h1>CERTIFICADO</h1>
                <p>de {{ $typeName }}</p>
            </div>

            <!-- Cuerpo -->
            <div class="certificate-body">
                <p>Se certifica que</p>
                <div class="user-name">{{ $user->name }}</div>
                <p>ha participado en</p>
                <div class="congress-name">{{ $congress->title }}</div>
                <div class="certificate-type">
                    {{ $typeName }}
                </div>
                <p>Este certificado acredita la participación activa en el evento académico mencionado.</p>
            </div>

            <!-- Fecha -->
            <div class="date-section">
                <p>Emitido el {{ now()->format('d') }} de {{ now()->locale('es')->monthName }} de {{ now()->format('Y') }}</p>
            </div>

            <!-- Firmas -->
            <div class="certificate-footer">
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $congress->creator->name ?? 'Director' }}</div>
                    <div class="signature-title">Director del Congreso</div>
                </div>
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-name">Comité Organizador</div>
                    <div class="signature-title">EventHub</div>
                </div>
            </div>

            <!-- Número de certificado -->
            <div class="certificate-number">
                <p>Número de Certificado</p>
                <strong>{{ $certificate->certificate_number }}</strong>
            </div>
        </div>
    </div>
</body>
</html>

