# Fase 2: El Micrositio y Multi-tenancy (Lógica de Negocio) - COMPLETADA

## ✅ Componentes Implementados

### 1. TenantService (`app/Services/TenantService.php`)
Servicio singleton que gestiona el congreso actual. Detecta el congreso mediante:
- **Dominio personalizado**: Si el congreso tiene `custom_domain` configurado
- **Subdominio**: Extrae el subdominio del host (ej: `evento.misistema.com`)
- **Slug en URL**: Detecta el slug en rutas como `/evento/{slug}`

**Métodos principales:**
- `getCurrentCongress()`: Obtiene el congreso actual
- `setCurrentCongress()`: Establece manualmente el congreso
- `hasCurrentCongress()`: Verifica si hay un congreso activo
- `getCurrentCongressId()`: Obtiene el ID del congreso actual

### 2. TenantMiddleware (`app/Http/Middleware/TenantMiddleware.php`)
Middleware que:
- Detecta el congreso actual usando `TenantService`
- Agrega el congreso al request para fácil acceso
- Aborta con 404 si una ruta requiere congreso y no se encuentra

**Registrado como alias `tenant`** en `bootstrap/app.php`

### 3. TenantScope (`app/Models/Scopes/TenantScope.php`)
GlobalScope que filtra automáticamente las consultas por `congress_id` cuando:
- Hay un congreso activo
- El modelo tiene la columna `congress_id`
- La tabla NO está en la lista de tablas globales (users, congresses, thematic_areas, etc.)

**Aplicado globalmente** en `AppServiceProvider`

### 4. Helpers (`app/Helpers/TenantHelper.php`)
Funciones helper para acceso rápido:
- `currentCongress()`: Obtiene el congreso actual
- `currentCongressId()`: Obtiene el ID del congreso actual
- `hasCurrentCongress()`: Verifica si hay un congreso activo

**Registrado en `composer.json`** para autoload automático

### 5. Roles y Permisos Actualizados
**Roles configurados:**
- `Super Admin`: Acceso total
- `Organizador`: Gestión de congresos y configuración
- `Admin`: Gestión completa de áreas temáticas y congresos
- `Revisor`: Revisión de papers
- `Autor`: Envío de papers
- `Asistente`: Acceso de solo lectura

**Permisos agregados:**
- Gestión de papers (view, create, edit, delete, review, assign)
- Gestión de inscripciones
- Configuración de congresos

## 🔧 Configuración Realizada

### 1. AppServiceProvider
- Registrado `TenantService` como singleton
- Aplicado `TenantScope` globalmente a todos los modelos

### 2. Bootstrap/App.php
- Registrado middleware `tenant` como alias

### 3. Modelo Congress
- Agregados campos multi-tenancy al `$fillable`
- Agregados casts para campos nuevos (boolean, array, decimal)

### 4. Composer.json
- Registrado archivo de helpers para autoload

## 📋 Rutas Multi-Tenancy

Las rutas actuales ya están estructuradas para multi-tenancy:

### Rutas Públicas (sin tenant)
```php
Route::get('/', ...); // Home general
Route::get('/congresos', ...); // Listado de congresos
Route::get('/congresos/{slug}', ...); // Detalle de congreso
```

### Rutas con Tenant (prefijo `/evento/{slug}` o `/congress/{congress}`)
```php
Route::prefix('evento/{slug}')->middleware('tenant')->group(function () {
    // Rutas del micrositio del congreso
});
```

## 🚀 Uso del Sistema Multi-Tenancy

### En Controladores
```php
use App\Services\TenantService;

public function index(TenantService $tenantService)
{
    $congress = $tenantService->getCurrentCongress();
    // O usar el helper
    $congress = currentCongress();
}
```

### En Vistas Blade
```blade
@if(hasCurrentCongress())
    <h1>{{ currentCongress()->title }}</h1>
@endif
```

### Desactivar GlobalScope cuando sea necesario
```php
// En consultas que necesitan ver todos los congresos
Congress::withoutGlobalScope(TenantScope::class)->get();
```

## ⚙️ Próximos Pasos (Fase 3)

1. **Call for Papers**: Formulario Livewire para envío de ponencias
2. **Asignación de Revisores**: Lógica para asignar papers
3. **Evaluación**: Sistema de Peer Review doble ciego
4. **Notificaciones**: Jobs de envío de correo encolados

## ⚠️ Notas Importantes

- El `TenantScope` se aplica automáticamente a modelos con `congress_id`
- Las tablas globales (users, congresses, thematic_areas) NO se filtran
- El middleware `tenant` debe aplicarse a rutas que requieren contexto de congreso
- Los helpers están disponibles globalmente después de `composer dump-autoload`

## 🔄 Comandos para Aplicar

```bash
# 1. Regenerar autoload para incluir helpers
composer dump-autoload

# 2. Ejecutar migraciones (si aún no se han ejecutado)
php artisan migrate

# 3. Ejecutar seeder de roles y permisos
php artisan db:seed --class=RolePermissionSeeder

# 4. Limpiar caché
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

