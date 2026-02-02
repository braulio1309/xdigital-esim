<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        // Redirect based on user type
        if ($user && $user->user_type === 'beneficiario') {
            return redirect()->route('beneficiario.dashboard');
        } elseif ($user && $user->user_type === 'cliente') {
            return redirect()->route('cliente.dashboard');
        }
        
        // Admin users go to metrics dashboard
        return redirect()->route('admin.metrics.dashboard');
    }
}
