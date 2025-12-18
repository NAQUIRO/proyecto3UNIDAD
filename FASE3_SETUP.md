# Fase 3: Gestión Científica (Core) - COMPLETADA

## ✅ Componentes Implementados

### 1. Call for Papers - Componente Livewire (`app/Livewire/Paper/SubmitPaperForm.php`)

**Características:**
- Formulario completo para envío de ponencias
- Validación en tiempo real de conteo de palabras
- Subida de múltiples archivos (resumen, paper completo, presentación)
- Validación de tipos de archivo (PDF, DOC, DOCX, PPT, PPTX)
- Validación de tamaño de archivos (10MB para resumen, 50MB para papers/presentaciones)
- Integración con áreas temáticas y editoriales
- Soporte para edición de papers existentes

**Validaciones implementadas:**
- Título: Requerido, máximo 255 caracteres
- Resumen: Mínimo 100 palabras, máximo configurable (por defecto 500)
- Palabras clave: Opcional, máximo 500 caracteres
- Archivos: Validación de tipo y tamaño

### 2. Reglas de Validación Personalizadas

#### `WordCountRule` (`app/Rules/WordCountRule.php`)
- Valida el conteo de palabras en un texto
- Configurable con mínimo y máximo de palabras
- Mensajes de error personalizados

#### `AllowedFileTypeRule` (`app/Rules/AllowedFileTypeRule.php`)
- Valida tipos de archivo permitidos
- Valida tamaño máximo de archivo
- Soporta múltiples extensiones

### 3. Servicio de Asignación de Revisores (`app/Services/ReviewerAssignmentService.php`)

**Funcionalidades:**
- **Asignación Automática**: Basada en área temática del paper
- **Asignación Manual**: Selección manual de revisores
- **Sugerencias Inteligentes**: Sugiere revisores basado en:
  - Área temática del paper
  - Carga de trabajo actual del revisor
  - Disponibilidad (sin asignaciones activas)

**Métodos principales:**
- `assignByThematicArea()`: Asigna revisores automáticamente
- `assignManually()`: Asigna revisores manualmente
- `suggestReviewers()`: Sugiere revisores disponibles

### 4. Sistema de Peer Review Doble Ciego (`app/Livewire/Review/ReviewForm.php`)

**Características:**
- Formulario de revisión completo
- Sistema de rúbricas con 5 criterios predeterminados:
  - Originalidad (0-10)
  - Metodología (0-10)
  - Resultados (0-10)
  - Escritura (0-10)
  - Relevancia (0-10)
- Cálculo automático de puntuación general
- Comentarios generales (visibles para el autor)
- Comentarios confidenciales (solo para el comité)
- Recomendación: Aceptar, Rechazar, Requerir Revisión

**Flujo de Revisión:**
1. Paper se envía → Estado: `submitted`
2. Admin asigna revisores → Estado: `under_review`
3. Revisor acepta asignación → Estado: `in_progress`
4. Revisor completa revisión → Estado: `completed`
5. Sistema calcula puntuación y genera recomendación

### 5. Jobs de Notificaciones

#### `SendPaperNotificationJob` (`app/Jobs/SendPaperNotificationJob.php`)
- Notifica a revisores cuando se les asigna un paper
- Envía email con detalles de la asignación

#### `SendPaperSubmittedNotificationJob` (`app/Jobs/SendPaperSubmittedNotificationJob.php`)
- Notifica al autor cuando su paper es enviado
- Notifica a administradores del congreso

#### `SendReviewCompletedNotificationJob` (`app/Jobs/SendReviewCompletedNotificationJob.php`)
- Notifica al autor cuando se completa una revisión
- Notifica a administradores del congreso

**Todos los jobs:**
- Implementan `ShouldQueue` para procesamiento asíncrono
- Usan driver `database` para colas
- Incluyen manejo de errores y logging

### 6. Controlador de Asignación de Revisores Mejorado

**Nuevas rutas agregadas:**
- `POST /congress/{congress}/papers/{paper}/review-assignments/auto` - Asignación automática
- `GET /congress/{congress}/papers/{paper}/review-assignments/suggest` - Obtener sugerencias

**Funcionalidades:**
- Asignación manual de revisores
- Asignación automática por área temática
- API para obtener sugerencias de revisores
- Aceptar/rechazar asignaciones (para revisores)

## 📋 Vistas Livewire Creadas

