<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\DailyChallenge;
use App\Domain\ValueObjects\ChallengeType;
use PHPUnit\Framework\TestCase;

class DailyChallengeTest extends TestCase
{
    public function test_can_create_challenge(): void
    {
        $challenge = DailyChallenge::create(
            studentId: 1,
            knowledgeBaseId: 1,
            type: ChallengeType::QUIZ,
            title: 'Test Challenge',
            content: 'What is 2+2?',
            correctAnswer: '4',
            options: ['3', '4', '5'],
            explanation: 'Basic math',
            points: 10,
        );

        $this->assertEquals('Test Challenge', $challenge->getTitle());
        $this->assertEquals(ChallengeType::QUIZ, $challenge->getType());
        $this->assertTrue($challenge->isPending());
    }

    public function test_can_submit_correct_answer(): void
    {
        $challenge = DailyChallenge::create(
            studentId: 1,
            knowledgeBaseId: 1,
            type: ChallengeType::QUIZ,
            title: 'Test',
            content: 'Question?',
            correctAnswer: 'correct',
            points: 10,
        );

        $challenge->submitAnswer('correct');

        $this->assertTrue($challenge->isAnswered());
        $this->assertTrue($challenge->isCorrect());
        $this->assertEquals(10, $challenge->getPointsEarned());
    }

    public function test_can_submit_incorrect_answer(): void
    {
        $challenge = DailyChallenge::create(
            studentId: 1,
            knowledgeBaseId: 1,
            type: ChallengeType::QUIZ,
            title: 'Test',
            content: 'Question?',
            correctAnswer: 'correct',
            points: 10,
        );

        $challenge->submitAnswer('wrong');

        $this->assertTrue($challenge->isAnswered());
        $this->assertFalse($challenge->isCorrect());
        $this->assertEquals(0, $challenge->getPointsEarned());
    }
}
