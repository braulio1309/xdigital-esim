<?php

namespace App\Helpers\App\Traits;


use App\Services\App\SmsSetting\VonageService;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Twilio\Rest\Client as TwilioClient;

trait SmsHelper
{
    public function sendSms($from, $phone)
    {
        $data = resolve(VonageService::class)->getData();

        if (empty($data['api_key']) || empty($data['api_secret'])) {

            return updated_responses('sms_setting');

        } else {
            try {
                $basic = new Basic($data['api_key'], $data['api_secret']);
                $client = new Client($basic);

                $message = $client->message()->send([
                    'to' => preg_replace('/(\W*)/', '', $phone),
                    'from' => $from,
                    'text' => "Test sms"
                ]);

                return $message;
            } catch (\Exception $exception) {

                return $exception;
            }

        }
    }

    public function sendTwilioSms($phone)
    {
        $data = resolve(VonageService::class)->getData();

        if (empty($data['account_sid']) || empty($data['auth_token'])) {

            return updated_responses('sms_setting');

        } else {
            try {
                $client = new TwilioClient($data['account_sid'], $data['auth_token']);
                $client->messages->create($phone, [
                    'from' => $data['twilio_number'],
                    'body' => "Test sms"]);

                return $client;
            } catch (\Exception $exception) {

                return $exception;
            }

        }
    }

}