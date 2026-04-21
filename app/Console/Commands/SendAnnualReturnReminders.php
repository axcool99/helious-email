<?php

namespace App\Console\Commands;

use App\Services\AnnualReturnReminderService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('reminders:send-annual-return {--date=}')]
#[Description('Send annual return reminder emails due on the selected date.')]
class SendAnnualReturnReminders extends Command
{
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
