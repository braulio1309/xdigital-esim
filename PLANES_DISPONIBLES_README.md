# Sistema de Planes Disponibles con Pago Stripe

## 📋 Descripción

Sistema completo para visualizar y comprar planes eSIM filtrados por país, con integración de pagos mediante Stripe y autenticación dinámica sin recargas de página.

## ✨ Características Principales

### Frontend
- 🌍 **Selector de países**: España (ES) y USA (US)
- 📱 **Grid responsive**: 4 columnas (desktop) → 2 (tablet) → 1 (mobile)
- 🎨 **Diseño atractivo**: Colores de marca Xcertus × Nomad
- 🔄 **Carga dinámica**: Planes se cargan via AJAX sin recargar
- 🆓 **Planes gratuitos**: Identificados con badge verde "GRATIS"

### Autenticación
- 🔐 **Login/Registro AJAX**: Sin recargar la página
- 📝 **Formularios validados**: En tiempo real
- ✅ **Auto-login**: Después de registro exitoso
- 🔒 **Sesión persistente**: Mantiene estado de usuario

### Pagos con Stripe
- 💳 **Stripe Elements**: Formulario seguro de tarjetas
- 🔐 **Payment Intents**: Flujo 3D Secure compatible
- ✅ **Validación robusta**: Cliente y servidor
- 🎯 **Test mode**: Soporta tarjetas de prueba

### Activación eSIM
- 📷 **Código QR**: Generación automática
- 📋 **Instalación manual**: SM-DP+ y código de activación
- 📱 **Copiar al portapapeles**: API moderna con fallback
- 💾 **Registro en BD**: Transacciones guardadas

## 🏗️ Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                        Frontend                             │
├─────────────────────────────────────────────────────────────┤
│  planes-disponibles.blade.php                               │
│  ├─ Vue.js inline (no compilation needed)                   │
│  ├─ Stripe.js (CDN)                                         │
│  └─ Bootstrap 4 + custom CSS                                │
└─────────────────────────────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      Backend API                            │
├─────────────────────────────────────────────────────────────┤
│  Controllers:                                               │
│  ├─ PlanesDisponiblesController                            │
│  │   ├─ index() - Vista principal                          │
│  │   ├─ getPlanes() - Planes por país                      │
│  │   ├─ createPaymentIntent() - Iniciar pago               │
│  │   ├─ procesarPago() - Confirmar y activar               │
│  │   └─ activarGratis() - Planes sin costo                 │
│  │                                                           │
│  └─ AuthController (API)                                    │
│      ├─ login() - AJAX login                                │
│      ├─ register() - AJAX registro                          │
│      ├─ logout() - AJAX logout                              │
│      └─ check() - Verificar autenticación                   │
└─────────────────────────────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      Services                               │
├─────────────────────────────────────────────────────────────┤
│  StripeService:                                             │
│  ├─ createPaymentIntent()                                   │
│  ├─ confirmPayment()                                        │
│  ├─ getPaymentStatus()                                      │
│  └─ cancelPayment()                                         │
│                                                              │
│  EsimFxService (existente):                                 │
│  ├─ getProducts($filters)                                   │
│  ├─ createOrder($productId, $transactionId)                 │
│  └─ activateOrder($orderId)                                 │
│                                                              │
│  ClienteService (existente):                                │
│  └─ save() - Crear cliente + usuario                        │
└─────────────────────────────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    External APIs                            │
├─────────────────────────────────────────────────────────────┤
│  Stripe API                    eSIM FX API                  │
│  ├─ Payment Intents            ├─ Auth Token                │
│  ├─ Charges                    ├─ Get Products              │
│  └─ Customers                  └─ Create Order              │
└─────────────────────────────────────────────────────────────┘
```

## 📦 Archivos Creados/Modificados

### Backend
```
app/
├── Http/
│   └── Controllers/
│       ├── Api/
│       │   └── AuthController.php          [NUEVO]
│       └── App/Cliente/
│           └── PlanesDisponiblesController.php [NUEVO]
└── Services/
    └── StripeService.php                   [NUEVO]

routes/
└── web.php                                 [MODIFICADO]

config/
└── services.php                            [MODIFICADO]

.env.example                                [MODIFICADO]
```

### Frontend
```
resources/
└── views/
    └── clientes/
        └── planes-disponibles.blade.php    [NUEVO]
