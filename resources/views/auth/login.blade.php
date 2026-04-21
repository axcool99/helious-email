@extends('layouts.auth', ['title' => 'Admin Login'])

@section('content')
    <div class="auth-heading">
        <h2>Sign in to your workspace</h2>
        <p>Enter your admin credentials to continue.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="stack">
        @csrf
        <label>
            <span>Email address</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
        </label>

        <label>
            <span>Password</span>
            <input type="password" name="password" required placeholder="••••••••">
        </label>

        <label class="checkbox">
            <input type="checkbox" name="remember" value="1">
            <span>Keep me signed in on this device</span>
        </label>

        <button type="submit" class="button" style="width:100%;margin-top:4px">Sign In</button>
    </form>
@endsection
