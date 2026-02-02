# Sistema de Planes Disponibles - Guía de Pruebas

## Configuración Inicial

### 1. Variables de Entorno
Copia `.env.example` a `.env` y configura las siguientes variables:

```bash
# Stripe Test Keys (obtener de https://dashboard.stripe.com/test/apikeys)
STRIPE_KEY=pk_test_51...
STRIPE_SECRET=sk_test_51...

# eSIM FX API (ya configuradas por defecto)
ESIMFX_BASE_URL=https://api.esimfx.com
ESIMFX_CLIENT_ID=7f4b881c-85fb-44b2-850c-10b2479a82b5
ESIMFX_CLIENT_KEY=b81889d2-8400-41eb-8783-bdf118a1810b
```

### 2. Instalar Dependencias
```bash
composer install
npm install
npm run dev
```

### 3. Base de Datos
```bash
php artisan migrate
```

## Casos de Prueba

### Test 1: Vista de Planes Disponibles
**URL**: `http://localhost/planes-disponibles`

**Pasos**:
1. Navegar a la URL
2. Verificar que se muestre el logo de Nomad eSIM
3. Verificar que aparezca el selector de países con opciones:
   - "Seleccione un país"
   - "España"
   - "Estados Unidos"
4. Verificar el mensaje informativo inicial

**Resultado Esperado**:
- ✅ Logo visible
- ✅ Selector de países funcional
- ✅ Mensaje: "Seleccione un país para ver los planes disponibles"

---

### Test 2: Carga de Planes por País

**Pasos**:
1. Seleccionar "España" en el dropdown
2. Esperar a que cargue
3. Observar el spinner de carga
4. Ver los planes en grid de 4 columnas

**Resultado Esperado**:
- ✅ Spinner visible mientras carga
- ✅ Planes mostrados en grid responsive
- ✅ Cada plan muestra:
  - Duración (ej: "30 días")
  - Datos (ej: "10GB")
  - Precio (ej: "5 USD" o "GRATIS")
  - Botón "Comprar"

**Cambiar a USA**:
1. Seleccionar "Estados Unidos"
2. Verificar que se carguen planes diferentes

---

### Test 3: Registro de Nuevo Usuario

**Pasos**:
1. Seleccionar un plan con precio > 0
2. Click en "Comprar"
3. Se abre modal de autenticación
4. Click en tab "Registrarse"
5. Llenar formulario:
   - Nombre: "Test"
   - Apellido: "Usuario"
   - Email: "test@example.com"
   - Contraseña: "password123"
   - Confirmar Contraseña: "password123"
6. Click en "Registrarse"

**Resultado Esperado**:
- ✅ Modal se abre correctamente
- ✅ Formulario de registro funcional
- ✅ Registro exitoso
- ✅ Modal se cierra
- ✅ Se abre modal de pago automáticamente

---

### Test 4: Login de Usuario Existente

**Pasos**:
1. Seleccionar un plan
2. Click en "Comprar"
3. En modal, permanecer en tab "Iniciar Sesión"
4. Ingresar credenciales:
   - Email: usuario registrado
   - Contraseña: su contraseña
5. Click en "Iniciar Sesión"

**Resultado Esperado**:
- ✅ Login exitoso
- ✅ Modal de auth se cierra
- ✅ Modal de pago se abre

---

### Test 5: Pago con Stripe - Tarjeta Exitosa

**Tarjeta de Prueba Stripe**:
- Número: `4242 4242 4242 4242`
- Fecha: Cualquier fecha futura (ej: 12/25)
- CVC: Cualquier 3 dígitos (ej: 123)
- ZIP: Cualquier código postal

**Pasos**:
1. Completar login/registro (Test 3 o 4)
2. En modal de pago, verificar:
   - Plan seleccionado visible
   - Monto total correcto
3. Ingresar datos de tarjeta de prueba
4. Click en "Pagar [monto]"
5. Esperar procesamiento

**Resultado Esperado**:
- ✅ Tarjeta aceptada
- ✅ Pago procesado exitosamente
- ✅ Modal de pago se cierra
- ✅ Modal de éxito se abre mostrando:
  - ✅ Icono de check verde
  - ✅ Código QR generado
  - ✅ Dirección SM-DP+
  - ✅ Código de activación
  - ✅ Botones "Copiar" funcionales

---

### Test 6: Pago con Stripe - Tarjeta Rechazada

**Tarjeta de Prueba (Siempre rechazada)**:
- Número: `4000 0000 0000 0002`
- Fecha: Cualquier fecha futura
- CVC: Cualquier 3 dígitos

**Pasos**:
1. Ingresar tarjeta rechazada
2. Intentar pagar

**Resultado Esperado**:
- ✅ Error mostrado en el modal
- ✅ Mensaje claro: "Your card was declined"
- ✅ Usuario puede reintentar con otra tarjeta

---

