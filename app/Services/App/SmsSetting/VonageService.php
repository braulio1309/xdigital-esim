<?php

namespace App\Services\App\SmsSetting;

use App\Models\Core\Setting\Setting;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\App\AppService;

class VonageService extends AppService
{
    public function updateVonageCredentials($request)
    {
        foreach ($request->only('sms_sent_from', 'sms_driver', 'api_key', 'api_secret', 'test_number') as $key => $item) {

            $setting = resolve(SettingRepository::class)->createSettingInstance($key, 'sms_setting');

            $setting->name = $key;
            $setting->value = encrypt($item);
            $setting->context = 'sms_setting';

            $setting->save();

        }

    }

    public function updateTwilioCredentials($request)
    {
        foreach ($request->only('sms_driver', 'account_sid', 'auth_token', 'twilio_number', 'twilio_test_number') as $key => $item) {

            $setting = resolve(SettingRepository::class)->createSettingInstance($key, 'sms_setting');

            $setting->name = $key;
            $setting->value = encrypt($item);
            $setting->context = 'sms_setting';

            $setting->save();

        }

    }

    public function getData()
    {
        $data = resolve(SettingRepository::class)
            ->formatSettings(
                Setting::query()->where('context', 'sms_setting')->get(), true
            );

        if (filled($data)) {
            return [
                'sms_sent_from' => @$data['sms_sent_from'],
                'sms_driver' => @$data['sms_driver'],
                'api_key' => @$data['api_key'],
                'api_secret' => @$data['api_secret'],
                'test_number' => @$data['test_number'],
                'account_sid' => @$data['account_sid'],
                'auth_token' => @$data['auth_token'],
                'twilio_number' => @$data['twilio_number'],
                'twilio_test_number' => @$data['twilio_test_number'],
            ];
        } else [];
    }
}