<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test for Planes Disponibles public routes
 */
class PlanesDisponiblesTest extends TestCase
{
    /**
     * Test that the planes disponibles index page loads
     *
     * @test
     */
    public function public_users_can_access_planes_disponibles_page()
    {
        $response = $this->get('/planes-disponibles');

        $response->assertStatus(200);
        $response->assertViewIs('planes-disponibles');
    }

    /**
     * Test that the get planes endpoint requires country parameter
     *
     * @test
     */
    public function get_planes_requires_country_parameter()
    {
        $response = $this->postJson('/planes/get-by-country', []);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test that the get planes endpoint validates country values
     *
     * @test
     */
    public function get_planes_validates_country_values()
    {
        $response = $this->postJson('/planes/get-by-country', [
            'country' => 'INVALID'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'País inválido'
        ]);
    }

    /**
     * Test that the get planes endpoint accepts valid countries
     *
     * @test
     */
    public function get_planes_accepts_valid_countries()
    {
        // Test ES
        $response = $this->postJson('/planes/get-by-country', [
            'country' => 'ES'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Test US
        $response = $this->postJson('/planes/get-by-country', [
            'country' => 'US'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * Test that the verificar auth endpoint works
     *
     * @test
     */
    public function verificar_auth_endpoint_works()
    {
        $response = $this->getJson('/planes/verificar-auth');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'authenticated',
            'user'
        ]);
    }

    /**
     * Test that checkout requires authentication
     *
     * @test
     */
    public function checkout_requires_authentication()
    {
        $response = $this->postJson('/planes/checkout', [
            'product_id' => 'test-product-id',
            'product_name' => 'Test Plan',
            'amount' => 10.00,
            'payment_method_id' => 'pm_test_123'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Debe iniciar sesión para continuar'
        ]);
    }
}
