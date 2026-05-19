<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienes una eSIM gratuita disponible</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#181c36;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f7fb; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(24,28,54,0.08);">
                <tr>
                    <td style="padding:28px 32px; background:linear-gradient(90deg, #181c36 0%, #623b86 100%); color:#ffffff;">
                        <h1 style="margin:0; font-size:24px; line-height:1.3;">¡Tienes una eSIM gratuita disponible!</h1>
                        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;">Actívala cuando quieras en pocos pasos.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 32px;">
                        <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                            Hola {{ trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: 'cliente' }},
                            @if($partnerName)
                                ya puedes activar tu eSIM gratuita con {{ $partnerName }}.
                            @else
                                ya puedes activar tu eSIM gratuita.
                            @endif
                        </p>

                        <p style="margin:0 0 24px; font-size:14px; line-height:1.7; color:#4b587c;">
                            Presiona el botón para abrir directamente tu formulario de registro eSIM.
                        </p>

                        <p style="margin:0 0 8px;">
                            <a href="{{ $activationUrl }}" style="display:inline-block; background:#181c36; color:#ffffff; text-decoration:none; padding:14px 20px; border-radius:10px; font-weight:700;">
                                Activar eSIM gratuita
                            </a>
                        </p>

                        <p style="margin:14px 0 0; font-size:12px; color:#6f7a96; word-break:break-all;">
                            Si no funciona el botón, copia este enlace en tu navegador:<br>
                            {{ $activationUrl }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
