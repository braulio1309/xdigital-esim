<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TranslatePasswordResetMailTemplateToSpanish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $templateIds = DB::table('notification_event_template')
            ->join('notification_events', 'notification_events.id', '=', 'notification_event_template.notification_event_id')
            ->where('notification_events.name', 'password_reset')
            ->pluck('notification_event_template.notification_template_id');

        if ($templateIds->isEmpty()) {
            return;
        }

        DB::table('notification_templates')
            ->whereIn('id', $templateIds)
            ->where('type', 'mail')
            ->update([
                'subject' => 'Enlace para restablecer tu contrasena en {app_name}',
                'default_content' => '<p><img src="{app_logo}" style="height: 75px"></p>
<p></p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hola {receiver_name}</span><br></p><p>Recibimos tu solicitud para restablecer la contrasena en {app_name}. Presiona el boton de abajo para continuar.</p><br>
<p><a href="{reset_password_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">Restablecer contrasena</a></p><br>
<p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
<p></p><p>Gracias por estar con nosotros.</p><p>Saludos,</p><p>{app_name}</p><p></p><p></p>',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $templateIds = DB::table('notification_event_template')
            ->join('notification_events', 'notification_events.id', '=', 'notification_event_template.notification_event_id')
            ->where('notification_events.name', 'password_reset')
            ->pluck('notification_event_template.notification_template_id');

        if ($templateIds->isEmpty()) {
            return;
        }

        DB::table('notification_templates')
            ->whereIn('id', $templateIds)
            ->where('type', 'mail')
            ->update([
                'subject' => 'Password reset link provided by {app_name}',
                'default_content' => '<p><img src="{app_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Your request for reset password has been approved from {app_name}. Press the button below to reset the password.</p><br>
<p><a href="{reset_password_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">Reset password</a></p><br>

We are highly expecting you as soon as possible. Hope you\'ll join us.
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{app_name}</p><p></p><p></p>',
                'updated_at' => now(),
            ]);
    }
}
