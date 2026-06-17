<?php

declare(strict_types=1);

namespace Src\Billing\Infrastructure\Payments;

interface PaymentGatewayInterface
{
    public function createPayment(array $paymentIntent): array;

    public function handleWebhook(array $payload): void;

    public function getPaymentStatus(string $externalId): string;

    public function refundPayment(string $externalId, ?int $amount = null): array;
}
