@extends('layouts.app', ['title' => 'Companies'])

@section('content')
    <section class="section-heading">
        <div>
            <p class="eyebrow">Records</p>
            <h2>Imported Company Data</h2>
        </div>
        <div class="actions">
            <a class="button button-secondary" href="{{ route('imports.index') }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Import Excel
            </a>
            <a class="button" href="{{ route('companies.create') }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Company
            </a>
        </div>
    </section>

    <section class="panel">
        <form method="GET" class="filters">
            <label>
                <span>Source type</span>
                <select name="source_type">
                    <option value="">All sources</option>
                    @foreach ($sourceTypes as $key => $source)
                        <option value="{{ $key }}" @selected($filters['source_type'] === $key)>{{ $source['label'] }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Company name</span>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search company name…">
            </label>

            <button type="submit" class="button">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Filter
            </button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Source</th>
                    <th>Email</th>
                    <th>Incorporation</th>
                    <th>Next Reminder</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($companies as $company)
                    <tr>
                        <td>{{ $company->company_name }}</td>
                        <td>{{ $company->sourceLabel() }}</td>
                        <td style="color:var(--muted)">{{ $company->email }}</td>
                        <td>{{ $company->incorporation_date->format('d/m/Y') }}</td>
                        <td>{{ $company->next_reminder_on?->format('d/m/Y') }}</td>
                        <td>
                            @if ($company->active)
                                <span class="badge active">Active</span>
                            @else
                                <span class="badge paused">Paused</span>
                            @endif
                        </td>
                        <td class="actions-inline">
                            <a href="{{ route('companies.edit', $company) }}" class="button button-secondary" style="padding:6px 12px;font-size:12px;border-radius:10px">Edit</a>
                            <form method="POST" action="{{ route('companies.destroy', $company) }}" onsubmit="return confirm('Delete this company record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="color:var(--muted);text-align:center;padding:36px">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:16px">
            {{ $companies->links() }}
        </div>
    </section>
@endsection
