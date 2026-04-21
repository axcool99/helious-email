@extends('layouts.app', ['title' => 'Reminder Report'])

@section('content')
    <section class="section-heading">
        <div>
            <p class="eyebrow">Reporting</p>
            <h2>Email Trigger Report</h2>
            <p class="muted">This report shows when reminder emails were triggered, to whom, and whether the SMTP send succeeded.</p>
        </div>
        <form method="POST" action="{{ route('reports.reminders.run') }}">
            @csrf
            <button type="submit" class="button">Run Due Reminders Now</button>
        </form>
    </section>

    <section class="panel">
        <form method="GET" class="filters">
            <label>
                <span>Status</span>
                <select name="status">
                    <option value="">All</option>
                    <option value="sent" @selected($filters['status'] === 'sent')>Sent</option>
                    <option value="failed" @selected($filters['status'] === 'failed')>Failed</option>
                </select>
            </label>

            <label>
                <span>Source type</span>
                <select name="source_type">
                    <option value="">All</option>
                    @foreach ($sourceTypes as $key => $source)
                        <option value="{{ $key }}" @selected($filters['source_type'] === $key)>{{ $source['label'] }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="button">Filter</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Triggered At</th>
                    <th>Company</th>
                    <th>Recipient</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Error</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->sent_at?->format('d M Y H:i') }}</td>
                        <td>{{ $log->company_name_snapshot }}</td>
                        <td>{{ $log->recipient_email }}</td>
                        <td>{{ config("company_sources.{$log->source_type}.label") }}</td>
                        <td><span class="badge {{ $log->status }}">{{ strtoupper($log->status) }}</span></td>
                        <td>{{ $log->error_message ?: 'None' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No reminder logs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $logs->links() }}
    </section>
@endsection
