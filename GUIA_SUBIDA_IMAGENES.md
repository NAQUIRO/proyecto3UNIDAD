# Guía para Subir Imágenes al Sistema

## Opción 1: Subir desde la Interfaz Web (Recomendado)

1. **Iniciar el servidor** (si no está corriendo):
   ```bash
   php artisan serve
   ```

2. **Acceder al sistema**:
   - Abre tu navegador en: `http://localhost:8000`
   - Inicia sesión como administrador

3. **Navegar a Congresos**:
   - Ve a: `Admin > Congresos > Crear` o `Editar`

4. **Subir imágenes**:
   - **Logo**: Arrastra y suelta o haz click en la zona de carga
   - **Banner**: Arrastra y suelta o haz click en la zona de carga
   - Verás la vista previa antes de guardar

## Opción 2: Importar Imágenes desde Carpeta Local

Si tienes las imágenes en una carpeta de tu computadora, puedes importarlas usando el comando:

```bash
# Importar un logo específico
php artisan images:import "C:\ruta\a\tu\imagen.jpg" --type=logo

# Importar un banner específico
php artisan images:import "C:\ruta\a\tu\banner.jpg" --type=banner

# Importar todas las imágenes de una carpeta
php artisan images:import "C:\ruta\a\tu\carpeta" --type=logo

# Importar para un congreso específico (si conoces el ID)
php artisan images:import "C:\ruta\a\tu\imagen.jpg" --type=logo --congress=1
```

**Ejemplo práctico**:
```bash
# Si tienes las imágenes en C:\Imagenes\Congresos\
php artisan images:import "C:\Imagenes\Congresos\logo.png" --type=logo
php artisan images:import "C:\Imagenes\Congresos\banner.jpg" --type=banner
```

## Opción 3: Copiar Manualmente

1. **Ubicación de destino**:
   - Logos: `storage/app/public/congresses/logos/`
   - Banners: `storage/app/public/congresses/banners/`

2. **Copiar archivos**:
   - Copia tus imágenes directamente a estas carpetas
   - Los nombres deben ser únicos (recomendado: usar nombres descriptivos)

3. **Asociar en la base de datos**:
   - Después de copiar, edita el congreso desde la interfaz web
   - O actualiza manualmente en la base de datos

## Formatos Soportados

- **Formatos**: JPG, JPEG, PNG, GIF, SVG
- **Tamaño máximo Logo**: 2MB
- **Tamaño máximo Banner**: 5MB

## Verificar que las Imágenes se Muestren

1. **Verificar el enlace simbólico**:
   ```bash
   php artisan storage:link
   ```

2. **Verificar permisos** (en Linux/Mac):
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

3. **Acceder a las imágenes**:
   - Las imágenes estarán disponibles en: `http://localhost:8000/storage/congresses/logos/nombre.jpg`
   - O usando: `{{ asset('storage/congresses/logos/nombre.jpg') }}` en las vistas

## Solución de Problemas

### Las imágenes no se muestran
- Verifica que el enlace simbólico esté creado: `php artisan storage:link`
- Verifica que los archivos estén en `storage/app/public/`
- Limpia la caché: `php artisan config:clear`

### Error al subir imágenes
- Verifica el tamaño del archivo (no exceder límites)
- Verifica el formato (debe ser imagen válida)
- Verifica permisos de escritura en `storage/app/public/`

### Las imágenes se suben pero no se guardan
- Verifica que el formulario tenga `enctype="multipart/form-data"`
- Verifica que el campo tenga `name="logo"` o `name="banner"`
- Revisa los logs en `storage/logs/laravel.log`




