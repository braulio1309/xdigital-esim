<?php

namespace App\Hooks\User;

use App\Hooks\HookContract;
use App\Helpers\Core\Traits\InstanceCreator;

class CustomRoute extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        // Redirect based on user type after login
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->user_type === 'beneficiario') {
                return [
                    'route_name' => 'beneficiario.dashboard',
                    'route_params' => null
                ];
            } elseif ($user->user_type === 'cliente') {
                return [
                    'route_name' => 'cliente.dashboard',
                    'route_params' => null
                ];
            }else{
                return [
                    'route_name' => 'report.view',
                    'route_params' => null  
                ];
            }
        }
        
        return [];
    }
}
