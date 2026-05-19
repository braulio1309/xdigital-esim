<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu eSIM está cerca de agotarse</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#181c36;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f7fb; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(24,28,54,0.08);">
                <tr>
                    <td style="padding:28px 32px; background:linear-gradient(90deg, #181c36 0%, #623b86 100%); color:#ffffff;">
                        <h1 style="margin:0; font-size:24px; line-height:1.3;">Tu eSIM está cerca del límite</h1>
                        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;">Has consumido aproximadamente {{ number_format($usagePercentage, 0) }}% del plan.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 32px;">
                        <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                            Hola {{ trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: 'cliente' }}, te recomendamos comprar un nuevo plan para tu eSIM.
                        </p>

                        @if($transaction->iccid)
                            <p style="margin:0 0 20px; font-size:14px; line-height:1.6; color:#4b587c;">
                                ICCID objetivo: <strong>{{ $transaction->iccid }}</strong>
                            </p>
                        @endif

                        <p style="margin:0 0 8px;">
                            <a href="{{ $rechargeUrl }}" style="display:inline-block; background:#181c36; color:#ffffff; text-decoration:none; padding:14px 20px; border-radius:10px; font-weight:700;">
                                Recargar ahora
                            </a>
                        </p>

                        <p style="margin:14px 0 0; font-size:12px; color:#6f7a96; word-break:break-all;">
                            El enlace es seguro y temporal. Si no funciona el botón, copia este enlace en tu navegador:<br>
                            {{ $rechargeUrl }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
