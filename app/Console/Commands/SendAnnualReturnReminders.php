<?php

namespace App\Console\Commands;

use App\Services\AnnualReturnReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendAnnualReturnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-annual-return {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send annual return reminder emails due on the selected date.';

    public function handle(AnnualReturnReminderService $reminderService): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : now()->startOfDay();

        $result = $reminderService->sendDueReminders($date);

        $this->info("Sent {$result['sent']} reminders with {$result['failed']} failures for {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
