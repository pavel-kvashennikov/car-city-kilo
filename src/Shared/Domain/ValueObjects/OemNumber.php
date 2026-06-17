<?php

namespace Src\Shared\Domain\ValueObjects;

final class OemNumber
{
    public function __construct(private readonly string $value) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function normalize(): string
    {
        return strtoupper(preg_replace('/[\s\-_.]/', '', $this->value));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
