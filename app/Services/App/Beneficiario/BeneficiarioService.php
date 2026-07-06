<?php

namespace App\Services\App\Beneficiario;

use App\Helpers\Core\Traits\FileHandler;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Settings\BeneficiaryCountryPrice;
use App\Models\App\Settings\BeneficiaryPlanMargin;
use App\Models\App\Settings\BeneficiaryPlanPrice;
use App\Models\App\Settings\SuperPartnerCountryPrice;
use App\Models\App\Settings\SuperPartnerPlanMargin;
use App\Models\App\Settings\SuperPartnerPlanPrice;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\App\AppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BeneficiarioService extends AppService
{
    use FileHandler;

    public function __construct(Beneficiario $beneficiario)
    {
        $this->model = $beneficiario;
    }

    /**
     * Save Beneficiario and create associated user
     * @param array $options
     * @return Beneficiario
     */
    public function save($options = [])
    {
        return DB::transaction(function () use ($options) {
            $attributes = count($options) ? $options : request()->all();

            if (empty($attributes['super_partner_id']) && auth()->check() && auth()->user()->user_type === 'super_partner') {
                $attributes['super_partner_id'] = SuperPartner::where('user_id', auth()->id())->value('id');
            }

            // Generate unique codigo if not provided
            if (!isset($attributes['codigo'])) {
                $attributes['codigo'] = $this->generateUniqueCode();
            }
            // Handle logo upload
            if (request()->hasFile('logo')) {
                $attributes['logo'] = $this->uploadImage(request()->file('logo'), 'beneficiarios/logos');
            }

            // Create the beneficiario - pass attributes as options to avoid request mutation
            $beneficiario = parent::save($attributes);
            
            // Create user if not already associated
            if (!$beneficiario->user_id && isset($attributes['nombre'])) {
                $user = $this->createUserForBeneficiario($beneficiario, $attributes);
                $beneficiario->user_id = $user->id;
                $beneficiario->save();
            }

            // If a super_partner_id is set, inherit all commissions and prices from that super partner
            if (!empty($attributes['super_partner_id'])) {
                $this->inheritFromSuperPartner($beneficiario, (int) $attributes['super_partner_id']);
            }
            
            return $beneficiario;
        });
    }

    /**
     * Generate a unique 8-character alphanumeric code
     * @return string
     */
    protected function generateUniqueCode()
    {
        do {
            // Generate random 8 character uppercase alphanumeric string using Laravel's Str helper
            $codigo = strtoupper(Str::random(8));
            
            // Check if code already exists
            $exists = Beneficiario::where('codigo', $codigo)->exists();
        } while ($exists);
        
        return $codigo;
    }

    /**
     * Create a user for the beneficiario
     * @param Beneficiario $beneficiario
     * @param array $attributes
     * @return User
     */
    protected function createUserForBeneficiario(Beneficiario $beneficiario, array $attributes)
    {
        $email = mb_strtolower(trim((string) $attributes['email']));
        
        // Use provided password from request
        $password = $attributes['password'];
        
        // Get active status
        $status = Status::findByNameAndType('status_active', 'user');

        $existingUser = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existingUser) {
            if (!$existingUser->hasRole('cliente')) {
                throw ValidationException::withMessages([
                    'email' => 'Este correo ya pertenece a otro usuario.',
                ]);
            }

            $existingUser->fill([
                'first_name' => $beneficiario->nombre,
                'last_name' => $attributes['apellido'] ?? '',
                'password' => $password,
                'user_type' => 'beneficiario',
                'status_id' => $status->id,
                'beneficiario_id' => null,
                'super_partner_id' => null,
            ]);
            $existingUser->save();

            if (!$existingUser->hasRole('beneficiario')) {
                $existingUser->assignRole('beneficiario');
            }

            return $existingUser;
        }
        
        $user = User::create([
            'first_name' => $beneficiario->nombre,
            'last_name'  => $attributes['apellido'] ?? '',
            'email'      => $email,
            'password'   => Hash::make($password),
            'user_type'  => 'beneficiario',
            'status_id'  => $status->id,
        ]);
        $user->assignRole('beneficiario');
        
        return $user;
    }

    /**
     * Copy all commissions, plan margins, plan prices and country prices from a super partner
     * to this beneficiario. Existing records are replaced.
     *
     * @param Beneficiario $beneficiario
     * @param int          $superPartnerId
     * @return void
     */
    protected function inheritFromSuperPartner(Beneficiario $beneficiario, int $superPartnerId): void
    {
        $superPartner = SuperPartner::find($superPartnerId);
        if (!$superPartner) {
            return;
        }

        // Copy scalar commission fields
        $beneficiario->commission_percentage        = $superPartner->commission_percentage;
        $beneficiario->free_esim_rate               = $superPartner->free_esim_rate;
        $beneficiario->sale_commission_latam_pct    = $superPartner->sale_commission_latam_pct;
        $beneficiario->sale_commission_usa_ca_eu_pct = $superPartner->sale_commission_usa_ca_eu_pct;
        $beneficiario->save();

        // Copy plan margins
        $spMargins = SuperPartnerPlanMargin::where('super_partner_id', $superPartnerId)->get();
        foreach ($spMargins as $spMargin) {
            BeneficiaryPlanMargin::updateOrCreate(
                [
                    'beneficiario_id' => $beneficiario->id,
                    'plan_capacity'   => $spMargin->plan_capacity,
                ],
                [
                    'margin_percentage' => $spMargin->margin_percentage,
                    'is_active'         => $spMargin->is_active,
                ]
            );
        }

        // Copy plan prices
        $spPrices = SuperPartnerPlanPrice::where('super_partner_id', $superPartnerId)->get();
        foreach ($spPrices as $spPrice) {
            BeneficiaryPlanPrice::updateOrCreate(
                [
                    'beneficiario_id' => $beneficiario->id,
                    'plan_capacity'   => $spPrice->plan_capacity,
                ],
                [
                    'price'     => $spPrice->price,
                    'is_active' => $spPrice->is_active,
                ]
            );
        }

        // Copy country prices
        $spCountryPrices = SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)->get();
        foreach ($spCountryPrices as $spCountryPrice) {
            BeneficiaryCountryPrice::updateOrCreate(
                [
                    'beneficiario_id' => $beneficiario->id,
                    'country_code'    => $spCountryPrice->country_code,
                    'plan_capacity'   => $spCountryPrice->plan_capacity,
                ],
                [
                    'percentage' => $spCountryPrice->percentage,
                    'price'      => $spCountryPrice->price,
                    'is_active'  => $spCountryPrice->is_active,
                ]
            );
        }
    }

    /**
     * Update Beneficiario service
     * @param Beneficiario $beneficiario
     * @return Beneficiario
     */
    public function update(Beneficiario $beneficiario)
    {
        $previousSuperPartnerId = $beneficiario->super_partner_id;

        $beneficiario->fill(request()->only(['nombre', 'descripcion', 'free_esim_rate', 'super_partner_id']));

        // Handle logo upload
        if (request()->hasFile('logo')) {
            // Delete old logo if exists
            if ($beneficiario->logo) {
                $this->deleteImage($beneficiario->logo);
            }
            $beneficiario->logo = $this->uploadImage(request()->file('logo'), 'beneficiarios/logos');
        }

        $this->model = $beneficiario;

        $beneficiario->save();

        // If a super_partner_id was newly assigned (or changed), inherit commissions and prices
        $newSuperPartnerId = (int) ($beneficiario->super_partner_id ?? 0);
        if ($newSuperPartnerId && $newSuperPartnerId !== (int) ($previousSuperPartnerId ?? 0)) {
            $this->inheritFromSuperPartner($beneficiario, $newSuperPartnerId);
        }

        // Update linked user credentials if provided
        if ($beneficiario->user_id) {
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
                User::where('id', $beneficiario->user_id)->update($userUpdates);
            }
        }

        return $beneficiario;
    }

    /**
     * Delete Beneficiario service
     * @param Beneficiario $beneficiario
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Beneficiario $beneficiario)
    {
        return $beneficiario->delete();
    }
}
