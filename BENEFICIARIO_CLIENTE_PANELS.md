# Panel de Beneficiarios y Clientes

Este documento describe la implementación del sistema de paneles personalizados para beneficiarios y clientes.

## Descripción General

El sistema permite que beneficiarios y clientes inicien sesión y visualicen paneles personalizados basados en su tipo de usuario.

## Usuarios

### Beneficiarios

**Credenciales de Acceso:**
- Usuario: El email del beneficiario (generado automáticamente si no se proporciona)
- Contraseña por defecto: `{nombre}123`
- Ejemplo: Si el nombre es "Juan", la contraseña será "Juan123"

**Panel de Beneficiario:**
El panel muestra:
- Porcentaje de comisión (inicialmente 0%)
- Ganancias totales (inicialmente $0.00)
- Ventas totales (inicialmente 0)
- Resumen financiero

### Clientes

**Credenciales de Acceso:**
- Usuario: El email del cliente
- Contraseña por defecto: `{nombre}123`
- Ejemplo: Si el nombre es "Maria", la contraseña será "Maria123"

**Panel de Cliente:**
El panel muestra:
- Plan activo (última transacción completada)
- Historial completo de transacciones
- Códigos QR de eSIM para cada transacción

## Base de Datos

### Migración

La migración `2026_01_26_193501_add_user_relationship_to_beneficiarios_and_clientes_tables.php` agrega:

**Tabla beneficiarios:**
- `user_id`: Relación con la tabla users
- `commission_percentage`: Porcentaje de comisión (decimal 5,2)
- `total_earnings`: Ganancias totales (decimal 10,2)
- `total_sales`: Total de ventas (integer)

**Tabla clientes:**
- `user_id`: Relación con la tabla users

**Tabla users:**
- `user_type`: Tipo de usuario ('admin', 'beneficiario', 'cliente')

### Ejecutar Migraciones

```bash
php artisan migrate
```

## Datos de Prueba

### Crear Datos de Prueba

Ejecutar el seeder para crear usuarios de prueba:

```bash
php artisan db:seed --class=BeneficiarioClienteSeeder
```

Esto creará:

**Beneficiario de Prueba:**
- Email: `beneficiario.test@example.com`
- Contraseña: `Juan123`
- Nombre: Juan Beneficiario

**Cliente de Prueba:**
- Email: `cliente.test@example.com`
- Contraseña: `Maria123`
- Nombre: Maria Cliente
- Incluye 3 transacciones de ejemplo

## Rutas

### Beneficiarios

- Dashboard: `/beneficiario/dashboard`
- API Data: `/beneficiario/dashboard/data`

### Clientes

- Dashboard: `/cliente/dashboard`
- API Data: `/cliente/dashboard/data`

## Flujo de Autenticación

1. Usuario inicia sesión en `/admin/users/login`
2. El sistema valida las credenciales
3. El hook `CustomRoute` detecta el tipo de usuario
4. Redirige al dashboard correspondiente:
   - Beneficiarios → `/beneficiario/dashboard`
   - Clientes → `/cliente/dashboard`
   - Admins → `/admin/dashboard`

## Creación Automática de Usuarios

Cuando se crea un beneficiario o cliente a través del CRUD:

1. Se guarda el registro en la tabla correspondiente
2. Automáticamente se crea un usuario asociado
3. La contraseña se genera como `{nombre}123`
4. El `user_type` se establece según el tipo
5. Se vincula el registro con el usuario mediante `user_id`

## Personalización

### Cambiar el Patrón de Contraseña

Editar los servicios:
- `app/Services/App/Beneficiario/BeneficiarioService.php`
- `app/Services/App/Cliente/ClienteService.php`

Buscar la línea:
```php
$password = $beneficiario->nombre . '123';
```

### Modificar los Dashboards

Las vistas se encuentran en:
- `resources/views/dashboard/beneficiario.blade.php`
- `resources/views/dashboard/cliente.blade.php`

Los controladores están en:
- `app/Http/Controllers/App/Beneficiario/BeneficiarioDashboardController.php`
- `app/Http/Controllers/App/Cliente/ClienteDashboardController.php`

## Seguridad

- Las contraseñas se almacenan hasheadas usando bcrypt
- Cada usuario debe cambiar su contraseña después del primer inicio de sesión (recomendado)
- Los dashboards verifican el tipo de usuario antes de mostrar información
- Solo los usuarios autenticados pueden acceder a los dashboards

## Soporte

Para cualquier problema o pregunta, contactar al equipo de desarrollo.
