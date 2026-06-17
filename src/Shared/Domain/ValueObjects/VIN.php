<?php

namespace Src\Shared\Domain\ValueObjects;

final class VIN
{
    public function __construct(private readonly string $value)
    {
        if (! preg_match('/^[A-HJ-NPR-Z0-9]{17}$/i', $value)) {
            throw new \InvalidArgumentException("Invalid VIN: {$value}");
        }
    }

    public function getValue(): string
    {
        return strtoupper($this->value);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
