<?php

namespace Tests\Feature;

use App\Mail\AnnualReturnReminderMail;
use App\Models\Company;
use App\Services\AnnualReturnReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendAnnualReturnRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_due_reminders_and_updates_next_cycle(): void
    {
        Mail::fake();

        $company = Company::query()->create([
            'source_type' => 'stacia',
            'company_name' => 'Reminder Test Sdn Bhd',
            'email' => 'client@example.com',
            'incorporation_date' => '2024-05-31',
            'active' => true,
        ]);

        $company->update([
            'next_reminder_on' => '2026-05-01',
        ]);

        $result = app(AnnualReturnReminderService::class)
            ->sendDueReminders(Carbon::parse('2026-05-01')->startOfDay());

        Mail::assertSent(AnnualReturnReminderMail::class, 1);
        $company->refresh();

        $this->assertSame(1, $result['sent']);
        $this->assertSame('2026-05-01', $company->last_reminder_sent_on?->toDateString());
        $this->assertSame('2027-05-01', $company->next_reminder_on?->toDateString());
    }

    public function test_email_body_decodes_company_entities_and_formats_subtitles(): void
    {
        $company = Company::query()->create([
            'source_type' => 'stacia',
            'company_name' => 'AUNTEA F&amp;B (BSD) SDN BHD',
            'email' => 'client@example.com',
            'incorporation_date' => '2024-06-04',
            'active' => true,
        ]);

        $body = app(AnnualReturnReminderService::class)->buildBody($company);
        $html = Blade::render('emails.annual_return_reminder', [
            'body' => $body,
            'mailSubject' => 'Annual Return Reminder',
        ]);

        $this->assertStringContainsString('RE : AUNTEA F&amp;B (BSD) SDN BHD', $html);
        $this->assertStringContainsString('Anniversary Date: 04/06/2024', $html);
        $this->assertStringNotContainsString('_Duty to lodge BO Information with Annual Return_', $html);
        $this->assertStringContainsString('font-weight: 700;">', $html);
        $this->assertStringContainsString('Duty to lodge BO Information with Annual Return', $html);
    }
}
