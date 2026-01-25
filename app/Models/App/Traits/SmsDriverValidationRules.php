<?php

namespace App\Models\App\Traits;


trait SmsDriverValidationRules
{
    public function vonageRules()
    {
        return [
            'sms_sent_from' => 'required',
            'sms_driver' => 'required',
            'api_key' => 'required',
            'api_secret' => 'required',
        ];
    }
    public function twilioRules()
    {
        return [
            'sms_driver' => 'required',
            'account_sid' => 'required',
            'auth_token' => 'required',
        ];
    }
}