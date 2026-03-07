<?php

namespace App\Http\Controllers\App\SuperPartner;

use App\Filters\App\SuperPartner\SuperPartnerFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\SuperPartnerRequest as Request;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use App\Services\App\SuperPartner\SuperPartnerService;

class SuperPartnerController extends Controller
{
    /**
     * SuperPartnerController constructor.
     *
     * @param SuperPartnerService $service
     * @param SuperPartnerFilter $filter
     */
    public function __construct(SuperPartnerService $service, SuperPartnerFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    /**
     * Display a listing of super partners.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created super partner.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->service->save();

        return created_responses('super_partner');
    }

    /**
     * Display the specified super partner.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->find($id)->load('user:id,email,last_name');
    }

    /**
     * Update the specified super partner.
     *
     * @param Request $request
     * @param SuperPartner $super_partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SuperPartner $super_partner)
    {
        $this->service->update($super_partner);

        return updated_responses('super_partner');
    }

    /**
     * Remove the specified super partner.
     *
     * @param SuperPartner $super_partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(SuperPartner $super_partner)
    {
        if ($this->service->delete($super_partner)) {
            return deleted_responses('super_partner');
        }
        return failed_responses();
    }

    /**
     * Export commissions summary for a super partner.
     *
     * @param SuperPartner $super_partner
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCommissions(SuperPartner $super_partner)
    {
        $partnerIds = $super_partner->beneficiarios()->pluck('id');

        $transactions = Transaction::with('beneficiario')
            ->whereIn('beneficiario_id', $partnerIds)
            ->get();

        $rows = $transactions->map(function ($t) {
            return [
                $t->transaction_id,
                $t->plan_name,
                $t->purchase_amount,
                $t->beneficiario ? $t->beneficiario->nombre : 'N/A',
                $t->getCommissionAmount(),
                $t->created_at ? $t->created_at->format('Y-m-d') : '',
            ];
        })->toArray();

        $headings = ['Transaction ID', 'Plan', 'Monto', 'Partner', 'Comisión', 'Fecha'];

        $filename = 'comisiones-super-partner-' . \Str::slug($super_partner->nombre) . '.csv';
        $filepath = storage_path('app/' . $filename);

        $fp = fopen($filepath, 'w');
        fputcsv($fp, $headings);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }
}
