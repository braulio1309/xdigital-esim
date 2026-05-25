<?php

namespace App\Http\Composer;

use App\Models\Core\Builder\Table\CustomTable;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $table = CustomTable::all();
        $user = auth()->user();

        $userType = $user->user_type ?? '';
        $userSubType = $user->user_sub_type ?? null;

        // Determine user category
        $isAdmin = !in_array($userType, ['beneficiario', 'cliente', 'super_partner', 'admin_partner', 'admin_beneficiario']);
        $isPartner = $userType === 'beneficiario';
        $isSuperPartner = in_array($userType, ['super_partner', 'admin_partner'], true);
        $isAdminBeneficiario = $userType === 'admin_beneficiario';

        // Atención al cliente: sub-users with atencion_cliente sub-type
        $isAtencionCliente = in_array($userSubType, ['atencion_cliente'], true)
            && in_array($userType, ['admin_partner', 'admin_beneficiario'], true);

        if ($isAdmin) {
            $menu = [
                [
                    'icon' => 'bar-chart-2',
                    'name' => 'Dashboard de Métricas',
                    'url' => request()->root() . '/admin/metrics',
                    'permission' => true,
                ],
                [
                    'icon' => 'user-check',
                    'name' => trans('custom.user_and_roles'),
                    'url' => request()->root() . '/users-and-roles',
                    'permission' => authorize_any(['view_users', 'view_roles', 'invite_user', 'create_roles']),
                ],
                [
                    'icon' => 'star',
                    'name' => 'Super Partners',
                    'url' => request()->root() . '/admin/super-partners',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Partners',
                    'url' => request()->root() . '/admin/beneficiarios',
                    'permission' => true,
                ],
                [
                    'icon' => 'user-check',
                    'name' => 'Clientes',
                    'url' => request()->root() . '/admin/clientes',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Transacciones',
                    'url' => request()->root() . '/admin/transactions',
                    'permission' => true,
                ],
                [
                    'icon' => 'dollar-sign',
                    'name' => 'Facturación Nomad',
                    'url' => request()->root() . '/admin/nomad-transactions',
                    'permission' => true,
                ],
                [
                    'icon' => 'credit-card',
                    'name' => 'Historial de Pagos',
                    'url' => request()->root() . '/admin/payment-histories',
                    'permission' => true,
                ],
                [
                    'icon' => 'settings',
                    'name' => 'Ajustes',
                    'url' => request()->root() . '/app-setting',
                    'permission' => authorize_any([
                        'view_settings',
                        'update_settings',
                        'view_delivery_settings',
                        'update_delivery_settings',
                        'view_sms_settings',
                        'update_sms_settings',
                        'view_recaptcha_settings',
                        'update_recaptcha_settings',
                        'view_notification_settings',
                        'update_notification_settings',
                        'view_notification_templates',
                        'update_notification_templates',
                    ]),
                ],
            ];
        } elseif ($isAtencionCliente) {
            // Atención al cliente users: only Transactions and Clients
            $menu = [
                [
                    'icon' => 'users',
                    'name' => 'Transacciones',
                    'url' => request()->root() . '/admin/transactions',
                    'permission' => true,
                ],
                [
                    'icon' => 'user',
                    'name' => 'Clientes',
                    'url' => request()->root() . '/admin/clientes',
                    'permission' => true,
                ],
            ];
        } elseif ($isSuperPartner) {
            $menu = [
                [
                    'icon' => 'bar-chart-2',
                    'name' => 'Dashboard',
                    'url' => request()->root() . '/admin/metrics',
                    'permission' => true,
                ],
                [
                    'icon' => 'user-check',
                    'name' => trans('custom.user_and_roles'),
                    'url' => request()->root() . '/users-and-roles',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Mis Partners',
                    'url' => request()->root() . '/admin/beneficiarios',
                    'permission' => true,
                ],
                [
                    'icon' => 'user',
                    'name' => 'Mis Clientes',
                    'url' => request()->root() . '/admin/clientes',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Transacciones',
                    'url' => request()->root() . '/admin/transactions',
                    'permission' => true,
                ],
                [
                    'icon' => 'file-text',
                    'name' => 'Reportes',
                    'url' => request()->root() . '/report-view',
                    'permission' => true,
                ],
            ];
        } elseif ($isAdminBeneficiario) {
            // Directivo sub-user of a partner (beneficiario)
            $menu = [
                [
                    'icon' => 'bar-chart-2',
                    'name' => 'Dashboard',
                    'url' => request()->root() . '/admin/dashboard',
                    'permission' => true,
                ],
                [
                    'icon' => 'user-check',
                    'name' => trans('custom.user_and_roles'),
                    'url' => request()->root() . '/users-and-roles',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Mis Clientes',
                    'url' => request()->root() . '/admin/clientes',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Transacciones',
                    'url' => request()->root() . '/admin/transactions',
                    'permission' => true,
                ],
                [
                    'icon' => 'credit-card',
                    'name' => 'Historial de Pagos',
                    'url' => request()->root() . '/admin/payment-histories',
                    'permission' => true,
                ],
            ];
        } elseif ($isPartner) {
            $menu = [
                [
                    'icon' => 'bar-chart-2',
                    'name' => 'Dashboard',
                    'url' => request()->root() . '/admin/dashboard',
                    'permission' => true,
                ],
                [
                    'icon' => 'user-check',
                    'name' => trans('custom.user_and_roles'),
                    'url' => request()->root() . '/users-and-roles',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Mis Clientes',
                    'url' => request()->root() . '/admin/clientes',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Transacciones',
                    'url' => request()->root() . '/admin/transactions',
                    'permission' => true,
                ],
                [
                    'icon' => 'credit-card',
                    'name' => 'Historial de Pagos',
                    'url' => request()->root() . '/admin/payment-histories',
                    'permission' => true,
                ],
            ];
        } else {
            $menu = [];
        }

        $view->with(['data' => $menu]);
    }
}
