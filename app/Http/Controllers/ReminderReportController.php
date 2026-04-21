<?php

namespace App\Http\Controllers;

use App\Models\ReminderLog;
use App\Services\AnnualReturnReminderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderReportController extends Controller
{
    public function __construct(private readonly AnnualReturnReminderService $reminderService)
    {
    }

    public function index(Request $request): View
    {
        $query = ReminderLog::query()->latest('sent_at');
        $status = $request->string('status')->toString();
        $sourceType = $request->string('source_type')->toString();

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($sourceType !== '') {
            $query->where('source_type', $sourceType);
        }

        return view('reports.reminders.index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'filters' => [
                'status' => $status,
                'source_type' => $sourceType,
            ],
            'sourceTypes' => config('company_sources'),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        $result = $this->reminderService->sendDueReminders(now()->startOfDay(), $request->user());

        return redirect()
            ->route('reports.reminders.index')
            ->with('status', "Reminder run completed. Sent {$result['sent']} emails and logged {$result['failed']} failures.");
    }
}
