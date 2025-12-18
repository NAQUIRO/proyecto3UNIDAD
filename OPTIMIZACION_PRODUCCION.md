# Optimizaciones para Producción

## 🚀 Optimizaciones de Laravel

### 1. Cachear Configuración

```bash
# Cachear configuración (hace la app más rápida)
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache
```

**Nota:** Después de cambiar `.env`, ejecutar:
```bash
php artisan config:clear
php artisan config:cache
```

### 2. Optimizar Autoloader de Composer

```bash
# En producción, usar --optimize-autoloader --no-dev
composer install --optimize-autoloader --no-dev
```

### 3. Optimizar Base de Datos

#### Índices
Asegúrate de que las tablas importantes tengan índices:

```php
// En migraciones
$table->index(['congress_id', 'user_id']);
$table->index('status');
$table->index('created_at');
```

#### Consultas Eager Loading
Siempre usar `with()` para evitar N+1 queries:

```php
// ❌ Mal
$papers = Paper::all();
foreach ($papers as $paper) {
    echo $paper->author->name; // N+1 query
}

// ✅ Bien
$papers = Paper::with('author')->get();
foreach ($papers as $paper) {
    echo $paper->author->name; // 1 query
}
```

### 4. Cachear Consultas Frecuentes

```php
// Cachear resultados de consultas costosas
$congresses = Cache::remember('active_congresses', 3600, function () {
    return Congress::where('is_active', true)->get();
});
```

### 5. Optimizar Assets

#### Comprimir CSS/JS
```bash
npm run build:prod
```

#### Usar CDN para Assets Estáticos
Configurar en `.env`:
```env
ASSET_URL=https://cdn.tudominio.com
```

### 6. Optimizar Imágenes

#### Usar Storage Optimizado
```php
// Comprimir imágenes al subir
use Intervention\Image\Facades\Image;

$image = Image::make($request->file('image'))
    ->resize(1200, null, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })
    ->encode('jpg', 85);
```

### 7. Configurar OPcache

En `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0  # En producción
opcache.revalidate_freq=0
```

### 8. Configurar Session Driver

Para mejor performance, usar `database` o `redis`:
```env
SESSION_DRIVER=database
```

### 9. Queue Jobs para Tareas Pesadas

```php
// En lugar de procesar inmediatamente
ProcessLargeFileJob::dispatch($file);

// En lugar de
$this->processLargeFile($file); // Bloquea la request
```

### 10. Limitar Logs

En producción, solo loguear errores:
```env
LOG_LEVEL=error
```

## 📊 Monitoreo

### 1. Laravel Telescope (Solo Desarrollo)
```bash
# No instalar en producción
composer require laravel/telescope --dev
```

### 2. Laravel Debugbar (Solo Desarrollo)
```bash
# No instalar en producción
composer require barryvdh/laravel-debugbar --dev
```

### 3. Monitoreo de Performance

Usar herramientas como:
- New Relic
- Sentry
- Laravel Pulse (Laravel 11+)

## 🔒 Seguridad

### 1. Variables de Entorno

```env
APP_DEBUG=false
APP_ENV=production
```

### 2. Headers de Seguridad

En `bootstrap/app.php` o middleware:
```php
return $next($request)
    ->header('X-Frame-Options', 'SAMEORIGIN')
    ->header('X-Content-Type-Options', 'nosniff')
    ->header('X-XSS-Protection', '1; mode=block');
```

### 3. Rate Limiting

```php
// En routes/web.php
Route::middleware(['throttle:60,1'])->group(function () {
    // Rutas públicas
});
```

### 4. Validar Inputs

Siempre validar datos de entrada:
```php
$validated = $request->validate([
    'email' => 'required|email|max:255',
    'name' => 'required|string|max:255',
]);
```

## 🗄️ Base de Datos

### 1. Índices Estratégicos

```php
// En migraciones
$table->index(['congress_id', 'status', 'created_at']);
```

### 2. Particionamiento (Para tablas grandes)

Para tablas con millones de registros, considerar particionamiento.

### 3. Limpiar Datos Antiguos

```php
// En un comando o job
SessionComment::where('created_at', '<', now()->subYear())->delete();
```

## 📦 Assets

### 1. Minificar CSS/JS

Vite lo hace automáticamente con `npm run build:prod`

### 2. Lazy Loading de Imágenes

```html
<img src="image.jpg" loading="lazy" alt="...">
```

### 3. Preload de Assets Críticos

```html
<link rel="preload" href="/css/critical.css" as="style">
```

## 🚀 Servidor

### 1. PHP-FPM Optimización

```ini
; php-fpm.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
```

### 2. Nginx/Apache Optimización

**Nginx:**
```nginx
gzip on;
gzip_types text/css application/javascript image/svg+xml;
gzip_min_length 1000;
```

**Apache:**
```apache
LoadModule deflate_module modules/mod_deflate.so
<Location />
    SetOutputFilter DEFLATE
</Location>
```

### 3. HTTP/2

Habilitar HTTP/2 en el servidor web para mejor performance.

## 📝 Checklist de Optimización

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Configuración cacheada (`config:cache`)
- [ ] Rutas cacheadas (`route:cache`)
- [ ] Vistas cacheadas (`view:cache`)
- [ ] Autoloader optimizado (`composer --optimize-autoloader`)
- [ ] Assets compilados (`npm run build:prod`)
- [ ] OPcache habilitado
- [ ] Índices en BD creados
- [ ] Logs configurados a nivel `error`
- [ ] Queue workers configurados
- [ ] Storage symlink creado
- [ ] Permisos correctos en `storage/` y `bootstrap/cache/`
- [ ] HTTPS configurado
- [ ] Headers de seguridad configurados

