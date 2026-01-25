<?php

namespace App\Helpers\Config;

use App\Helpers\Core\Traits\InstanceCreator;
use App\Services\Core\Setting\DeliverySettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetStorageConfig
{
    use InstanceCreator;

    public function clear()
    {
        Artisan::call('optimize:clear');
        return $this;
    }

    public function set()
    {
        $storage_config = resolve(DeliverySettingService::class)->getFormattedDeliverySettings('storage_configuration');

        if ($storage_config) {
            Config::set('filesystems.default', $storage_config['storage_type']);
            if ($storage_config['storage_type'] === 's3') {
                Config::set('filesystems.disks.s3.key', $storage_config['aws_access_key']);
                Config::set('filesystems.disks.s3.secret', $storage_config['aws_secret_key']);
                Config::set('filesystems.disks.s3.region', $storage_config['aws_region']);
                Config::set('filesystems.disks.s3.bucket', $storage_config['aws_bucket_name']);
            }
        }
    }
}