```

### Documentación
```
TESTING_GUIDE.md                            [NUEVO]
PLANES_DISPONIBLES_README.md                [ESTE ARCHIVO]
```

## 🚀 Instalación

### 1. Clonar y configurar
```bash
git clone [repo]
cd xdigital-esim
composer install
cp .env.example .env
```

### 2. Configurar Variables de Entorno

Editar `.env`:

```bash
# Stripe (obtener en https://dashboard.stripe.com/test/apikeys)
STRIPE_KEY=pk_test_51...
STRIPE_SECRET=sk_test_51...

# eSIM FX (ya incluidas por defecto)
ESIMFX_BASE_URL=https://api.esimfx.com
ESIMFX_CLIENT_ID=7f4b881c-85fb-44b2-850c-10b2479a82b5
ESIMFX_CLIENT_KEY=b81889d2-8400-41eb-8783-bdf118a1810b

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xdigital_esim
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Preparar Base de Datos
```bash
php artisan migrate
```

### 4. Iniciar Servidor
```bash
php artisan serve
```

Visitar: `http://localhost:8000/planes-disponibles`

## 🧪 Testing

Ver documentación completa en: [`TESTING_GUIDE.md`](TESTING_GUIDE.md)

### Prueba Rápida

1. **Acceder**: `http://localhost/planes-disponibles`
2. **Seleccionar país**: España
3. **Elegir plan**: Click en "Comprar"
4. **Registrarse**:
   - Nombre: Test
   - Apellido: Usuario
   - Email: test@example.com
   - Contraseña: password123
5. **Pagar con tarjeta de prueba**:
   - Número: `4242 4242 4242 4242`
   - Fecha: 12/25
   - CVC: 123
6. **Verificar**: QR code y datos de instalación

## 🔒 Seguridad

### Implementado
- ✅ CSRF tokens en todos los formularios
- ✅ Validación server-side de todos los inputs
- ✅ Sanitización de datos
- ✅ Autenticación requerida para pagos
- ✅ API keys en variables de entorno
- ✅ Transacciones únicas con uniqid()
- ✅ Stripe Payment Intents (3D Secure compatible)
- ✅ Rate limiting en rutas públicas

### Recomendaciones Producción
- [ ] Activar Rate Limiting agresivo
- [ ] Configurar webhook de Stripe para confirmación
- [ ] Implementar logging detallado de transacciones
- [ ] Monitoreo de intentos de pago fallidos
- [ ] Backup automático de transacciones
- [ ] SSL/HTTPS obligatorio
- [ ] Configurar Stripe Radar para fraude

## 📊 Base de Datos

### Tabla: `transactions`

