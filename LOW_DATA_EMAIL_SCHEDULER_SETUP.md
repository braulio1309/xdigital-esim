# Configuración de aviso 75% de consumo

Se agregó el comando:

- `php artisan esim:notify-low-data`

Este comando consulta el uso de eSIM (API eSIM FX) y envía correo cuando una eSIM está en ~75% o más, incluyendo enlace seguro tokenizado para comprar sin login.

## Scheduler (recomendado)

En `app/Console/Kernel.php` quedó programado:

- `esim:notify-low-data` cada hora.

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
