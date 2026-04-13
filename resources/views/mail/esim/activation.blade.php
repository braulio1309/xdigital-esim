<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu eSIM ya fue activada</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#181c36;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f7fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(24,28,54,0.08);">
                    <tr>
                        <td style="padding:28px 32px; background:linear-gradient(90deg, #181c36 0%, #623b86 100%); color:#ffffff;">
                            <h1 style="margin:0; font-size:24px; line-height:1.3;">Tu eSIM ya fue activada</h1>
                            <p style="margin:10px 0 0; font-size:14px; opacity:0.9;">Te enviamos este correo con los datos necesarios para terminar la instalacion en tu dispositivo.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                                La activacion quedo asociada al correo <strong>{{ $recipientEmail }}</strong>
                                @if($partnerName)
                                    para el aliado <strong>{{ $partnerName }}</strong>.
                                @else
                                    .
                                @endif
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; background:#f8faff; border:1px solid #dbe7f3; border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 8px; font-size:13px; color:#5a6785; text-transform:uppercase; letter-spacing:0.04em;">Plan activado</p>
                                        <p style="margin:0; font-size:22px; font-weight:700; color:#181c36;">{{ $esimData['data_amount'] ?? 'N/A' }} GB</p>
                                        <p style="margin:8px 0 0; font-size:14px; color:#4b587c;">Vigencia: {{ $esimData['duration_days'] ?? 'N/A' }} dias</p>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:0 0 14px; font-size:18px; color:#181c36;">Datos de activacion manual</h2>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #edf1f7; width:180px; font-size:14px; color:#5a6785;"><strong>SM-DP+</strong></td>
                                    <td style="padding:12px 0; border-bottom:1px solid #edf1f7; font-size:14px; color:#181c36; word-break:break-word;">{{ $esimData['smdp'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #edf1f7; width:180px; font-size:14px; color:#5a6785;"><strong>Codigo de activacion</strong></td>
                                    <td style="padding:12px 0; border-bottom:1px solid #edf1f7; font-size:14px; color:#181c36; word-break:break-word;">{{ $esimData['code'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0; width:180px; font-size:14px; color:#5a6785;"><strong>ICCID</strong></td>
                                    <td style="padding:12px 0; font-size:14px; color:#181c36; word-break:break-word;">{{ $esimData['iccid'] ?? 'N/A' }}</td>
                                </tr>
                            </table>

                            @if($activationLink)
                                <p style="margin:0 0 18px; font-size:14px; line-height:1.6; color:#4b587c;">
                                    Si tu dispositivo soporta apertura directa del plan, puedes intentar usar este enlace de activacion:
                                </p>
                                <p style="margin:0 0 22px; padding:14px 16px; background:#f3f6fb; border-radius:10px; font-size:13px; word-break:break-all; color:#181c36;">
                                    {{ $activationLink }}
                                </p>
                            @endif

                            <p style="margin:0; font-size:14px; line-height:1.7; color:#4b587c;">
                                Si no puedes escanear el QR desde la web, usa los datos manuales anteriores dentro de la opcion <strong>Agregar eSIM</strong> o <strong>Plan de datos moviles</strong> de tu telefono.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>