<?php

namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use Illuminate\Database\Seeder;

/**
 * Class SuperPartnerRoleSeeder
 *
 * Adds the "Super Partner" role to an existing installation without
 * truncating the roles table. Safe to run on production.
 */
class SuperPartnerRoleSeeder extends Seeder
{
    /**
     * Run the database seed.
     */
    public function run()
    {
        $superAdmin = User::first();

        Role::firstOrCreate(
            ['name' => 'Super Partner'],
            [
                'is_admin'   => 0,
                'type_id'    => Type::findByAlias('app')->id,
                'created_by' => $superAdmin->id,
                'is_default' => 0,
            ]
        );
    }
}
