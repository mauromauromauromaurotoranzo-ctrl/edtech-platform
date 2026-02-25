<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\SpacedRepetitionItem;
use PHPUnit\Framework\TestCase;

class SpacedRepetitionItemTest extends TestCase
{
    public function test_can_create_item(): void
    {
        $item = SpacedRepetitionItem::create(1, 1);

        $this->assertEquals(1, $item->getStudentId());
        $this->assertEquals(1, $item->getContentChunkId());
        $this->assertEquals(2.5, $item->getEasinessFactor());
        $this->assertEquals(0, $item->getRepetitionCount());
    }

    public function test_first_review_sets_interval_to_1_day(): void
    {
        $item = SpacedRepetitionItem::create(1, 1);
        
        $item->review(4); // Good response

        $this->assertEquals(1, $item->getIntervalDays());
        $this->assertEquals(1, $item->getRepetitionCount());
    }

    public function test_second_review_sets_interval_to_6_days(): void
    {
        $item = SpacedRepetitionItem::create(1, 1);
        
        $item->review(4);
        $item->review(4);

        $this->assertEquals(6, $item->getIntervalDays());
        $this->assertEquals(2, $item->getRepetitionCount());
    }

    public function test_failed_review_resets_progress(): void
    {
        $item = SpacedRepetitionItem::create(1, 1);
        
        $item->review(4);
        $item->review(4);
        $item->review(2); // Failed

        $this->assertEquals(1, $item->getIntervalDays());
        $this->assertEquals(0, $item->getRepetitionCount());
    }

    public function test_easiness_factor_decreases_on_failure(): void
    {
        $item = SpacedRepetitionItem::create(1, 1);
        $initialEF = $item->getEasinessFactor();
        
        $item->review(1); // Failed badly

        $this->assertLessThan($initialEF, $item->getEasinessFactor());
    }

    public function test_item_is_due_when_no_next_review(): void
    {
        $item = SpacedRepetitionItem::create(1, 1);
        
        $this->assertTrue($item->isDue());
    }

    public function test_invalid_quality_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $item = SpacedRepetitionItem::create(1, 1);
        $item->review(6); // Invalid quality
    }
}
