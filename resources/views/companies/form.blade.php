@extends('layouts.app', ['title' => $mode === 'create' ? 'Add Company' : 'Edit Company'])

@section('content')
    <section class="panel">
        <div class="section-heading">
            <div>
                <p class="eyebrow">CRUD</p>
                <h2>{{ $mode === 'create' ? 'Add Company Record' : 'Edit Company Record' }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ $mode === 'create' ? route('companies.store') : route('companies.update', $company) }}" class="stack">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif

            <label>
                <span>Source type</span>
                <select name="source_type" required>
                    @foreach ($sourceTypes as $key => $source)
                        <option value="{{ $key }}" @selected(old('source_type', $company->source_type) === $key)>{{ $source['label'] }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Company name</span>
                <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}" required>
            </label>

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" required>
            </label>

            <label>
                <span>Incorporation date</span>
                <input type="date" name="incorporation_date" value="{{ old('incorporation_date', $company->incorporation_date?->toDateString()) }}" required>
            </label>

            <label class="checkbox">
                <input type="checkbox" name="active" value="1" @checked(old('active', $company->active))>
                <span>Reminder active</span>
            </label>

            <div class="actions">
                <a class="button button-secondary" href="{{ route('companies.index') }}">Cancel</a>
                <button type="submit" class="button">{{ $mode === 'create' ? 'Create' : 'Save Changes' }}</button>
            </div>
        </form>
    </section>
@endsection
