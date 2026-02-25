<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Services\DailyChallengeScheduler;
use Illuminate\Console\Command;

class SendDailyChallenges extends Command
{
    protected $signature = 'challenges:send-daily';
    protected $description = 'Send daily challenges to all students';

    public function handle(DailyChallengeScheduler $scheduler): int
    {
        $this->info('Processing daily challenges...');
        
        $stats = $scheduler->processDailyChallenges();
        
        $this->info("Processed: {$stats['processed']}");
        $this->info("Sent: {$stats['sent']}");
        $this->info("Failed: {$stats['failed']}");
        
        return self::SUCCESS;
    }
}
