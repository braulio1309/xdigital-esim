# Sistema de Permisos para eSIM Gratuita - Documentación

## Resumen

Este documento describe la implementación del sistema de permisos para eSIM gratuita, que permite controlar qué clientes pueden activar su eSIM gratuita a través del portal de registro público **sin necesidad de autenticación**.

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

## Flujo de Activación de eSIM Gratuita (Sin Login)

### Acceso Público a `/registro/esim`

**NO se requiere autenticación**. El formulario es completamente público.

### Proceso de Validación en Form Submit

Cuando un usuario envía el formulario en `/registro/esim`:

1. **Validación de datos del formulario** (nombre, apellido, email, país)

2. **Verificación de email**:
   - Sistema busca si el email ya existe en la tabla `clientes`
   
3. **Si el email YA EXISTE**:
   - **Flag `can_activate_free_esim = false`**:
     - ❌ Redirige a `/planes-disponibles`
     - Muestra mensaje: "No tienes permiso para activar una eSIM gratuita. Por favor, contacta al administrador."
   
   - **Flag `can_activate_free_esim = true`**:
     - ✅ Usa el cliente existente
     - Continúa con el proceso de activación de eSIM
     - Después de activar exitosamente, desactiva el flag automáticamente
     - El cliente solo puede usar su eSIM gratuita **una vez**

4. **Si el email es NUEVO**:
   - ✅ Registra nuevo cliente normalmente
   - Asocia al beneficiario si hay referralCode
   - Activa eSIM inmediatamente (comportamiento original para registros públicos)

**Archivo**: `app/Http/Controllers/App/Cliente/RegistroEsimController.php`

```php
public function mostrarFormulario(HttpRequest $request, $referralCode = null)
{
    // Public form - no authentication required
    // Email validation happens on form submission
    
    return view('clientes.registro-esim', [
        'beneficiario' => $beneficiario,
        'referralCode' => $referralCode,
        'parametro' => $request->query('parametro', ''),
        'affordableCountries' => $affordableCountries
    ]);
}

public function registrarCliente(HttpRequest $request, ...)
{
    // Validate form (email without unique constraint)
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|email', // No unique check here
        'country_code' => 'required|string|max:2',
        'referralCode' => 'nullable|string'
    ]);

    // Check if email already exists
    $existingCliente = Cliente::where('email', $validated['email'])->first();
    
    if ($existingCliente) {
        // Email exists - check flag
        if (!$existingCliente->can_activate_free_esim) {
            return redirect()->route('planes.index')
                ->with('error', 'No tienes permiso...');
        }
        
        // Has permission - use existing cliente
        $cliente = $existingCliente;
    } else {
        // New email - register normally
        $cliente = $service->save();
    }
    
    // ... activate eSIM ...
    
    // Deactivate flag after successful activation
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
6. Se le debe informar al cliente que vaya a `/registro/esim` y use su email

### Caso 2: Cliente existente con flag=false intenta activar eSIM

1. Cliente con `can_activate_free_esim = false` accede a `/registro/esim`
2. Completa el formulario con su email (juan@example.com)
3. Sistema verifica: email existe y `can_activate_free_esim = false`
4. Redirige a `/planes-disponibles`
5. Muestra mensaje: "No tienes permiso para activar una eSIM gratuita..."

### Caso 3: Cliente existente con flag=true activa su eSIM gratuita

1. Cliente con `can_activate_free_esim = true` accede a `/registro/esim`
2. Completa formulario con su email (juan@example.com) y selecciona país
3. Sistema verifica: email existe y `can_activate_free_esim = true`
4. eSIM se activa exitosamente
5. Sistema automáticamente cambia `can_activate_free_esim = false`
6. Cliente ya no puede volver a activar eSIM gratuita

### Caso 4: Usuario nuevo registra eSIM por primera vez

1. Usuario nuevo accede a `/registro/esim` (con o sin referralCode)
2. Completa formulario con email nuevo (maria@example.com)
3. Sistema verifica: email NO existe
4. Registra nuevo cliente y activa eSIM inmediatamente
5. Comportamiento normal de registro público (sin flag)

### Caso 5: Beneficiario administra sus clientes

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

1. ✅ **Nuevo usuario** en registro/esim → Verificar registro normal y activación
2. ✅ **Cliente existente sin flag** en registro/esim → Verificar redirección a planes
3. ✅ **Cliente existente con flag** en registro/esim → Verificar activación y desactivación de flag
4. ✅ **Toggle flag** desde listado admin → Verificar cambio inmediato
5. ✅ **Login como beneficiario** → Verificar que solo ve sus clientes
6. ✅ **Beneficiario crea cliente** → Verificar asociación automática

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

# Registro público con email existente (sin flag)
curl -X POST http://localhost/registro/esim \
  -d "nombre=Juan&apellido=Perez&email=existing@example.com&country_code=US"
# Resultado esperado: Redirección a planes-disponibles

# Registro público con email existente (con flag)
curl -X POST http://localhost/registro/esim \
  -d "nombre=Juan&apellido=Perez&email=withflag@example.com&country_code=US"
# Resultado esperado: Activación exitosa + flag desactivado

# Registro público con email nuevo
curl -X POST http://localhost/registro/esim \
  -d "nombre=Maria&apellido=Garcia&email=new@example.com&country_code=ES"
# Resultado esperado: Registro y activación normal
```

### Escenarios de Prueba Detallados

#### Escenario 1: Email nuevo (registro público normal)
```
Entrada: Email que NO existe en BD
Acción: Enviar formulario /registro/esim
Resultado: ✅ Nuevo cliente creado + eSIM activada
Verificar: Cliente en BD sin flag activado
```

#### Escenario 2: Email existente con flag=true (activación permitida)
```
Entrada: Email que existe con can_activate_free_esim=true
Acción: Enviar formulario /registro/esim
Resultado: ✅ eSIM activada + flag cambiado a false
Verificar: Flag ahora es false en BD
```

#### Escenario 3: Email existente con flag=false (sin permiso)
```
Entrada: Email que existe con can_activate_free_esim=false
Acción: Enviar formulario /registro/esim
Resultado: ❌ Redirección a /planes-disponibles + mensaje error
Verificar: Cliente no recibió eSIM
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

- ✅ **Formulario público**: NO requiere autenticación, completamente accesible
- ✅ **Validación en backend**: El email se valida al enviar el formulario, no al mostrar
- ✅ **Compatible con registro público**: Los nuevos usuarios siguen registrándose normalmente
- ✅ **Control granular**: Los clientes creados manualmente pueden tener o no el permiso
- ✅ **Uso único**: El flag se desactiva automáticamente después de usar la eSIM gratuita
- ✅ **Sin pasos adicionales**: El flujo es directo, sin requerir login ni autenticación
- ✅ **Gestión por beneficiarios**: Cada beneficiario administra sus propios clientes
