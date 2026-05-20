<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recarga tu eSIM</title>
</head>
<body style="margin:0; padding:0; background-color:#eef6fb; font-family:Arial, Helvetica, sans-serif; color:#181c36;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef6fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(24,28,54,0.08); border:1px solid #d7e8f4;">
                    <tr>
                        <td style="padding:28px 32px; background:linear-gradient(90deg, #181c36 0%, #2d9cdb 100%); color:#ffffff;">
                            <p style="margin:0 0 10px; font-size:11px; line-height:1.4; letter-spacing:0.08em; text-transform:uppercase; opacity:0.85;">Alianza Nomad Esim - Xcertus</p>
                            <h1 style="margin:0; font-size:24px; line-height:1.3;">Tu eSIM está lista para recarga</h1>
                            <p style="margin:10px 0 0; font-size:14px; opacity:0.9;">Si te estás quedando sin datos, puedes recargar de inmediato desde el botón.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                                Preparamos este acceso directo para la transacción <strong>{{ $transaction->transaction_id ?? ('#' . $transaction->id) }}</strong>.
                            </p>

                            @if(!empty($transaction->iccid))
                                <p style="margin:0 0 24px; font-size:14px; color:#4b587c; word-break:break-word;">
                                    ICCID: <strong>{{ $transaction->iccid }}</strong>
                                </p>
                            @endif

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                                <tr>
                                    <td align="center" style="border-radius:8px; background:#2d9cdb;">
                                        <a href="{{ $rechargeLink }}" style="display:inline-block; padding:12px 22px; color:#ffffff; text-decoration:none; font-weight:600; font-size:14px;">
                                            Recargar ahora
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
