<?php

namespace App\Mail\Core\User;

use App\Models\Core\Auth\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginVerificationCodeMail extends Mailable
{
    use SerializesModels;

    protected $user;
    protected $code;

    public function __construct(User $user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this->view('emails.auth.login-code', [
            'user' => $this->user,
            'code' => $this->code,
        ])->subject(trans('default.two_factor_login_mail_subject'));
    }
}
