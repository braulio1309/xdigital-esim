<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectBasedOnUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirect based on user type
            if ($user->user_type === 'beneficiario') {
                // If trying to access admin dashboard, redirect to beneficiario dashboard
                if ($request->is('admin/dashboard*') || $request->is('admin')) {
                    return redirect()->route('beneficiario.dashboard');
                }
            } elseif ($user->user_type === 'cliente') {
                // If trying to access admin dashboard, redirect to cliente dashboard
                if ($request->is('admin/dashboard*') || $request->is('admin')) {
                    return redirect()->route('cliente.dashboard');
                }
            }
        }
        
        return $next($request);
    }
}
