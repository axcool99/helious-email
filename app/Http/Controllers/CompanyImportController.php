<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ImportBatch;
use App\Services\CompanyImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyImportController extends Controller
{
    public function __construct(private readonly CompanyImportService $importService)
    {
    }

    public function index(): View
    {
        $sources = collect(config('company_sources'))
            ->map(function (array $config, string $key): array {
                return [
                    'key' => $key,
                    'label' => $config['label'],
                    'start_row' => $config['start_row'],
                    'company_column' => $config['company_column'],
                    'incorporation_date_column' => $config['incorporation_date_column'],
                    'email_column' => $config['email_column'],
                    'companies_count' => Company::query()->sourceType($key)->count(),
                    'last_import' => ImportBatch::query()->where('source_type', $key)->latest()->first(),
                ];
            })
            ->values();

        return view('imports.index', [
            'sources' => $sources,
            'recentImports' => ImportBatch::query()
                ->with('user')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_type' => ['required', 'in:'.implode(',', array_keys(config('company_sources')))],
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
            'confirm_replace' => ['accepted'],
        ], [
            'confirm_replace.accepted' => 'You must confirm that the current source data will be replaced entirely.',
        ]);

        $result = $this->importService->import(
            $validated['file'],
            $validated['source_type'],
            $request->user(),
        );

        return redirect()
            ->route('imports.index')
            ->with('status', sprintf(
                'Imported %d rows for %s. Replaced %d existing rows and skipped %d invalid rows.',
                $result['imported_rows'],
                config("company_sources.{$validated['source_type']}.label"),
                $result['replaced_rows'],
                $result['skipped_rows'],
            ));
    }
}
