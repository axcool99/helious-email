@extends('layouts.app', ['title' => 'Imports'])

@section('content')
    <section class="section-heading">
        <div>
            <p class="eyebrow">Excel Imports</p>
            <h2>Replace Source Data Securely</h2>
            <p class="muted" style="margin:6px 0 0">Uploaded spreadsheets are parsed in memory and not retained after import. Re-uploading a source replaces every existing record for that source type.</p>
        </div>
    </section>

    <section class="grid two">
        @foreach ($sources as $source)
            <article class="panel">
                <div class="section-heading" style="margin-bottom:16px">
                    <div>
                        <p class="eyebrow">{{ $source['eyebrow'] ?? $source['key'] }}</p>
                        <h3>{{ $source['label'] }}</h3>
                    </div>
                    <span class="pill">{{ $source['companies_count'] }} active rows</span>
                </div>

                <ul class="mapping-list">
                    <li>Start row: <strong style="color:var(--text-secondary)">{{ $source['start_row'] }}</strong></li>
                    <li>Company name: column <strong style="color:var(--text-secondary)">{{ $source['company_column'] }}</strong></li>
                    <li>Incorporation date: column <strong style="color:var(--text-secondary)">{{ $source['incorporation_date_column'] }}</strong></li>
                    <li>Email: column <strong style="color:var(--text-secondary)">{{ $source['email_column'] }}</strong></li>
                </ul>

                <div class="warning-banner" style="margin-top:16px">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    Uploading data again will replace all current data for this source entirely.
                </div>

                @if ($source['last_import'])
                    <p class="muted" style="font-size:12px;margin-top:10px">
                        Last import: {{ $source['last_import']->created_at->format('d M Y H:i') }}
                        by {{ $source['last_import']->user?->email ?? 'system' }}
                    </p>
                @endif

                <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data" class="stack" style="margin-top:20px">
                    @csrf
                    <input type="hidden" name="source_type" value="{{ $source['key'] }}">

                    <label>
                        <span>Excel file (.xlsx)</span>
                        <input type="file" name="file" accept=".xlsx" required>
                    </label>

                    <label class="checkbox">
                        <input type="checkbox" name="confirm_replace" value="1" required>
                        <span>I understand this upload will replace all current {{ $source['label'] }} records.</span>
                    </label>

                    <button type="submit" class="button">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Upload and Replace
                    </button>
                </form>
            </article>
        @endforeach
    </section>

    <section class="panel">
        <div class="section-heading" style="margin-bottom:16px">
            <div>
                <p class="eyebrow">Import Activity</p>
                <h3>Recent Upload Batches</h3>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Imported</th>
                    <th>Skipped</th>
                    <th>Replaced</th>
                    <th>Uploaded By</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentImports as $batch)
                    <tr>
                        <td>{{ config("company_sources.{$batch->source_type}.label") }}</td>
                        <td>{{ $batch->imported_rows }}</td>
                        <td>{{ $batch->skipped_rows }}</td>
                        <td>{{ $batch->replaced_rows }}</td>
                        <td>{{ $batch->user?->email ?? 'system' }}</td>
                        <td>{{ $batch->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="color:var(--muted);text-align:center;padding:28px">No imports yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
