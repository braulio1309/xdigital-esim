<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border-radius:16px;padding:32px;border:1px solid #e5e7eb;">
            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">Hola {{ $user->full_name }},</p>
            <p style="margin:0 0 24px;font-size:16px;line-height:1.6;">Tu codigo de verificacion para iniciar sesion es:</p>
            <div style="display:inline-block;padding:16px 24px;border-radius:12px;background:#111827;color:#ffffff;font-size:28px;letter-spacing:6px;font-weight:700;">
                {{ $code }}
            </div>
            <p style="margin:24px 0 0;font-size:14px;line-height:1.6;color:#6b7280;">Este codigo expira en 10 minutos. Si no solicitaste este acceso, puedes ignorar este correo.</p>
        </div>
    </div>
</body>
</html>
