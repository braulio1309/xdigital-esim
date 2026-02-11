# Sistema de Permisos para eSIM Gratuita - Documentación

## Resumen

Este documento describe la implementación del sistema de permisos para eSIM gratuita, que permite controlar qué clientes pueden activar su eSIM gratuita a través del portal de registro público.

## Cambios en la Base de Datos

### Nueva Columna: `can_activate_free_esim`

**Tabla**: `clientes`
**Tipo**: `boolean`
**Default**: `false`
**Ubicación**: Después de `beneficiario_id`

**Migración**: `2026_02_11_111500_add_can_activate_free_esim_to_clientes_table.php`

```php
Schema::table('clientes', function (Blueprint $table) {
    $table->boolean('can_activate_free_esim')->default(false)->after('beneficiario_id');
});
```

## Flujo de Registro Manual de Clientes (Admin/Beneficiario)

### Comportamiento Anterior
- Al crear un cliente desde el panel admin, automáticamente se activaba su eSIM
- Se creaba una orden y transacción
- El cliente recibía su eSIM inmediatamente

### Comportamiento Nuevo
- Al crear un cliente desde el panel admin, **NO** se activa su eSIM automáticamente
- Solo se crea el registro del cliente
- Se puede configurar:
  - Beneficiario asociado
  - Flag `can_activate_free_esim` (checkbox)
- El cliente debe ir a `/registro/esim` para activar su eSIM gratuita (si tiene el flag activo)

**Archivo**: `app/Http/Controllers/App/Cliente/ClienteController.php`

```php
public function store(Request $request)
{
    // Simply save the client without automatic eSIM activation
    // The flag can_activate_free_esim controls whether they can activate it later
    $cliente = $this->service->save();
    return created_responses('cliente');
}
```

## Flujo de Activación de eSIM Gratuita

### Acceso a `/registro/esim`

Cuando un usuario autenticado (tipo `cliente`) intenta acceder a `/registro/esim`:

1. **Si NO tiene el flag activo**:
   - Redirige a `/planes-disponibles`
   - Muestra mensaje: "No tienes permiso para activar una eSIM gratuita. Por favor, contacta al administrador."

2. **Si tiene el flag activo**:
   - Permite completar el proceso de activación
   - Al activar exitosamente la eSIM, desactiva automáticamente el flag
   - El cliente solo puede usar su eSIM gratuita una vez

**Archivo**: `app/Http/Controllers/App/Cliente/RegistroEsimController.php`

```php
public function mostrarFormulario(HttpRequest $request, $referralCode = null)
{
    // Check if user is authenticated and is a cliente
    if (auth()->check() && auth()->user()->user_type === 'cliente') {
        $cliente = Cliente::where('user_id', auth()->id())->first();
        
        // If cliente doesn't have permission to activate free eSIM
        if ($cliente && !$cliente->can_activate_free_esim) {
            return redirect()->route('planes.index')
                ->with('error', 'No tienes permiso para activar una eSIM gratuita...');
        }
    }
    // ...
}

public function registrarCliente(HttpRequest $request, ...)
{
    // ... after successful eSIM activation ...
    
    // If this client has the can_activate_free_esim flag, deactivate it
    if ($cliente->can_activate_free_esim) {
        $cliente->can_activate_free_esim = false;
        $cliente->save();
    }
}
```

## Asociación con Beneficiarios

### Filtrado por Beneficiario

Los beneficiarios autenticados solo pueden ver sus propios clientes en el listado.

**Archivo**: `app/Http/Controllers/App/Cliente/ClienteController.php`

```php
public function index()
{
    $query = $this->service->filters($this->filter)->latest();
    
    // Filter by beneficiario_id if user is a beneficiario
    if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
        $beneficiario = Beneficiario::where('user_id', auth()->id())->first();
        
        if ($beneficiario) {
            $query = $query->where('beneficiario_id', $beneficiario->id);
        }
    }
    
    return $query->with('beneficiario:id,nombre')->paginate(request()->get('per_page', 10));
}
```

### Relación en el Modelo

El campo `beneficiario_id` ya existía en la tabla `clientes` (agregado en migración anterior).

**Archivo**: `app/Models/App/Cliente/Cliente.php`

```php
protected $fillable = [
    'nombre', 
    'apellido', 
    'email', 
    'user_id', 
    'beneficiario_id',  // Ya existía
    'can_activate_free_esim'  // Nuevo
];

public function beneficiario()
{
    return $this->belongsTo(Beneficiario::class);
}
```

## Interfaz de Usuario (Vue)

### Listado de Clientes (`Index.vue`)

**Nuevas Columnas**:

1. **Beneficiario**: Muestra el nombre del beneficiario asociado (o "N/A" si no tiene)
2. **eSIM Gratuita**: Badge que muestra si el cliente puede activar su eSIM gratuita
   - Verde "Permitido" si `can_activate_free_esim = true`
   - Gris "No permitido" si `can_activate_free_esim = false`

**Nuevo Botón de Acción**: "Toggle eSIM"
- Permite activar/desactivar el permiso de eSIM gratuita
- Muestra confirmación antes de cambiar el estado
- Actualiza la tabla automáticamente después del cambio

```javascript
toggleFreeEsim(rowData) {
    const url = `/app/clientes/${rowData.id}/toggle-free-esim`;
    const newStatus = !rowData.can_activate_free_esim;
    const action = newStatus ? 'activar' : 'desactivar';
    
    if (confirm(`¿Está seguro que desea ${action} el permiso de eSIM gratuita...?`)) {
        this.axiosPost(url)
            .then(response => {
                this.$toastr.s(response.data.message);
                this.$hub.$emit('reload-' + this.tableId);
            });
    }
}
```

