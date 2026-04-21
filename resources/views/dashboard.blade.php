@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <section class="section-heading">
        <div>
            <p class="eyebrow">Snapshot</p>
            <h3>Current Metrics</h3>
        </div>
    </section>

    <section class="cards">
        <article class="card">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <span>Total Companies</span>
            <strong>{{ number_format($totals['companies']) }}</strong>
        </article>
        <article class="card">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <span>Due Today</span>
            <strong>{{ number_format($totals['due_today']) }}</strong>
        </article>
        <article class="card">
            <div class="card-icon" style="background:var(--accent-dim)">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="var(--accent)" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <span>Next 30 Days</span>
            <strong>{{ number_format($totals['upcoming_30_days']) }}</strong>
        </article>
        <article class="card">
            <div class="card-icon" style="background:rgba(245,197,66,.12)">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="var(--warning)" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </div>
            <span>Sent This Month</span>
            <strong>{{ number_format($totals['sent_this_month']) }}</strong>
        </article>
    </section>

    <section class="panel">
        <div class="section-heading" style="margin-bottom:16px">
            <div>
                <p class="eyebrow">Due Queue</p>
                <h3>Companies Requiring Reminder</h3>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Source</th>
                    <th>Next Reminder</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dueCompanies as $company)
                    <tr>
                        <td>{{ $company->company_name }}</td>
                        <td>{{ $company->sourceLabel() }}</td>
                        <td>{{ $company->next_reminder_on?->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="color:var(--muted);text-align:center;padding:28px">No reminders due today.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel">
        <div class="section-heading" style="margin-bottom:16px">
            <div>
                <p class="eyebrow">Delivery Report</p>
                <h3>Recent Reminder Activity</h3>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Sent At</th>
                    <th>Company</th>
                    <th>Recipient</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentLogs as $log)
                    <tr>
                        <td>{{ $log->sent_at?->format('d M Y H:i') }}</td>
                        <td>{{ $log->company_name_snapshot }}</td>
                        <td>{{ $log->recipient_email }}</td>
                        <td><span class="badge {{ $log->status }}">{{ strtoupper($log->status) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="color:var(--muted);text-align:center;padding:28px">No reminder activity yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
