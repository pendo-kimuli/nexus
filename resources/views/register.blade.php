@extends('layout')
@section('content')
<div class="card">
    <h1>NEXUS</h1>
    <p class="subtitle">Create an account</p>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="field">
            <label>Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
            <label>Phone Number</label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="e.g. 0712345678" required>
        </div>
        <div class="field">
            <label>I am a...</label>
            <select name="role" required>
                <option value="individual">Individual User (skills/services)</option>
                <option value="investor">Verified Investor / Lender</option>
            </select>
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="field">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn-primary">Register</button>
    </form>
    <p class="footer-link">Already have an account? <a href="{{ route('login') }}">Login</a></p>
</div>
@endsection