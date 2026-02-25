<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Services\DailyChallengeScheduler;
use Illuminate\Console\Command;

class GenerateDailyChallenges extends Command
{
    protected $signature = 'challenges:generate-next-day';
    protected $description = 'Generate challenges for the next day';

    public function handle(DailyChallengeScheduler $scheduler): int
    {
        $this->info('Generating next day challenges...');
        
        $stats = $scheduler->generateNextDayChallenges();
        
        $this->info("Students processed: {$stats['students_processed']}");
        $this->info("Challenges generated: {$stats['challenges_generated']}");
        $this->info("Failed: {$stats['failed']}");
        
        return self::SUCCESS;
    }
}
