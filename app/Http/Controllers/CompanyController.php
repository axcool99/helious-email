<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Company::query()->latest();
        $sourceType = $request->string('source_type')->toString();
        $search = $request->string('search')->toString();

        if ($sourceType !== '') {
            $query->where('source_type', $sourceType);
        }

        if ($search !== '') {
            $query->where('company_name', 'like', "%{$search}%");
        }

        return view('companies.index', [
            'companies' => $query->paginate(20)->withQueryString(),
            'sourceTypes' => config('company_sources'),
            'filters' => [
                'source_type' => $sourceType,
                'search' => $search,
            ],
        ]);
    }

    public function create(): View
    {
        return view('companies.form', [
            'company' => new Company(['active' => true]),
            'sourceTypes' => config('company_sources'),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Company::query()->create($this->validatedData($request));

        return redirect()->route('companies.index')->with('status', 'Company record created.');
    }

    public function edit(Company $company): View
    {
        return view('companies.form', [
            'company' => $company,
            'sourceTypes' => config('company_sources'),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $company->update($this->validatedData($request));

        return redirect()->route('companies.index')->with('status', 'Company record updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('companies.index')->with('status', 'Company record deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'source_type' => ['required', 'in:'.implode(',', array_keys(config('company_sources')))],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'incorporation_date' => ['required', 'date'],
            'active' => ['nullable', 'boolean'],
        ]) + [
            'active' => $request->boolean('active'),
        ];
    }
}
