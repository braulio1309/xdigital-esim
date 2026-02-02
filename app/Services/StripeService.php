<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar pagos con Stripe
 * Documentación: https://stripe.com/docs/api
 */
class StripeService
{
    protected $secretKey;
    protected $publishableKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        $this->publishableKey = config('services.stripe.key');
        
        // Configura la API key de Stripe
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Obtener la clave pública para el frontend
     * @return string
     */
    public function getPublishableKey()
    {
        return $this->publishableKey;
    }

    /**
     * Crear un Payment Intent
     * Un Payment Intent representa la intención de cobrar a un cliente
     * 
     * @param float $amount Monto en unidades menores (centavos para USD)
     * @param string $currency Código de moneda (usd, eur, etc.)
     * @param array $metadata Datos adicionales del pago
     * @return array
     * @throws Exception
     */
    public function createPaymentIntent($amount, $currency = 'usd', $metadata = [])
    {
        try {
            // Convertir el monto a centavos si está en dólares
            $amountInCents = intval($amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => strtolower($currency),
                'metadata' => $metadata,
                // Habilitar métodos de pago automáticos
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (Exception $e) {
            Log::error('Error creando Payment Intent en Stripe: ' . $e->getMessage());
            throw new Exception('Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar un Payment Intent existente
     * Útil cuando necesitas confirmar el pago después de capturar el método de pago
     * 
     * @param string $paymentIntentId ID del Payment Intent
     * @return array
     * @throws Exception
     */
    public function confirmPayment($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->status === 'requires_confirmation') {
                $paymentIntent->confirm();
            }

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'payment_intent' => $paymentIntent,
            ];
        } catch (Exception $e) {
            Log::error('Error confirmando Payment Intent en Stripe: ' . $e->getMessage());
            throw new Exception('Error al confirmar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el estado de un Payment Intent
     * 
     * @param string $paymentIntentId ID del Payment Intent
     * @return array
     * @throws Exception
     */
    public function getPaymentStatus($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100, // Convertir centavos a unidad principal
                'currency' => strtoupper($paymentIntent->currency),
                'payment_intent' => $paymentIntent,
            ];
        } catch (Exception $e) {
            Log::error('Error obteniendo estado de pago en Stripe: ' . $e->getMessage());
            throw new Exception('Error al obtener el estado del pago: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar un Payment Intent
     * 
     * @param string $paymentIntentId ID del Payment Intent
     * @return array
     * @throws Exception
     */
    public function cancelPayment($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $canceledIntent = $paymentIntent->cancel();

            return [
                'success' => true,
                'status' => $canceledIntent->status,
            ];
        } catch (Exception $e) {
            Log::error('Error cancelando Payment Intent en Stripe: ' . $e->getMessage());
            throw new Exception('Error al cancelar el pago: ' . $e->getMessage());
        }
    }
}
