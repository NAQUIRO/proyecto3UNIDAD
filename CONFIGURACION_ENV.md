# Configuración del archivo .env

El archivo `.env` ha sido creado exitosamente. A continuación se detallan las configuraciones importantes que debes verificar y ajustar según tu entorno:

## Configuraciones Básicas

```env
APP_NAME="Sistema de Gestión de Congresos"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
```

## Base de Datos (MySQL/MariaDB)

**IMPORTANTE:** Configura tu base de datos MySQL. Por defecto está configurado para XAMPP:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=congresos_db
DB_USERNAME=root
DB_PASSWORD=
```

**Pasos para configurar:**
1. Crea la base de datos en MySQL:
   ```sql
   CREATE DATABASE congresos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Ajusta `DB_USERNAME` y `DB_PASSWORD` si no usas `root` sin contraseña.

3. Ejecuta las migraciones:
   ```bash
   php artisan migrate
   ```

4. Ejecuta los seeders:
   ```bash
   php artisan db:seed
   ```

## Configuración de Correo

Para desarrollo local, puedes usar Mailpit (incluido en Laravel):

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@congresos.local"
MAIL_FROM_NAME="${APP_NAME}"
```

Para producción, configura un servidor SMTP real:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-servidor.com
MAIL_PORT=587
MAIL_USERNAME=tu-usuario
MAIL_PASSWORD=tu-contraseña
MAIL_ENCRYPTION=tls
```

## Pasarelas de Pago

### Stripe
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### PayPal
```env
PAYPAL_CLIENT_ID=tu_client_id
PAYPAL_CLIENT_SECRET=tu_client_secret
PAYPAL_MODE=sandbox  # o 'live' para producción
```

### MercadoPago
```env
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_WEBHOOK_SECRET=tu_webhook_secret
```

## Almacenamiento de Archivos

```env
FILESYSTEM_DISK=public
```

Asegúrate de que el enlace simbólico esté creado:
```bash
php artisan storage:link
```

## Configuración de Sesiones y Cache

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

## Configuraciones Adicionales

### DomPDF (Generación de PDFs)
```env
DOMPDF_ENABLE_REMOTE=true
DOMPDF_ENABLE_AUTOLOAD=true
```

### Excel (Exportación de reportes)
```env
EXCEL_EXPORTS_CHUNK=1000
```

## Verificación

Después de configurar el `.env`, ejecuta:

```bash
# Limpiar caché de configuración
php artisan config:clear

# Verificar que todo esté correcto
php artisan about
```

## Notas Importantes

1. **Nunca subas el archivo `.env` al repositorio** - Ya está en `.gitignore`
2. **En producción**, cambia:
   - `APP_DEBUG=false`
   - `APP_ENV=production`
   - Usa credenciales reales de base de datos y correo
3. **Genera una nueva clave** si clonas el proyecto:
   ```bash
   php artisan key:generate
   ```

## Solución de Problemas

Si tienes problemas de conexión a la base de datos:
1. Verifica que MySQL esté corriendo
2. Verifica las credenciales en `.env`
3. Prueba la conexión:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

Si tienes problemas con el almacenamiento:
```bash
php artisan storage:link
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```




