<?php

namespace App\Http\Controllers\App\Settings;

use App\Hooks\Settings\AfterDeliverySettingSaved;
use App\Hooks\Settings\BeforeDeliverySettingSaved;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\StorageRequest;
use App\Services\Core\Setting\DeliverySettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class StorageSettingController extends Controller
{

    public function __construct(DeliverySettingService $setting)
    {
        $this->service = $setting;
    }


    public function index()
    {
        return resolve(DeliverySettingService::class)->getFormattedDeliverySettings('storage_configuration');
    }

    public function update(StorageRequest $request)
    {
        BeforeDeliverySettingSaved::new()
            ->handle();
//        Artisan::call('optimize:clear');

        foreach ($request->only('storage_type', 'aws_bucket_name', 'aws_region', 'aws_access_key', 'aws_secret_key', 'aws_use_path_style_end_point') as $key => $value) {
            $this->service->update($key, $value, 'storage_configuration');
        }

        AfterDeliverySettingSaved::new()
            ->handle();

        return updated_responses('settings', [
            'settings' => $this->service->getFormattedDeliverySettings('storage_configuration')
        ]);
    }
}