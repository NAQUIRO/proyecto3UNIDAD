# Configuración SMTP para Envío de Correos

## Configuración en .env

### Opción 1: Gmail (Recomendado para desarrollo)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="EventHub"
```

**Nota:** Para Gmail, necesitas generar una "Contraseña de aplicación" desde tu cuenta de Google.

### Opción 2: SendGrid (Recomendado para producción)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.tu-api-key-de-sendgrid
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="EventHub"
```

### Opción 3: Mailgun
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@tudominio.mailgun.org
MAIL_PASSWORD=tu-password-de-mailgun
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="EventHub"
```

### Opción 4: Servidor SMTP Personalizado
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.tudominio.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tudominio.com
MAIL_PASSWORD=tu-contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="EventHub"
```

## Configuración de Proveedores de Pago

### Stripe
```env
STRIPE_KEY=pk_test_...  # Clave pública (modo test)
STRIPE_SECRET=sk_test_...  # Clave secreta (modo test)
STRIPE_WEBHOOK_SECRET=whsec_...  # Secreto del webhook
```

**Para producción:**
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### PayPal
```env
PAYPAL_CLIENT_ID=tu_client_id
PAYPAL_SECRET=tu_secret
PAYPAL_SANDBOX=true  # false para producción
PAYPAL_WEBHOOK_ID=tu_webhook_id
```

### MercadoPago
```env
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_SANDBOX=true  # false para producción
```

## Verificación de Configuración

### Probar SMTP
```bash
# Enviar correo de prueba
php artisan tinker
>>> Mail::raw('Test email', function($message) { $message->to('tu-email@test.com')->subject('Test'); });
```

### Verificar Colas
```bash
# Ver jobs pendientes
php artisan queue:work --once

# Procesar colas continuamente
php artisan queue:listen
```

## Notas Importantes

1. **Seguridad**: Nunca commitees el archivo `.env` al repositorio
2. **Producción**: Usa servicios profesionales (SendGrid, Mailgun) para mejor deliverability
3. **Rate Limiting**: Los servicios SMTP tienen límites de envío por hora/día
4. **Webhooks**: Configura los webhooks en los dashboards de Stripe/PayPal/MercadoPago
5. **SPF/DKIM**: Configura registros DNS para mejorar deliverability

