<?php

namespace Tests\Unit\Services;

use App\Services\EsimFxService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EsimFxServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.esimfx.base_url' => 'https://api.esimfx.test']);
        Cache::forget('esimfx_token');
    }

    /** @test */
    public function it_sends_the_v2_product_id_as_a_top_level_field_for_topups(): void
    {
        Http::fake([
            'https://api.esimfx.test/account/api/v1/auth' => Http::response([
                'data' => ['access_token' => 'test-token'],
            ]),
            'https://api.esimfx.test/order/api/v1/create_order' => Http::response([
                'data' => ['id' => 'ORDER-1'],
            ]),
        ]);

        $service = new EsimFxService();
        $service->createOrder('esimfx_100M_1D_CN', 'TOPUP-1', [
            'operation_type' => 'TOPUP',
            'iccid' => 'ICCID-1',
        ]);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.esimfx.test/order/api/v1/create_order'
                && $request->data() === [
                    'transaction_id' => 'TOPUP-1',
                    'count' => 1,
                    'operation_type' => 'TOPUP',
                    'product_id' => 'esimfx_100M_1D_CN',
                    'iccid' => 'ICCID-1',
                ];
        });
    }

    /** @test */
    public function it_keeps_the_existing_product_shape_for_new_esim_orders(): void
    {
        Http::fake([
            'https://api.esimfx.test/account/api/v1/auth' => Http::response([
                'data' => ['access_token' => 'test-token'],
            ]),
            'https://api.esimfx.test/order/api/v1/create_order' => Http::response([
                'data' => ['id' => 'ORDER-2'],
            ]),
        ]);

        $service = new EsimFxService();
        $service->createOrder('esimfx_100M_1D_CN', 'NEW-1');

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.esimfx.test/order/api/v1/create_order'
                && $request->data() === [
                    'transaction_id' => 'NEW-1',
                    'count' => 1,
                    'operation_type' => 'NEW',
                    'product' => ['id' => 'esimfx_100M_1D_CN'],
                ];
        });
    }
}
