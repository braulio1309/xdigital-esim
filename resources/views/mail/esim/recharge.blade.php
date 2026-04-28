<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu eSIM fue recargada</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#181c36;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f7fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(24,28,54,0.08);">
                    <tr>
                        <td style="padding:28px 32px; background:linear-gradient(90deg, #181c36 0%, #623b86 100%); color:#ffffff;">
                            <h1 style="margin:0; font-size:24px; line-height:1.3;">Tu eSIM fue recargada</h1>
                            <p style="margin:10px 0 0; font-size:14px; opacity:0.9;">Se añadieron datos a tu eSIM existente. Los nuevos datos ya están disponibles.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                                La recarga fue procesada para el correo <strong>{{ $recipientEmail }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; background:#f8faff; border:1px solid #dbe7f3; border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 8px; font-size:13px; color:#5a6785; text-transform:uppercase; letter-spacing:0.04em;">Datos recargados</p>
                                        <p style="margin:0; font-size:32px; font-weight:700; color:#181c36;">{{ $gbAmount }} GB</p>
                                        @if($planName)
                                            <p style="margin:8px 0 0; font-size:14px; color:#4b587c;">Plan: {{ $planName }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if($iccid)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:12px 0; width:180px; font-size:14px; color:#5a6785;"><strong>ICCID</strong></td>
                                        <td style="padding:12px 0; font-size:14px; color:#181c36; word-break:break-word;">{{ $iccid }}</td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:0; font-size:14px; line-height:1.7; color:#4b587c;">
                                Los nuevos datos fueron añadidos a tu eSIM. No necesitas reinstalar ni escanear ningún QR. Simplemente activa los datos en tu dispositivo si no lo has hecho.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
