<?php

namespace Src\Shared\Domain\ValueObjects;

final class Money
{
    public function __construct(
        private readonly int $amount,
        private readonly string $currency = 'RUB'
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public static function fromRubles(float $rubles): self
    {
        return new self((int) round($rubles * 100));
    }

    public static function fromKopecks(int $kopecks): self
    {
        return new self($kopecks);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function toRubles(): float
    {
        return $this->amount / 100;
    }

    public function format(): string
    {
        return number_format($this->toRubles(), 0, '.', ' ').' ₽';
    }

    public function add(Money $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }
}
