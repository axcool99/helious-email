<?php

namespace App\Services;

use App\Mail\AnnualReturnReminderMail;
use App\Models\Company;
use App\Models\ReminderLog;
use App\Models\User;
use App\Support\AnnualReturnReminderDate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AnnualReturnReminderService
{
    public function sendDueReminders(?Carbon $runDate = null, ?User $triggeredBy = null): array
    {
        $runDate ??= now()->startOfDay();
        $subject = 'Annual Return Reminder';

        $companies = Company::query()
            ->where('active', true)
            ->whereDate('next_reminder_on', '<=', $runDate->toDateString())
            ->orderBy('next_reminder_on')
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($companies as $company) {
            try {
                Mail::to($company->email)->send(new AnnualReturnReminderMail(
                    mailSubject: $subject,
                    body: $this->buildBody($company),
                ));

                $company->forceFill([
                    'last_reminder_sent_on' => $runDate->toDateString(),
                    'next_reminder_on' => AnnualReturnReminderDate::nextReminderDate(
                        $company->incorporation_date,
                        $runDate->copy()->addDay(),
                    )->toDateString(),
                ])->save();

                ReminderLog::query()->create([
                    'company_id' => $company->id,
                    'source_type' => $company->source_type,
                    'company_name_snapshot' => $company->company_name,
                    'recipient_email' => $company->email,
                    'subject' => $subject,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $sent++;
            } catch (\Throwable $exception) {
                ReminderLog::query()->create([
                    'company_id' => $company->id,
                    'source_type' => $company->source_type,
                    'company_name_snapshot' => $company->company_name,
                    'recipient_email' => $company->email,
                    'subject' => $subject,
                    'status' => 'failed',
                    'sent_at' => now(),
                    'error_message' => $exception->getMessage(),
                ]);

                $failed++;
            }
        }

        return [
            'date' => $runDate,
            'sent' => $sent,
            'failed' => $failed,
            'triggered_by' => $triggeredBy?->email,
        ];
    }

    public function buildBody(Company $company): string
    {
        $template = file_get_contents(resource_path('templates/annual_return_reminder.txt'));
        $companyName = html_entity_decode($company->company_name, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return str_replace(
            ['<COMPANY NAME>', '<DD/MM/YYYY'],
            [
                Str::of($companyName)->squish()->value(),
                $company->incorporation_date->format('d/m/Y'),
            ],
            $template,
        );
    }
}
