<?php

namespace Src\Shared\Domain\ValueObjects;

final class PhoneNumber
{
    public function __construct(private readonly string $value)
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $value);
        if (! preg_match('/^\+?[0-9]{10,15}$/', $cleaned)) {
            throw new \InvalidArgumentException("Invalid phone number: {$value}");
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function normalized(): string
    {
        return preg_replace('/[^0-9+]/', '', $this->value);
    }

    public function masked(): string
    {
        $normalized = $this->normalized();
        $len = strlen($normalized);

        if ($len <= 4) {
            return $normalized;
        }

        return substr($normalized, 0, 4).str_repeat('*', $len - 6).substr($normalized, -2);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
