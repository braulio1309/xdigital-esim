# Sistema de Planes Disponibles - Documentación

## Descripción General

Este sistema permite a los usuarios ver y comprar planes eSIM filtrados por país (España y USA) con integración de Stripe para pagos y autenticación dinámica sin recargas de página.

## Configuración Inicial

### 1. Variables de Entorno

Agregar las siguientes variables en tu archivo `.env`:

```env
# Stripe Configuration
STRIPE_PUBLISHABLE_KEY=pk_test_your_key_here
STRIPE_SECRET_KEY=sk_test_your_key_here

# eSIMfx API Configuration
ESIMFX_BASE_URL=https://api.esimfx.com
ESIMFX_CLIENT_ID=7f4b881c-85fb-44b2-850c-10b2479a82b5
ESIMFX_CLIENT_KEY=b81889d2-8400-41eb-8783-bdf118a1810b
```

### 2. Claves de Prueba de Stripe

Para pruebas, usa estas claves de Stripe:
- **Publishable Key**: `pk_test_51...` (obtén en https://dashboard.stripe.com/test/apikeys)
- **Secret Key**: `sk_test_51...` (obtén en https://dashboard.stripe.com/test/apikeys)

### 3. Tarjetas de Prueba de Stripe

Para probar pagos, usa estas tarjetas de prueba:
- **Éxito**: `4242 4242 4242 4242`
- **Requiere autenticación**: `4000 0025 0000 3155`
- **Tarjeta declinada**: `4000 0000 0000 9995`
- **CVC**: Cualquier 3 dígitos
- **Fecha**: Cualquier fecha futura
- **ZIP**: Cualquier código postal

## Estructura de Archivos

### Backend

1. **Controller**: `app/Http/Controllers/App/PlanesDisponiblesController.php`
   - `index()`: Renderiza la vista principal
   - `getPlanes()`: Obtiene planes filtrados por país (AJAX)
   - `verificarAuth()`: Verifica si el usuario está autenticado
   - `autenticar()`: Maneja login y registro
   - `procesarPago()`: Procesa pagos con Stripe y crea órdenes eSIM

2. **Service**: `app/Services/EsimFxService.php`
   - `getProductsByCountry($countryCode)`: Obtiene productos de la API eSIMfx

3. **Rutas**: `routes/web.php`
   ```php
   Route::get('/planes-disponibles', ...)->name('planes.index');
   Route::post('/planes/get-by-country', ...)->name('planes.get');
   Route::post('/planes/checkout', ...)->name('planes.checkout');
   Route::post('/planes/auth', ...)->name('planes.auth');
   Route::get('/planes/verificar-auth', ...)->name('planes.verificar-auth');
   ```

### Frontend

**Vista**: `resources/views/planes-disponibles.blade.php`
- Layout auth con logos de Xcertus & Nomad
- Selector de país
- Grid de tarjetas de planes
- Modal de autenticación (login/registro)
- Modal de checkout con Stripe
- Modal de resultado con QR code

## Flujo de Usuario

### 1. Selección de País
```
Usuario accede a /planes-disponibles
    ↓
Selecciona país (España o USA)
    ↓
Sistema carga planes dinámicamente vía AJAX
    ↓
Muestra tarjetas de planes (4 por fila)
```

### 2. Compra de Plan
```
Usuario hace clic en "Comprar"
    ↓
Sistema verifica autenticación
    ↓
Si NO autenticado: Muestra modal login/registro
Si autenticado: Continúa
    ↓
Muestra modal de checkout
    ↓
Si plan GRATIS: Botón "Obtener Plan Gratuito"
Si plan PAGO: Formulario Stripe con campos de tarjeta
    ↓
Usuario completa pago
    ↓
Sistema:
  1. Procesa pago en Stripe (si aplica)
  2. Crea orden en API eSIMfx
  3. Guarda transacción en BD
    ↓
Muestra QR code y datos de activación
```

## Características Principales

### 1. Filtrado por País
- Selector dropdown con España (ES) y USA (US)
- Carga dinámica de planes sin recargar página
- Manejo de estados vacíos

### 2. Tarjetas de Planes
Cada tarjeta muestra:
- **Duración**: `{duration} {duration_unit}s` (ej: "30 DAYs")
- **Datos**: `{amount} {amount_unit}` (ej: "10 GB")
- **Precio**: 
  - Si `price === 0`: "FREE" en verde
  - Si `price > 0`: "$5 USD" en azul
- **Botón**: "Comprar"

### 3. Autenticación Dinámica
Modal con 2 pestañas:
- **Iniciar Sesión**: Email + Password
- **Registrarse**: Nombre, Apellido, Email, Teléfono, Password

Características:
- Sin recargas de página
- Validación en tiempo real
- Creación automática de usuario y cliente
- Login automático después de registro

### 4. Integración Stripe
- Stripe Elements para entrada segura de tarjetas
- Soporte para 3D Secure (SCA)
- Manejo de planes gratuitos (sin Stripe)
- Validación de errores en tiempo real

### 5. Generación de eSIM
Después del pago exitoso:
- Genera QR code con SimpleSoftwareIO
- Muestra datos para instalación manual:
  - Dirección SM-DP+
  - Código de activación
- Botones de copiar al portapapeles
- Guarda transacción en base de datos

## Estructura de Base de Datos

### Tabla: `transactions`
```sql
- id (bigint, PK)
- transaction_id (string, unique)
- order_id (string, nullable)
- status (string, nullable)
- iccid (string, nullable)
- esim_qr (text, nullable)
- creation_time (timestamp, nullable)
- cliente_id (bigint, FK)
- created_at, updated_at
```

### Tabla: `clientes`
```sql
- id (bigint, PK)
- nombre (string)
- apellido (string)
- email (string)
- user_id (bigint, FK, nullable)
- beneficiario_id (bigint, FK, nullable)
- created_at, updated_at
```

## API eSIMfx

### Endpoint: Get Products
```
POST https://api.esimfx.com/product/api/v1/get_products
Authorization: Bearer {token}

Body:
{
  "coountries": "ES"  // Nota: typo intencional según API
}

Response:
{
  "data": {
    "products": [
      {
        "id": "uuid",
        "name": "United States_10GB_30DAYs",
        "duration": 30,
        "duration_unit": "DAY",
        "amount": 10,
        "amount_unit": "GB",
        "coverage": ["US"],
        "price": 5,
        "price_unit": "USD"
      }
    ]
  }
}
```

### Endpoint: Create Order
```
POST https://api.esimfx.com/order/api/v1/create_order
Authorization: Bearer {token}

Body:
{
  "product": {
    "id": "uuid"
  },
  "transaction_id": "WEB-123-1234567890",
  "count": 1,
  "operation_type": "NEW"
}

Response:
{
  "data": {
    "id": "order_id",
    "status": "completed",
    "esim": {
      "iccid": "89...",
      "esim_qr": "LPA:1$smdp.address$activation_code"
    }
  }
}
```

## Estilos y Diseño

### Colores de Marca
```css
--xcertus-purple: #623b86;
--xcertus-yellow: #ffcc00;
--nomad-blue: #2d9cdb;
--nomad-navy: #181c36;
```

### Responsive
- Desktop: 4 tarjetas por fila (col-md-3)
- Tablet: 2 tarjetas por fila (col-sm-6)
- Mobile: 1 tarjeta por fila (col-12)

### Animaciones
- Hover en tarjetas: Elevación y border azul
- Loading overlay con spinner
- Transiciones suaves en todos los elementos

## Seguridad

1. **CSRF Protection**: Tokens en todos los formularios
2. **Validación Backend**: Todos los inputs validados en servidor
3. **Stripe Secret Key**: Solo en backend, nunca expuesta
4. **Autenticación**: Verificada antes de procesar pagos
5. **Sanitización**: Datos escapados en frontend

## Testing Manual

### Checklist de Pruebas
- [ ] Acceso a `/planes-disponibles`
- [ ] Selector de país funciona
- [ ] Planes se cargan para España
- [ ] Planes se cargan para USA
- [ ] Tarjetas muestran datos correctamente
- [ ] Botón "Comprar" requiere autenticación
- [ ] Modal de login funciona
- [ ] Modal de registro funciona
- [ ] Checkout muestra plan seleccionado
- [ ] Stripe Elements se renderiza
- [ ] Tarjeta de prueba funciona
- [ ] Plan gratuito no requiere pago
- [ ] QR code se genera
- [ ] Datos manuales son copiables
- [ ] Transacción se guarda en BD

## Troubleshooting

### Error: "Stripe key not found"
**Solución**: Verifica que `STRIPE_PUBLISHABLE_KEY` esté en `.env` y que `php artisan config:clear` se haya ejecutado.

### Error: "API eSIMfx failed"
**Solución**: Verifica las credenciales `ESIMFX_CLIENT_ID` y `ESIMFX_CLIENT_KEY` en `.env`.

### Error: "Payment failed"
**Solución**: 
- Verifica que uses tarjetas de prueba de Stripe
- Revisa logs en Stripe Dashboard
- Confirma que `STRIPE_SECRET_KEY` sea correcta

### Planes no se cargan
**Solución**:
- Abre la consola del navegador
- Revisa errores de AJAX
- Verifica que la ruta `/planes/get-by-country` responda
- Confirma que la API eSIMfx esté disponible

## Logs y Debugging

Los errores se registran en:
- `storage/logs/laravel.log`
- Busca líneas con `Error obteniendo planes` o `Error procesando pago`

Habilitar debug en `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Extensiones Futuras

Posibles mejoras:
1. Más países (agregar opciones al selector)
2. Filtros adicionales (precio, datos, duración)
3. Comparación de planes
4. Historial de compras del usuario
5. Notificaciones por email
6. Panel de administración para ver transacciones
7. Soporte para otras pasarelas de pago

## Contacto y Soporte

Para problemas o preguntas:
1. Revisa esta documentación
2. Consulta logs en `storage/logs/`
3. Verifica configuración en `.env`
4. Contacta al equipo de desarrollo
