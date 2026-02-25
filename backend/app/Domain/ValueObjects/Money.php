<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Money
{
    private int $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'USD')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative");
        }
        $this->amount = (int) round($amount * 100);
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->amount / 100;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): Money
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException("Cannot add different currencies");
        }
        return new Money(($this->amount + $other->amount) / 100, $this->currency);
    }

    public function subtract(Money $other): Money
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException("Cannot subtract different currencies");
        }
        return new Money(($this->amount - $other->amount) / 100, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return number_format($this->getAmount(), 2) . ' ' . $this->currency;
    }
}
