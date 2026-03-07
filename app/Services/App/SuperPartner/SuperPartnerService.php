<?php

namespace App\Services\App\SuperPartner;

use App\Helpers\Core\Traits\FileHandler;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\App\AppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperPartnerService extends AppService
{
    use FileHandler;

    public function __construct(SuperPartner $superPartner)
    {
        $this->model = $superPartner;
    }

    /**
     * Save SuperPartner and create associated user
     *
     * @param array $options
     * @return SuperPartner
     */
    public function save($options = [])
    {
        return DB::transaction(function () use ($options) {
            $attributes = count($options) ? $options : request()->all();

            if (!isset($attributes['codigo'])) {
                $attributes['codigo'] = $this->generateUniqueCode();
            }

            if (request()->hasFile('logo')) {
                $attributes['logo'] = $this->uploadImage(request()->file('logo'), 'super-partners/logos');
            }

            $superPartner = parent::save($attributes);

            if (!$superPartner->user_id && isset($attributes['nombre']) && isset($attributes['email']) && isset($attributes['password'])) {
                $user = $this->createUserForSuperPartner($superPartner, $attributes);
                $superPartner->user_id = $user->id;
                $superPartner->save();
            }

            return $superPartner;
        });
    }

    /**
     * Generate a unique 8-character alphanumeric code
     *
     * @return string
     */
    protected function generateUniqueCode()
    {
        do {
            $codigo = strtoupper(Str::random(8));
            $exists = SuperPartner::where('codigo', $codigo)->exists();
        } while ($exists);

        return $codigo;
    }

    /**
     * Create a user for the super partner
     *
     * @param SuperPartner $superPartner
     * @param array $attributes
     * @return User
     */
    protected function createUserForSuperPartner(SuperPartner $superPartner, array $attributes)
    {
        $status = Status::findByNameAndType('status_active', 'user');

        $user = User::create([
            'first_name' => $superPartner->nombre,
            'last_name'  => $attributes['apellido'] ?? '',
            'email'      => $attributes['email'],
            'password'   => Hash::make($attributes['password']),
            'user_type'  => 'super_partner',
            'status_id'  => $status->id,
        ]);

        $user->assignRole('Super Partner');

        return $user;
    }

    /**
     * Update SuperPartner
     *
     * @param SuperPartner $superPartner
     * @return SuperPartner
     */
    public function update(SuperPartner $superPartner)
    {
        $superPartner->fill(request()->only(['nombre', 'descripcion', 'commission_percentage']));

        if (request()->hasFile('logo')) {
            if ($superPartner->logo) {
                $this->deleteImage($superPartner->logo);
            }
            $superPartner->logo = $this->uploadImage(request()->file('logo'), 'super-partners/logos');
        }

        $this->model = $superPartner;
        $superPartner->save();

        if ($superPartner->user_id) {
            $userUpdates = [];

            if (request()->filled('email')) {
                $userUpdates['email'] = request('email');
            }
            if (request()->filled('apellido')) {
                $userUpdates['last_name'] = request('apellido');
            }
            if (request()->filled('password')) {
                $userUpdates['password'] = Hash::make(request('password'));
            }

            if (!empty($userUpdates)) {
                User::where('id', $superPartner->user_id)->update($userUpdates);
            }
        }

        return $superPartner;
    }

    /**
     * Delete SuperPartner
     *
     * @param SuperPartner $superPartner
     * @return bool|null
     */
    public function delete(SuperPartner $superPartner)
    {
        return $superPartner->delete();
    }
}
