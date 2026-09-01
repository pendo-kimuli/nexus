@extends('layout')
@section('content')
<div class="card">
    <h1>NEXUS</h1>
    <p class="subtitle">Sign in</p>
    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    <p class="footer-link">No account? <a href="{{ route('register') }}">Register here</a></p>
</div>
@endsection