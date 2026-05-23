<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu eSIM ya fue activada</title>
</head>
<body style="margin:0; padding:0; background-color:#eef6fb; font-family:Arial, Helvetica, sans-serif; color:#181c36;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef6fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(24,28,54,0.08); border:1px solid #d7e8f4;">
                    <tr>
                        <td style="padding:28px 32px; background:linear-gradient(90deg, #181c36 0%, #2d9cdb 100%); color:#ffffff;">
                            <p style="margin:0 0 10px; font-size:11px; line-height:1.4; letter-spacing:0.08em; text-transform:uppercase; opacity:0.85;">Alianza Nomad Esim - Xcertus</p>
                            <h1 style="margin:0; font-size:24px; line-height:1.3;">Tu eSIM ya fue activada</h1>
                            <p style="margin:10px 0 0; font-size:14px; opacity:0.9;">Te enviamos este correo con los datos necesarios para terminar la instalacion en tu dispositivo.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                                La activacion quedo asociada al correo <strong>{{ $recipientEmail }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; background:#f3f9fd; border:1px solid #cfe6f4; border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 8px; font-size:13px; color:#5a6785; text-transform:uppercase; letter-spacing:0.04em;">Plan activado</p>
                                        <p style="margin:0; font-size:22px; font-weight:700; color:#181c36;">{{ $esimData['data_amount'] ?? 'N/A' }} GB</p>
                                        <p style="margin:8px 0 0; font-size:14px; color:#4b587c;">Vigencia: {{ $esimData['duration_days'] ?? 'N/A' }} dias</p>
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($qrBase64))
                                <h2 style="margin:0 0 14px; font-size:18px; color:#181c36;">Escanea el código QR</h2>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                                    <tr>
                                        <td align="center" style="padding:20px; background:#f3f9fd; border:1px solid #cfe6f4; border-radius:12px;">
                                            <img src="data:image/png;base64,{{ $qrBase64 }}"
                                                 alt="QR de activación eSIM"
                                                 width="220"
                                                 height="220"
                                                 style="display:block; border:0;">
                                            <p style="margin:14px 0 0; font-size:13px; color:#5a6785; text-align:center;">
                                                Escanea este código desde los ajustes de tu dispositivo en <strong>Agregar eSIM</strong>.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

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
                                <p style="margin:0 0 22px; padding:14px 16px; background:#f3f9fd; border:1px solid #cfe6f4; border-radius:10px; font-size:13px; word-break:break-all; color:#181c36;">
                                    {{ $activationLink }}
                                </p>
                            @endif

                            <p style="margin:0; font-size:14px; line-height:1.7; color:#4b587c;">
                                Si tienes problemas para escanear el QR, usa los datos manuales de activacion que aparecen a continuacion.
                            </p>

                            @if(!empty($companionFormUrl))
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0 0; background:#f0f9ff; border:1px solid #bde0f5; border-radius:12px;">
                                    <tr>
                                        <td style="padding:18px 20px;">
                                            <p style="margin:0 0 6px; font-size:14px; font-weight:700; color:#181c36;">¿Viajan más contigo?</p>
                                            <p style="margin:0 0 14px; font-size:13px; color:#4b587c; line-height:1.6;">Tu voucher incluye viajeros adicionales. Haz clic en el botón para registrar sus correos y enviarles su eSIM gratuita.</p>
                                            <a href="{{ $companionFormUrl }}" style="display:inline-block; background:#2d9cdb; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:10px; font-weight:700; font-size:14px;">
                                                Registrar acompañantes
                                            </a>
                                            <p style="margin:12px 0 0; font-size:11px; color:#6f7a96; word-break:break-all;">Si el botón no funciona, copia este enlace: {{ $companionFormUrl }}</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>