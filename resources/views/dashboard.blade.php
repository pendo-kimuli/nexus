@extends('layout')
@section('content')
<h1>Welcome, {{ $user->name }}</h1>
<p class="subtitle">{{ ucfirst($user->role) }} account</p>

<div class="dashboard-grid">
    <div class="stat-card">
        <span class="stat-label">Trust Score</span>
        <span class="stat-value">{{ $user->trustScore->score ?? 0 }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Value Declarations</span>
        <span class="stat-value">{{ $declarationCount }}</span>
    </div>
</div>

<div class="quick-actions">
    <a href="{{ route('value-declarations.create') }}" class="btn-primary">+ New Value Declaration</a>
    <a href="{{ route('matches') }}" class="btn-secondary">View Matches</a>
    <a href="{{ route('trust-profile') }}" class="btn-secondary">Trust Profile</a>
    @if ($user->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Admin Dashboard</a>
    @endif
    @if ($user->isInvestor())
        <a href="{{ route('investors.index') }}" class="btn-secondary">Browse Eligible Users</a>
    @endif
</div>
@endsection