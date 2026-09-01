@extends('layout')
@section('content')
<h1>Trust Profile</h1>
<p class="subtitle">{{ $user->name }}</p>

<div class="dashboard-grid">
    <div class="stat-card">
        <span class="stat-label">Overall Trust Score</span>
        <span class="stat-value">{{ $trustScore->score ?? 0 }} / 100</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Capital Access</span>
        <span class="stat-value">{{ ($trustScore->capital_eligible ?? false) ? 'Eligible' : 'Not yet (need ' . $threshold . ')' }}</span>
    </div>
</div>

@if ($trustScore)
<div class="list-item">
    <p><strong>Timeliness:</strong> {{ $trustScore->timeliness_score }}</p>
    <p><strong>Rating consistency:</strong> {{ $trustScore->rating_score }}</p>
    <p><strong>Profile completeness:</strong> {{ $trustScore->completeness_score }}</p>
    <p><strong>Dispute history:</strong> {{ $trustScore->dispute_score }}</p>
</div>
@endif

@if ($trustScore->capital_eligible ?? false)
    <a href="{{ route('capital.create') }}" class="btn-primary">Apply for Capital Access</a>
@endif

<h2>Exchange History</h2>
<div class="list">
    @forelse ($exchanges as $e)
        <div class="list-item">
            <h3>{{ $e->title }}</h3>
            <p class="tag">{{ ucfirst($e->status) }}</p>
            <p>{{ $e->milestones->where('status', 'completed')->count() }} of {{ $e->milestones->count() }} milestones completed</p>
        </div>
    @empty
        <p>No exchanges yet.</p>
    @endforelse
</div>
@endsection