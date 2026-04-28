<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso a tu cuenta eSIM</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f7fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,0.08);">
                    <tr>
                        <td style="background:#0f172a;padding:24px 32px;color:#ffffff;">
                            <h1 style="margin:0;font-size:24px;">Tu acceso ya está listo, aquí podrás ver la info de tu eSIM</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;">Hola {{ trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')) ?: 'cliente' }},</p>
                            <p style="margin:0 0 16px;">Se creó tu cuenta para ingresar a la plataforma.</p>
                            <p style="margin:0 0 8px;"><strong>Usuario:</strong> tu correo electrónico, {{ $cliente->email }}</p>
                            <p style="margin:0 0 16px;"><strong>Clave:</strong> tu cédula de identidad, {{ $plainPassword }}</p>
                            <p style="margin:0 0 20px;">Usa tu número de cédula exactamente como clave, sin caracteres adicionales al final.</p>
                            <p style="margin:0 0 24px;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:600;">Ir a iniciar sesión</a>
                            </p>
                            <p style="margin:0;color:#6b7280;font-size:14px;">Si no solicitaste este acceso, por favor responde a este correo.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>