### Test 7: Plan Gratuito

**Pasos**:
1. Si hay planes con precio = 0 (marcados "GRATIS")
2. Click en "Comprar" de un plan gratuito
3. Completar login/registro si no está autenticado
4. El plan se activa automáticamente sin pago

**Resultado Esperado**:
- ✅ No se solicita pago
- ✅ eSIM activada directamente
- ✅ Modal de éxito con QR y datos

---

### Test 8: Copiar al Portapapeles

**Pasos**:
1. Después de activar un plan exitosamente
2. En modal de éxito, click en "Copiar" junto a SM-DP+
3. Pegar en un editor de texto
4. Click en "Copiar" junto a Código de Activación
5. Pegar en un editor de texto

**Resultado Esperado**:
- ✅ Mensaje "Copiado al portapapeles"
- ✅ Texto copiado correctamente
- ✅ Funciona en navegadores modernos (Chrome, Firefox, Safari)

---

### Test 9: Responsividad

**Pasos Desktop (>1200px)**:
1. Abrir en pantalla grande
2. Verificar grid de planes

**Resultado Esperado**: 
- ✅ 4 planes por fila

**Pasos Tablet (768px - 1200px)**:
1. Redimensionar ventana o usar DevTools
2. Verificar grid

**Resultado Esperado**:
- ✅ 2 planes por fila

**Pasos Mobile (<768px)**:
1. Abrir en móvil o DevTools
2. Verificar grid

**Resultado Esperado**:
- ✅ 1 plan por fila
- ✅ Cards apiladas verticalmente
- ✅ Botones y formularios accesibles

---

### Test 10: Validaciones

**Email Duplicado**:
1. Intentar registrar con email existente
**Resultado**: ❌ Error "Este email ya está registrado"

**Contraseñas No Coinciden**:
1. Registro con contraseñas diferentes
**Resultado**: ❌ Error "Las contraseñas no coinciden"

**Campos Vacíos**:
1. Intentar login/registro sin llenar campos
**Resultado**: ❌ Validación HTML5 previene envío

---

## Tarjetas de Prueba Stripe Adicionales

### Tarjetas Exitosas
- Visa: `4242 4242 4242 4242`
- Mastercard: `5555 5555 5555 4444`
- American Express: `3782 822463 10005`

### Tarjetas con Errores
- Rechazada: `4000 0000 0000 0002`
- Fondos insuficientes: `4000 0000 0000 9995`
- CVC incorrecto: `4000 0000 0000 0127`
- Expirada: `4000 0000 0000 0069`

Más tarjetas: https://stripe.com/docs/testing

---

## Verificación en Base de Datos

Después de un pago exitoso, verificar:

```sql
-- Verificar transacción creada
SELECT * FROM transactions 
WHERE transaction_id LIKE 'STRIPE-%' 
ORDER BY creation_time DESC 
LIMIT 1;

-- Verificar datos incluidos
-- - order_id
-- - transaction_id (STRIPE-{cliente_id}-{timestamp}-{uniqid})
-- - status (completed)
-- - iccid
-- - esim_qr
-- - cliente_id
```

---

## Logs y Debugging

### Logs de Laravel
```bash
tail -f storage/logs/laravel.log
```

### Logs de Stripe
Ir a: https://dashboard.stripe.com/test/logs

### Console del Navegador
Abrir DevTools (F12) y revisar:
- Errores JavaScript
- Respuestas de API (Network tab)
- Errores de Vue

---

## Problemas Comunes

### 1. "API key inválida"
**Solución**: Verificar que `STRIPE_KEY` y `STRIPE_SECRET` estén en `.env`

### 2. "No hay planes disponibles"
**Solución**: Verificar credenciales eSIM FX API en `.env`

### 3. Modal no se abre
**Solución**: 
- Verificar que jQuery y Bootstrap estén cargados
- Verificar console del navegador

### 4. Error al copiar
**Solución**: 
- Debe usar HTTPS o localhost
- Navegadores antiguos pueden no soportar Clipboard API

---

## Checklist Final

Antes de considerar completo:

- [ ] Vista se carga correctamente
- [ ] Selector de países funciona
- [ ] Planes se cargan por país
- [ ] Registro de usuario funciona
- [ ] Login funciona
- [ ] Pago con Stripe exitoso
- [ ] Pago rechazado maneja error
- [ ] Planes gratuitos se activan
- [ ] QR se genera correctamente
- [ ] Copiar al portapapeles funciona
- [ ] Responsive en mobile, tablet, desktop
- [ ] Validaciones funcionan
- [ ] Transacciones se guardan en BD
- [ ] Logs no muestran errores críticos

---

## Soporte

Para problemas o preguntas:
1. Revisar logs de Laravel
2. Revisar console del navegador
3. Verificar credenciales de API
4. Consultar documentación de Stripe: https://stripe.com/docs
5. Consultar documentación de eSIM FX API
