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

        // Determinar el tipo de usuario
        $isAdmin = !in_array($user->user_type ?? '', ['beneficiario', 'cliente']);

        $isPartner = $user->user_type === 'beneficiario';

        // Si es admin, mostrar solo el dashboard de métricas
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
                    'icon' => 'users',
                    'name' => 'Beneficiarios',
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
                    'icon' => 'file-text',
                    'name' => 'Reportes',
                    'url' => request()->root() . '/app/report-view',
                    'permission' => true,
                ],
                [
                    'icon' => 'settings',
                    'name' => 'Ajustes',
                    'url' => request()->root() . '/app-setting',
                    'permission' => true,
                ],
            ];
        } else if ($isPartner) {
            $menu = [
                //Agrega el dashboard de métricas para beneficiarios
                [
                    'icon' => 'bar-chart-2',
                    'name' => 'Dashboard',
                    'url' => request()->root() . '/admin/dashboard',
                    'permission' => true,
                ],
                [
                    'icon' => 'users',
                    'name' => 'Mis Clientes',
                    'url' => request()->root() . '/admin/clientes',
                    'permission' => true,
                ],
            ];
        } else {
            $menu = [];   
        }

        $view->with(['data' => $menu]);
    }
}
