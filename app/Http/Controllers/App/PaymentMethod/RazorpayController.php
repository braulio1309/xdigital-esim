<?php


namespace App\Http\Controllers\App\PaymentMethod;

use App\Models\Core\Setting\Setting;
use App\Repositories\Core\Setting\SettingRepository;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Stripe;


use App\Http\Controllers\Controller;

class RazorpayController extends Controller
{
    private $settings;

    public function __construct()
    {
        $this->settings = resolve(SettingRepository::class)
            ->formatSettings(
                Setting::query()->where('context', 'razorpay')
                    ->get(), true
            );
    }

    public function razorPayInformation()
    {
        return $this->settings;
    }


    public function razorPost(Request $request)
    {
        $input = $request->all();
        $api = new Api($this->settings['key_id'], $this->settings['secret_key']);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return created_responses('razor_payment_success');
    }

    public function razorpayStatus()
    {
        return ['isSetRazorpay' => $this->settings ? true : false];
    }
}