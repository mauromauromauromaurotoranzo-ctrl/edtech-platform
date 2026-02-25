<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Services\SmartReminderService;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send all due reminders';

    public function handle(SmartReminderService $service): int
    {
        $this->info('Sending due reminders...');
        
        $stats = $service->sendDueReminders();
        
        $this->info("Sent: {$stats['sent']}");
        $this->info("Failed: {$stats['failed']}");
        
        return self::SUCCESS;
    }
}