### 1. `resources/views/livewire/paper/submit-paper-form.blade.php`
- Formulario completo con validación en tiempo real
- Contador de palabras dinámico
- Preview de archivos seleccionados
- Diseño responsive con Tailwind CSS

### 2. `resources/views/livewire/review/review-form.blade.php`
- Formulario de revisión doble ciego
- Sistema de rúbricas interactivo
- Validación de puntuaciones
- Comentarios generales y confidenciales

## 🔧 Configuración Realizada

### 1. Storage Link
- Symlink creado: `public/storage` → `storage/app/public`
- Archivos se guardan en: `storage/app/public/papers/{paper_id}/`

### 2. Rutas Actualizadas
- Agregadas rutas para asignación automática y sugerencias
- Integración con el sistema multi-tenancy

### 3. Integración con Multi-Tenancy
- Los papers se filtran automáticamente por `congress_id`
- Las asignaciones de revisores respetan el contexto del congreso

## 📁 Estructura de Archivos Creados

```
app/
├── Livewire/
│   ├── Paper/
│   │   └── SubmitPaperForm.php
│   └── Review/
│       └── ReviewForm.php
├── Services/
│   └── ReviewerAssignmentService.php
├── Jobs/
│   ├── SendPaperNotificationJob.php
│   ├── SendPaperSubmittedNotificationJob.php
│   └── SendReviewCompletedNotificationJob.php
└── Rules/
    ├── WordCountRule.php
    └── AllowedFileTypeRule.php

resources/views/livewire/
├── paper/
│   └── submit-paper-form.blade.php
└── review/
    └── review-form.blade.php
```

## 🚀 Uso del Sistema

### Envío de Papers
```php
// En una vista Blade
<livewire:paper.submit-paper-form :congress="$congress" :paper="$paper" />
```

### Asignación de Revisores
```php
// Asignación automática
$service = app(ReviewerAssignmentService::class);
$assignments = $service->assignByThematicArea($paper, 2);

// Asignación manual
$assignments = $service->assignManually($paper, [1, 2, 3], $deadline, $notes);

// Obtener sugerencias
$suggestions = $service->suggestReviewers($paper, 5);
```

### Revisión de Papers
```php
// En una vista Blade
<livewire:review.review-form :review="$review" />
```

## ⚙️ Procesamiento de Colas

Para procesar los jobs de notificaciones en local:

```bash
# Procesar colas
php artisan queue:work --tries=3

# O en modo listen (recomendado para desarrollo)
php artisan queue:listen --tries=3
```

**Configuración en .env:**
```env
QUEUE_CONNECTION=database
```

## 📝 Validaciones Implementadas

### Papers
- Título: Requerido, máximo 255 caracteres
- Resumen: Mínimo 100 palabras, máximo configurable
- Palabras clave: Opcional, máximo 500 caracteres
- Archivos:
  - Resumen: PDF, DOC, DOCX - Máx. 10MB
  - Paper completo: PDF, DOC, DOCX - Máx. 50MB
  - Presentación: PDF, PPT, PPTX - Máx. 50MB

### Revisiones
- Recomendación: Requerida (accept, reject, revision_required)
- Comentarios: Mínimo 50 caracteres
- Rúbricas: Puntuación entre 0 y max_score

## ⚠️ Notas Importantes

- Los archivos se almacenan en `storage/app/public/papers/{paper_id}/`
- El symlink `public/storage` debe existir (ya creado)
- Los jobs se procesan usando driver `database`
- El sistema de revisión es doble ciego (el revisor no ve información del autor)
- Las asignaciones automáticas seleccionan revisores aleatoriamente de los disponibles

## 🔄 Próximos Pasos (Fase 4)

1. **Pagos**: Integración modular (Stripe/PayPal/Pasarela Local)
2. **Facturación**: Generación de recibos PDF
3. **Mailing**: Sistema de envío de correos masivos con chunks
4. **Configuración SMTP**: En .env

## 🧪 Testing

Para probar el sistema:

1. **Crear un paper:**
   - Acceder a `/congress/{slug}/papers/create`
   - Completar el formulario
   - Subir archivos
   - Guardar como borrador

2. **Enviar paper:**
   - Desde la vista del paper, hacer clic en "Enviar"
   - El job de notificación se encolará

3. **Asignar revisores:**
   - Como admin, ir a `/congress/{slug}/review-assignments`
   - Asignar manualmente o automáticamente

4. **Revisar paper:**
   - Como revisor, acceder a la revisión asignada
   - Completar las rúbricas y comentarios
   - Enviar recomendación

