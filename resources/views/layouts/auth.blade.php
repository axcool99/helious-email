<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Helious — Admin Login' }}</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
</head>
<body>
    <div class="page-glow page-glow-one"></div>
    <div class="page-glow page-glow-two"></div>

    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="brand-mark">HR</div>
                <div class="auth-brand-text">
                    <h1>Helious Reminder</h1>
                    <p>Admin Portal</p>
                </div>
            </div>

            @if (session('status'))
                <div class="flash success" style="margin-bottom:18px">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash error" style="margin-bottom:18px">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
