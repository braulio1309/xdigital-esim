# Configuración de avisos 75% / 90% de consumo

Se agregó el comando:

- `php artisan notificar:consumo-esim`

Este comando consulta el uso de eSIM (API eSIM FX) y envía correo cuando una transacción alcanza los umbrales de **75%** y **90%**, incluyendo enlace seguro tokenizado para comprar sin login.
Cada umbral se envía **una sola vez por transacción** y queda marcado en DB (`usage_75_notified_at`, `usage_90_notified_at`).

## Scheduler (recomendado)

En `app/Console/Kernel.php` quedó programado:

- `notificar:consumo-esim` cada 30 minutos (`everyThirtyMinutes()`).

Configura cron del servidor (una vez):

```bash
* * * * * cd /ruta/a/xdigital-esim && php artisan schedule:run >> /dev/null 2>&1
```

## Cola (recomendado en producción)

Para no bloquear solicitudes web:

1. Definir `QUEUE_CONNECTION=database` (o redis).
2. Levantar worker:

```bash
php artisan queue:work --queue=default --tries=3
```

> Si no hay worker, el envío funcionará con `sync`, pero no es ideal para carga alta.

## Prueba local rápida

1. Ejecutar worker de colas:

```bash
php artisan queue:work --queue=default --tries=3
```

2. Ejecutar scheduler manual:

```bash
php artisan schedule:run
```

3. (Opcional) Ejecutar directo el comando:

```bash
php artisan notificar:consumo-esim
```
