# Guía de Despliegue FTP para EventHub

## 📋 Requisitos Previos

- Acceso FTP/SFTP al servidor
- PHP 8.2+ instalado en el servidor
- Composer instalado localmente
- Node.js y npm instalados localmente
- Base de datos MySQL/PostgreSQL configurada

## 🚀 Pasos de Despliegue

### 1. Preparación Local

#### 1.1. Compilar Assets
```bash
# Instalar dependencias de Node
npm install

# Compilar assets para producción
npm run build:prod
```

#### 1.2. Optimizar para Producción
```bash
# Limpiar caché
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimizar autoloader
composer install --optimize-autoloader --no-dev

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 1.3. Generar Key de Aplicación
```bash
php artisan key:generate
```

### 2. Configuración del .env para Producción

Crea un archivo `.env.production` con las siguientes configuraciones:

```env
APP_NAME="EventHub"
APP_ENV=production
APP_KEY=base64:TU_APP_KEY_AQUI
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario_db
DB_PASSWORD=contraseña_db

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.tudominio.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tudominio.com
MAIL_PASSWORD=contraseña_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"

# Proveedores de Pago
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

PAYPAL_CLIENT_ID=tu_client_id
PAYPAL_SECRET=tu_secret
PAYPAL_SANDBOX=false

MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_SANDBOX=false
```

### 3. Subir Archivos al Servidor

#### 3.1. Archivos a Subir (vía FTP)

**Incluir:**
- `/app`
- `/bootstrap`
- `/config`
- `/database`
- `/public`
- `/resources`
- `/routes`
- `/storage` (crear directorio si no existe)
- `artisan`
- `composer.json`
- `composer.lock`
- `.env` (renombrar desde `.env.production`)

**Excluir (NO subir):**
- `/node_modules`
- `/vendor` (se instala en el servidor)
- `/.git`
- `/.idea`
- `/tests`
- `phpunit.xml`
- `.env.example`
- `package.json` (opcional, solo si necesitas compilar en el servidor)

#### 3.2. Estructura de Directorios en el Servidor

```
/public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          # Punto de entrada (DocumentRoot)
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── storage -> ../storage/app/public
├── resources/
├── routes/
├── storage/         # Permisos 775
│   ├── app/
│   │   └── public/ # Permisos 775
│   ├── framework/
│   └── logs/
├── vendor/          # Se instala en el servidor
├── .env
├── artisan
└── composer.json
```

### 4. Configuración en el Servidor

#### 4.1. Conectar vía SSH (si está disponible)

```bash
# Navegar al directorio del proyecto
cd /ruta/a/tu/proyecto

# Instalar dependencias de Composer
composer install --optimize-autoloader --no-dev

# Crear symlink de storage
php artisan storage:link

# Ejecutar migraciones
php artisan migrate --force

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 4.2. Permisos de Directorios

```bash
# Dar permisos a storage y cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4.3. Configuración del Servidor Web

**Apache (.htaccess en /public)**

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Nginx**

```nginx
server {
    listen 80;
    server_name tudominio.com;
    root /ruta/a/tu/proyecto/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Configuración de Colas (Opcional pero Recomendado)

Si usas colas, configura un cron job o supervisor:

**Cron Job:**
```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

**Supervisor (para queue:work):**
```ini
[program:eventhub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/tu/proyecto/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta/a/tu/proyecto/storage/logs/worker.log
stopwaitsecs=3600
```

### 6. Verificación Post-Despliegue

1. **Verificar que la aplicación carga:**
   - Visitar `https://tudominio.com`
   - Verificar que no hay errores 500

2. **Verificar storage:**
   - Subir una imagen de perfil
   - Verificar que se guarda en `storage/app/public`

3. **Verificar base de datos:**
   - Intentar crear un usuario
   - Verificar que se guarda en la BD

4. **Verificar colas:**
   - Enviar un email de prueba
   - Verificar que se procesa

5. **Verificar certificados:**
   - Generar un certificado de prueba
   - Verificar que se crea el PDF

### 7. Optimizaciones Adicionales

#### 7.1. Comprimir Assets
```bash
# En el servidor, comprimir CSS y JS
gzip -k public/css/*.css
gzip -k public/js/*.js
```

#### 7.2. Cachear Assets
Configurar headers de cache en el servidor web para assets estáticos.

#### 7.3. CDN (Opcional)
Considerar usar un CDN para assets estáticos (CSS, JS, imágenes).

## 🔒 Seguridad

### Checklist de Seguridad

- [ ] `APP_DEBUG=false` en producción
- [ ] `APP_ENV=production`
- [ ] Permisos correctos en `storage/` y `bootstrap/cache/`
- [ ] `.env` no accesible públicamente
- [ ] HTTPS configurado
- [ ] Firewall configurado
- [ ] Backups automáticos de BD
- [ ] Logs monitoreados

## 📝 Script de Despliegue Automatizado

Crea un script `deploy.sh`:

```bash
#!/bin/bash

echo "🚀 Iniciando despliegue..."

# Compilar assets
echo "📦 Compilando assets..."
npm run build:prod

# Optimizar
echo "⚡ Optimizando..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Subir archivos (ajustar según tu método)
echo "📤 Subiendo archivos..."
# rsync -avz --exclude 'node_modules' --exclude '.git' ./ usuario@servidor:/ruta/proyecto/

echo "✅ Despliegue completado!"
```

## 🐛 Solución de Problemas

### Error 500
- Verificar permisos de `storage/` y `bootstrap/cache/`
- Verificar logs en `storage/logs/laravel.log`
- Verificar que `.env` existe y está configurado

### Assets no cargan
- Verificar que `public/storage` es un symlink
- Verificar que Vite compiló los assets
- Verificar rutas en `vite.config.js`

### Base de datos no conecta
- Verificar credenciales en `.env`
- Verificar que el servidor permite conexiones desde tu IP
- Verificar que la BD existe

### Colas no procesan
- Verificar que `QUEUE_CONNECTION=database`
- Ejecutar `php artisan queue:work` manualmente
- Verificar logs de errores

## 📞 Soporte

Para problemas adicionales, consultar:
- Documentación de Laravel: https://laravel.com/docs
- Logs del servidor: `storage/logs/laravel.log`
- Logs del servidor web (Apache/Nginx)

