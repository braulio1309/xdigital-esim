<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeneficiarioDashboardController extends Controller
{
    /**
     * Show the beneficiario dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user is a beneficiario
        if ($user->user_type !== 'beneficiario') {
            abort(403, 'Unauthorized access');
        }
        
        $beneficiario = $user->beneficiario;
        
        if (!$beneficiario) {
            abort(404, 'Beneficiario not found');
        }
        
        // Get dashboard data
        $data = [
            'beneficiario' => $beneficiario,
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => $beneficiario->total_earnings ?? 0,
            'total_sales' => $beneficiario->total_sales ?? 0,
        ];
        
        return view('dashboard.beneficiario', $data);
    }

    /**
     * Get beneficiario dashboard data as JSON (for API/AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $user = $request->user();
        
        if ($user->user_type !== 'beneficiario') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $beneficiario = $user->beneficiario;
        
        if (!$beneficiario) {
            return response()->json(['error' => 'Beneficiario not found'], 404);
        }
        
        return response()->json([
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => number_format($beneficiario->total_earnings ?? 0, 2),
            'total_sales' => $beneficiario->total_sales ?? 0,
            'nombre' => $beneficiario->nombre,
            'descripcion' => $beneficiario->descripcion,
        ]);
    }
}