```sql
CREATE TABLE transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(255),           -- ID desde eSIM FX API
    transaction_id VARCHAR(255),     -- ID único: STRIPE-{cliente}-{time}-{uniq}
    status VARCHAR(50),              -- completed, pending, failed
    iccid VARCHAR(255),              -- ICCID de la eSIM
    esim_qr TEXT,                    -- String para generar QR
    creation_time TIMESTAMP,         -- Fecha/hora de creación
    cliente_id BIGINT,               -- FK a tabla clientes
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🎨 Diseño UI/UX

### Colores de Marca
```css
--xcertus-purple: #623b86;  /* Morado Xcertus */
--xcertus-yellow: #ffcc00;  /* Amarillo Xcertus */
--nomad-blue: #2d9cdb;      /* Azul Nomad */
--nomad-navy: #181c36;      /* Navy Nomad */
```

### Breakpoints Responsive
- **Desktop**: ≥1200px → 4 columnas
- **Laptop**: 992px-1199px → 3 columnas  
- **Tablet**: 768px-991px → 2 columnas
- **Mobile**: <768px → 1 columna

### Animaciones
- Hover en cards: `translateY(-5px)` + sombra
- Botones: `scale(1.02)` en hover
- Modales: Fade in/out Bootstrap
- Loading: Spinner con color brand

## 🔗 Rutas

### Públicas
```
GET  /planes-disponibles          → Vista principal
POST /planes/get-by-country       → Obtener planes por país
POST /api/auth/login             → Login AJAX
POST /api/auth/register          → Registro AJAX
GET  /api/auth/check             → Verificar sesión
POST /api/auth/logout            → Cerrar sesión
```

### Autenticadas
```
POST /planes/create-payment-intent → Crear Payment Intent
POST /planes/procesar-pago         → Procesar pago y activar
POST /planes/activar-gratis        → Activar plan gratuito
```

## 🐛 Troubleshooting

### Problema: "Stripe key is invalid"
**Solución**: Verificar que las keys en `.env` sean correctas y empiecen con `pk_test_` y `sk_test_`

### Problema: No se cargan los planes
**Solución**: 
1. Verificar credenciales eSIM FX en `.env`
2. Revisar logs: `tail -f storage/logs/laravel.log`
3. Verificar conexión a internet

### Problema: Modal no se abre
**Solución**:
1. Verificar que jQuery y Bootstrap estén cargados
2. Abrir DevTools Console (F12) y buscar errores JavaScript
3. Verificar que Vue esté inicializado correctamente

### Problema: No se puede copiar al portapapeles
**Solución**:
1. Usar HTTPS o localhost (requisito de Clipboard API)
2. En producción, asegurar SSL activo
3. Fallback automático a `document.execCommand` en navegadores antiguos

### Problema: Payment Intent falla
**Solución**:
1. Verificar que usuario esté autenticado
2. Revisar console de Stripe: https://dashboard.stripe.com/test/logs
3. Verificar que monto sea > 0 y moneda válida

## 📈 Métricas y Monitoring

### KPIs Recomendados
- Tasa de conversión (visitantes → compras)
- Tiempo promedio hasta compra
- Tasa de abandono en pago
- Planes más vendidos por país
- Errores de pago más comunes

### Logging
```php
// Todos los eventos importantes están logueados:
Log::info('Plan seleccionado', ['plan_id' => $planId]);
Log::error('Error en pago', ['error' => $e->getMessage()]);
```

## 🔄 Flujo Completo

```
Usuario → Selecciona País
    ↓
API eSIM FX → Retorna Planes
    ↓
Usuario → Selecciona Plan
    ↓
¿Plan Gratuito?
    SÍ → Verificar Auth → Activar eSIM → Mostrar QR
    NO ↓
    ↓
¿Usuario Autenticado?
    NO → Modal Login/Registro
    SÍ ↓
    ↓
Modal Pago Stripe
    ↓
Ingresar Tarjeta
    ↓
Stripe → Procesar Pago
    ↓
¿Pago Exitoso?
    NO → Mostrar Error → Reintentar
    SÍ ↓
    ↓
API eSIM FX → Activar Orden
    ↓
Guardar Transacción en BD
    ↓
Generar QR Code
    ↓
Mostrar Modal Éxito
    ↓
Usuario → Escanea QR o copia datos
```

## 📚 Recursos Externos

- [Stripe Payment Intents API](https://stripe.com/docs/payments/payment-intents)
- [Stripe Test Cards](https://stripe.com/docs/testing)
- [Stripe Elements](https://stripe.com/docs/stripe-js)
- [eSIM FX API Documentation](https://api.esimfx.com/docs)
- [Laravel Validation](https://laravel.com/docs/9.x/validation)
- [Vue.js 2.x Guide](https://v2.vuejs.org/v2/guide/)
- [Bootstrap 4 Documentation](https://getbootstrap.com/docs/4.6/)

## 🤝 Contribuir

1. Fork el proyecto
2. Crear branch (`git checkout -b feature/mejora`)
3. Commit cambios (`git commit -m 'Add: nueva característica'`)
4. Push al branch (`git push origin feature/mejora`)
5. Abrir Pull Request

## 📄 Licencia

[Tu Licencia Aquí]

## 👥 Autores

- **Backend**: Laravel 9 + PHP 8.3
- **Frontend**: Vue.js 2.7 + Bootstrap 4
- **Payment**: Stripe API v8.12
- **eSIM**: eSIM FX API v1

---

**Nota**: Este sistema está configurado para modo de prueba. Antes de pasar a producción, asegúrate de:
1. Cambiar keys de Stripe a modo live
2. Configurar webhooks de Stripe
3. Activar SSL/HTTPS
4. Revisar todas las configuraciones de seguridad
5. Realizar pruebas exhaustivas con tarjetas reales (pequeños montos)
