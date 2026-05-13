<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Models\Core\Auth\Role;

class AfterLogin extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        if (
            $this->model
            && $this->model->user_type === 'admin_partner'
            && !$this->model->roles()->where('name', 'Super Partner')->exists()
        ) {
            $role = Role::query()->where('name', 'Super Partner')->first();

            if ($role) {
                $this->model->roles()->syncWithoutDetaching([$role->id]);
                cache()->forget('user-roles-permissions-' . $this->model->id);
                cache()->forget('auth-user-permissions-' . $this->model->id);
            }
        }

        return $this->model;
    }
}
