<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClienteDashboardController extends Controller
{
    /**
     * Show the cliente dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user is a cliente
        if ($user->user_type !== 'cliente') {
            abort(403, 'Unauthorized access');
        }
        
        $cliente = $user->cliente;
        
        if (!$cliente) {
            abort(404, 'Cliente not found');
        }
        
        // Get active plan (latest transaction)
        $activePlan = $cliente->active_plan;
        
        // Get all transactions
        $transactions = $cliente->transactions()
            ->orderBy('creation_time', 'desc')
            ->get();
        
        $data = [
            'cliente' => $cliente,
            'active_plan' => $activePlan,
            'transactions' => $transactions,
        ];
        
        return view('dashboard.cliente', $data);
    }

    /**
     * Get cliente dashboard data as JSON (for API/AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $user = $request->user();
        
        if ($user->user_type !== 'cliente') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $cliente = $user->cliente;
        
        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], 404);
        }
        
        $activePlan = $cliente->active_plan;
        $transactions = $cliente->transactions()
            ->orderBy('creation_time', 'desc')
            ->get();
        
        return response()->json([
            'cliente' => [
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'email' => $cliente->email,
            ],
            'active_plan' => $activePlan ? [
                'transaction_id' => $activePlan->transaction_id,
                'status' => $activePlan->status,
                'iccid' => $activePlan->iccid,
                'creation_time' => $activePlan->creation_time,
            ] : null,
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                    'iccid' => $transaction->iccid,
                    'creation_time' => $transaction->creation_time,
                ];
            }),
        ]);
    }
}
