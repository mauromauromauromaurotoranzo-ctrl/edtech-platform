<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private int $amount; // Stored in cents to avoid floating point issues
    private string $currency;

    public function __construct(int $amount, string $currency = 'USD')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }
        
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be a 3-letter ISO code');
        }
        
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public static function fromDecimal(float $amount, string $currency = 'USD'): self
    {
        $cents = (int) round($amount * 100);
        return new self($cents, $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getAmountInDecimal(): float
    {
        return $this->amount / 100;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->getAmount(), $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->getAmount(), $this->currency);
    }

    public function multiply(float $factor): self
    {
        $newAmount = (int) round($this->amount * $factor);
        return new self($newAmount, $this->currency);
    }

    public function percentage(float $percent): self
    {
        return $this->multiply($percent / 100);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->getAmount() 
            && $this->currency === $other->getCurrency();
    }

    public function greaterThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount > $other->getAmount();
    }

    public function lessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount < $other->getAmount();
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function format(): string
    {
        $symbol = match($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency . ' ',
        };
        
        return $symbol . number_format($this->getAmountInDecimal(), 2);
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException(
                "Cannot operate on different currencies: {$this->currency} and {$other->getCurrency()}"
            );
        }
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