### Modal de Creación/Edición (`AddModal.vue`)

**Nuevos Campos**:

1. **Beneficiario**: Selector dropdown con lista de beneficiarios disponibles
   - Campo opcional
   - Se carga dinámicamente desde `/app/beneficiarios`

2. **Permitir eSIM Gratuita**: Checkbox
   - Permite habilitar/deshabilitar el flag al crear o editar
   - Label: "Puede activar su eSIM gratuita"

```javascript
data() {
    return {
        inputs: {
            nombre: '',
            apellido: '',
            email: '',
            beneficiario_id: null,  // Nuevo
            can_activate_free_esim: false,  // Nuevo
        },
        beneficiarios: [],  // Nuevo
        // ...
    }
}
```

## Endpoints API

### Toggle Flag de eSIM Gratuita

**Ruta**: `POST /app/clientes/{cliente}/toggle-free-esim`

**Respuesta**:
```json
{
    "status": true,
    "message": "Permiso de eSIM gratuita activado exitosamente.",
    "data": { /* cliente object */ }
}
```

**Archivo**: `routes/app/cliente.php`

```php
Route::post('clientes/{cliente}/toggle-free-esim', [ClienteController::class, 'toggleFreeEsim'])
    ->name('clientes.toggle-free-esim');
```

## Casos de Uso

### Caso 1: Admin registra cliente con permiso de eSIM gratuita

1. Admin accede a `/admin/clientes`
2. Click en botón "Agregar"
3. Completa formulario:
   - Nombre: Juan
   - Apellido: Pérez
   - Email: juan@example.com
   - Beneficiario: (Selecciona uno de la lista)
   - ✅ Permitir eSIM Gratuita
4. Cliente creado con `can_activate_free_esim = true`
5. Cliente NO recibe eSIM automáticamente
6. Cliente debe ir a `/registro/esim` con su cuenta para activarla

### Caso 2: Cliente intenta activar eSIM sin permiso

1. Cliente inicia sesión en el sistema
2. Intenta acceder a `/registro/esim`
3. Sistema verifica: `can_activate_free_esim = false`
4. Redirige a `/planes-disponibles`
5. Muestra mensaje: "No tienes permiso para activar una eSIM gratuita..."

### Caso 3: Cliente activa su eSIM gratuita

1. Cliente con `can_activate_free_esim = true` accede a `/registro/esim`
2. Completa el formulario de activación
3. eSIM se activa exitosamente
4. Sistema automáticamente cambia `can_activate_free_esim = false`
5. Cliente ya no puede volver a activar eSIM gratuita

### Caso 4: Beneficiario administra sus clientes

1. Beneficiario inicia sesión
2. Accede a `/admin/clientes`
3. Solo ve clientes donde `beneficiario_id = [su id]`
4. Puede usar botón "Toggle eSIM" para dar/quitar permiso
5. Al hacer toggle, confirma la acción
6. Cliente recibe/pierde permiso inmediatamente

## Mensajes de Error y Feedback

### Vista de Planes Disponibles

Muestra alertas de sesión flash:
- Error: Badge rojo con ícono
- Success: Badge verde con ícono
- Dismissible (botón X para cerrar)

### Modal de Cliente (AddModal)

- Error al cargar beneficiarios: Toast de error
- Éxito al guardar: Toast de éxito
- Error de validación: Mostrado por FormMixin

### Toggle de Flag

- Confirmación antes de cambiar
- Toast de éxito después del cambio
- Toast de error si falla la operación
- Recarga automática de la tabla

## Consideraciones de Seguridad

1. **Autorización**: Solo usuarios autenticados y autorizados pueden crear/editar clientes
2. **Validación**: Los flags booleanos se validan en el backend
3. **Filtrado**: Los beneficiarios solo ven sus propios clientes
4. **Log**: Todas las activaciones de eSIM se registran en la tabla `transactions`
5. **Flag único**: Una vez usada la eSIM gratuita, el flag se desactiva automáticamente

## Testing Recomendado

### Pruebas Manuales

1. ✅ Crear cliente sin flag → Verificar que no pueda acceder a registro/esim
2. ✅ Crear cliente con flag → Verificar que pueda activar eSIM
3. ✅ Activar eSIM gratuita → Verificar que flag se desactive
4. ✅ Toggle flag desde listado → Verificar cambio inmediato
5. ✅ Login como beneficiario → Verificar que solo ve sus clientes
6. ✅ Beneficiario crea cliente → Verificar asociación automática

### Pruebas de Base de Datos

```bash
php artisan migrate
# Verifica que la columna can_activate_free_esim exista en tabla clientes
```

### Pruebas de Endpoints

```bash
# Toggle flag
curl -X POST http://localhost/app/clientes/1/toggle-free-esim \
  -H "Authorization: Bearer {token}"
```

## Archivos Modificados

1. `database/migrations/2026_02_11_111500_add_can_activate_free_esim_to_clientes_table.php` (NUEVO)
2. `app/Models/App/Cliente/Cliente.php`
3. `app/Http/Controllers/App/Cliente/ClienteController.php`
4. `app/Http/Controllers/App/Cliente/RegistroEsimController.php`
5. `routes/app/cliente.php`
6. `resources/js/app/Components/Views/App/Clientes/Index.vue`
7. `resources/js/app/Components/Views/App/Clientes/AddModal.vue`
8. `resources/views/clientes/planes-disponibles.blade.php`

## Notas Adicionales

- El sistema es compatible con el flujo existente de registro público
- No afecta a los clientes que se registran por sí mismos en `/registro/esim`
- Los clientes creados manualmente ahora tienen control más granular
- Los beneficiarios tienen mejor gestión de sus clientes asociados
