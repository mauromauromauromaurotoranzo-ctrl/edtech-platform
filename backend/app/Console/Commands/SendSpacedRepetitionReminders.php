<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Services\SpacedRepetitionService;
use Illuminate\Console\Command;

class SendSpacedRepetitionReminders extends Command
{
    protected $signature = 'sr:send-reminders';
    protected $description = 'Send spaced repetition review reminders';

    public function handle(SpacedRepetitionService $service): int
    {
        $this->info('Sending SR reminders...');
        
        $stats = $service->sendReminders();
        
        $this->info("Sent: {$stats['sent']}");
        $this->info("Total due: {$stats['total_due']}");
        
        return self::SUCCESS;
    }
}
