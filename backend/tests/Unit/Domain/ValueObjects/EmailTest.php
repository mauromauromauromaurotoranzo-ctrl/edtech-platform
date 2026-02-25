<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_can_create_valid_email(): void
    {
        $email = Email::create('test@example.com');
        
        $this->assertEquals('test@example.com', $email->getValue());
    }

    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        Email::create('invalid-email');
    }

    public function test_emails_are_equal(): void
    {
        $email1 = Email::create('test@example.com');
        $email2 = Email::create('test@example.com');
        
        $this->assertTrue($email1->equals($email2));
    }

    public function test_emails_are_not_equal(): void
    {
        $email1 = Email::create('test1@example.com');
        $email2 = Email::create('test2@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }
}
