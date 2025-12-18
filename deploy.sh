#!/bin/bash

# Script de Despliegue Automatizado para EventHub
# Uso: ./deploy.sh [produccion|staging]

ENVIRONMENT=${1:-produccion}
REMOTE_HOST="usuario@servidor.com"
REMOTE_PATH="/ruta/a/tu/proyecto"
LOCAL_PATH="."

echo "🚀 Iniciando despliegue a $ENVIRONMENT..."

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Función para mostrar errores
error_exit() {
    echo -e "${RED}❌ Error: $1${NC}" >&2
    exit 1
}

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error_exit "No se encontró el archivo artisan. ¿Estás en el directorio raíz del proyecto?"
fi

# 1. Compilar assets
echo -e "${YELLOW}📦 Compilando assets...${NC}"
if ! npm run build:prod; then
    error_exit "Error al compilar assets"
fi
echo -e "${GREEN}✅ Assets compilados${NC}"

# 2. Optimizar para producción
echo -e "${YELLOW}⚡ Optimizando aplicación...${NC}"
php artisan config:clear || error_exit "Error al limpiar config"
php artisan route:clear || error_exit "Error al limpiar rutas"
php artisan view:clear || error_exit "Error al limpiar vistas"
php artisan cache:clear || error_exit "Error al limpiar cache"

# 3. Optimizar Composer
echo -e "${YELLOW}📚 Optimizando autoloader...${NC}"
composer install --optimize-autoloader --no-dev || error_exit "Error al optimizar Composer"

# 4. Cachear configuración
echo -e "${YELLOW}💾 Cacheando configuración...${NC}"
php artisan config:cache || error_exit "Error al cachear config"
php artisan route:cache || error_exit "Error al cachear rutas"
php artisan view:cache || error_exit "Error al cachear vistas"

# 5. Crear lista de archivos a excluir
echo -e "${YELLOW}📋 Preparando archivos...${NC}"
EXCLUDE_FILE=$(mktemp)
cat > "$EXCLUDE_FILE" << EOF
node_modules/
.git/
.idea/
tests/
.phpunit.result.cache
.env.example
.env.backup
*.log
.DS_Store
Thumbs.db
EOF

# 6. Sincronizar archivos (usando rsync)
echo -e "${YELLOW}📤 Sincronizando archivos con el servidor...${NC}"
if command -v rsync &> /dev/null; then
    rsync -avz \
        --exclude-from="$EXCLUDE_FILE" \
        --exclude='vendor/' \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        "$LOCAL_PATH/" "$REMOTE_HOST:$REMOTE_PATH/" \
        || error_exit "Error al sincronizar archivos"
    echo -e "${GREEN}✅ Archivos sincronizados${NC}"
else
    echo -e "${YELLOW}⚠️  rsync no está instalado. Debes subir los archivos manualmente.${NC}"
fi

# 7. Ejecutar comandos en el servidor (si SSH está disponible)
echo -e "${YELLOW}🔧 Ejecutando comandos en el servidor...${NC}"
ssh "$REMOTE_HOST" << 'ENDSSH'
    cd /ruta/a/tu/proyecto
    
    # Instalar dependencias
    composer install --optimize-autoloader --no-dev
    
    # Crear symlink de storage
    php artisan storage:link
    
    # Ejecutar migraciones (solo si hay nuevas)
    # php artisan migrate --force
    
    # Cachear configuración
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Limpiar cache de aplicación
    php artisan cache:clear
    
    # Reiniciar workers de cola (si usas supervisor)
    # supervisorctl restart eventhub-worker:*
ENDSSH

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Comandos ejecutados en el servidor${NC}"
else
    echo -e "${YELLOW}⚠️  No se pudieron ejecutar comandos en el servidor. Ejecútalos manualmente.${NC}"
fi

# 8. Limpiar archivos temporales
rm -f "$EXCLUDE_FILE"

echo -e "${GREEN}✅ Despliegue completado exitosamente!${NC}"
echo -e "${YELLOW}📝 Recuerda verificar:${NC}"
echo "   - Que la aplicación carga correctamente"
echo "   - Que los assets se cargan"
echo "   - Que la base de datos funciona"
echo "   - Que las colas están procesando"

