<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ReminderLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->startOfDay();

        return view('dashboard', [
            'totals' => [
                'companies' => Company::query()->count(),
                'due_today' => Company::query()->whereDate('next_reminder_on', '<=', $today->toDateString())->where('active', true)->count(),
                'upcoming_30_days' => Company::query()->whereBetween('next_reminder_on', [$today->toDateString(), $today->copy()->addDays(30)->toDateString()])->where('active', true)->count(),
                'sent_this_month' => ReminderLog::query()->where('status', 'sent')->whereBetween('sent_at', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])->count(),
            ],
            'dueCompanies' => Company::query()
                ->where('active', true)
                ->whereDate('next_reminder_on', '<=', $today->toDateString())
                ->orderBy('next_reminder_on')
                ->limit(10)
                ->get(),
            'recentLogs' => ReminderLog::query()->latest('sent_at')->limit(10)->get(),
        ]);
    }
}
