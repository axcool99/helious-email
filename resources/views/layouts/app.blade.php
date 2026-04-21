<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Helious Annual Return Reminder' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
</head>
<body>
    <div class="page-glow page-glow-one"></div>
    <div class="page-glow page-glow-two"></div>

    <div class="shell">
        <aside class="sidebar">
            <div class="brand-block">
                <div class="brand-mark">HR</div>
                <div>
                    <p class="eyebrow">Admin Portal</p>
                    <h1>Helious</h1>
                </div>
            </div>

            @auth
                <nav class="nav">
                    <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('reports.reminders.index') }}" @class(['active' => request()->routeIs('reports.reminders.*')])>
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        Reminder Report
                    </a>
                    <a href="{{ route('companies.index') }}" @class(['active' => request()->routeIs('companies.*')])>
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        Companies
                    </a>
                    <a href="{{ route('imports.index') }}" @class(['active' => request()->routeIs('imports.*')])>
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Imports
                    </a>
                </nav>
            @endauth

            <div class="sidebar-footer">
                @auth
                    <div class="sidebar-user">
                        <span class="user-dot"></span>
                        <div>
                            <strong>{{ auth()->user()->name }}</strong>
                            <p>{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button button-secondary sidebar-button" type="submit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Log Out
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Helious Workspace</p>
                    <h2>{{ $title ?? 'Dashboard' }}</h2>
                </div>
            </header>

            @if (session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash error">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
