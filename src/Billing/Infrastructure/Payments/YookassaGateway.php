<?php

declare(strict_types=1);

namespace Src\Billing\Infrastructure\Payments;

use Illuminate\Support\Facades\Http;
use Src\Billing\Domain\Models\Payment;

class YookassaGateway implements PaymentGatewayInterface
{
    private string $shopId;

    private string $secretKey;

    public function __construct()
    {
        $this->shopId = config('services.yookassa.shop_id', '');
        $this->secretKey = config('services.yookassa.secret_key', '');
    }

    public function createPayment(array $paymentIntent): array
    {
        $response = Http::withBasicAuth($this->shopId, $this->secretKey)
            ->withHeaders(['Idempotence-Key' => uniqid('', true)])
            ->post('https://api.yookassa.ru/v3/payments', [
                'amount' => [
                    'value' => number_format($paymentIntent['amount'] / 100, 2, '.', ''),
                    'currency' => $paymentIntent['currency'] ?? 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $paymentIntent['return_url'],
                ],
                'description' => $paymentIntent['description'] ?? '',
                'capture' => true,
            ]);

        return $response->json();
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['event'] ?? '';
        $paymentData = $payload['object'] ?? [];

        if ($event === 'payment.succeeded') {
            Payment::where('external_id', $paymentData['id'])
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
        }

        if ($event === 'payment.canceled') {
            Payment::where('external_id', $paymentData['id'])
                ->update(['status' => 'cancelled']);
        }
    }

    public function getPaymentStatus(string $externalId): string
    {
        $response = Http::withBasicAuth($this->shopId, $this->secretKey)
            ->get("https://api.yookassa.ru/v3/payments/{$externalId}");

        return $response->json('status', 'unknown');
    }

    public function refundPayment(string $externalId, ?int $amount = null): array
    {
        $body = ['payment_id' => $externalId];

        if ($amount) {
            $body['amount'] = [
                'value' => number_format($amount / 100, 2, '.', ''),
                'currency' => 'RUB',
            ];
        }

        $response = Http::withBasicAuth($this->shopId, $this->secretKey)
            ->withHeaders(['Idempotence-Key' => uniqid('', true)])
            ->post('https://api.yookassa.ru/v3/refunds', $body);

        return $response->json();
    }
}
