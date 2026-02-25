<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Currency;
use App\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_can_create_money(): void
    {
        $money = Money::create(100.50, Currency::USD);
        
        $this->assertEquals(100.50, $money->getAmount());
        $this->assertEquals(Currency::USD, $money->getCurrency());
    }

    public function test_cannot_create_negative_amount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        Money::create(-10, Currency::USD);
    }

    public function test_can_add_money_same_currency(): void
    {
        $money1 = Money::create(100, Currency::USD);
        $money2 = Money::create(50, Currency::USD);
        
        $result = $money1->add($money2);
        
        $this->assertEquals(150, $result->getAmount());
    }

    public function test_cannot_add_different_currencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $money1 = Money::create(100, Currency::USD);
        $money2 = Money::create(50, Currency::EUR);
        
        $money1->add($money2);
    }

    public function test_formatting(): void
    {
        $money = Money::create(99.99, Currency::USD);
        
        $this->assertEquals('$99.99', $money->format());
    }
}
