@extends('layout')
@section('content')
<h1>{{ $exchange->title }}</h1>
<p class="tag">{{ ucfirst($exchange->status) }}</p>
<p><strong>Initiator:</strong> {{ $exchange->initiator->name }} &nbsp; <strong>Counterpart:</strong> {{ $exchange->counterpart->name }}</p>
<p><strong>Contract terms:</strong> {{ $exchange->contract_terms }}</p>

@if ($exchange->status === 'pending')
    @if (auth()->id() === $exchange->counterpart_id)
        <div class="card">
            <p>{{ $exchange->initiator->name }} has proposed this exchange. Do you accept?</p>
            <form method="POST" action="{{ route('exchanges.accept', $exchange) }}" style="display:inline">
                @csrf
                <button type="submit" class="btn-primary">Accept</button>
            </form>
            <form method="POST" action="{{ route('exchanges.decline', $exchange) }}" style="display:inline">
                @csrf
                <button type="submit" class="btn-secondary">Decline</button>
            </form>
        </div>
    @else
        <p><em>Waiting for {{ $exchange->counterpart->name }} to accept this exchange.</em></p>
    @endif
@elseif ($exchange->status === 'disputed')
    <div class="alert alert-error">
        <p><strong>This exchange is disputed.</strong></p>
        <p>{{ $exchange->dispute_reason }}</p>
    </div>
@elseif ($exchange->status === 'declined')
    <p><em>This exchange was declined.</em></p>
@elseif ($exchange->status === 'completed')
    <div class="alert alert-success"><p>All milestones on this exchange are complete.</p></div>
@endif

@if (in_array($exchange->status, ['active', 'completed']))
<h2>Milestones</h2>
<div class="list">
    @forelse ($exchange->milestones as $m)
        <div class="list-item">
            <h3>{{ $m->title }}</h3>
            @if ($m->description)<p>{{ $m->description }}</p>@endif
            @if ($m->due_date)<p>Due: {{ $m->due_date->format('d M Y') }}</p>@endif
            <p class="tag">{{ $m->status === 'completed' ? 'Completed' : 'Pending confirmation' }}</p>

            @php
                $isInitiator = auth()->id() === $exchange->initiator_id;
                $myConfirmed = $isInitiator ? $m->initiator_confirmed_at : $m->counterpart_confirmed_at;
            @endphp

            @if ($m->status !== 'completed' && $exchange->status === 'active')
                @if ($myConfirmed)
                    <p><em>You've confirmed this milestone. Waiting on the other party.</em></p>
                @else
                    <form method="POST" action="{{ route('milestones.confirm', $m) }}">
                        @csrf
                        <div class="field">
                            <label>Rate the other party's delivery on this milestone (1-5)</label>
                            <select name="rating" required>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Good</option>
                                <option value="3">3 - Fair</option>
                                <option value="2">2 - Poor</option>
                                <option value="1">1 - Very poor</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Confirm Delivery</button>
                    </form>
                @endif
            @endif
        </div>
    @empty
        <p>No milestones yet.</p>
    @endforelse
</div>
@endif

@if ($exchange->status === 'active')
<h2>Add a Milestone</h2>
<form method="POST" action="{{ route('milestones.store', $exchange) }}" class="card">
    @csrf
    <div class="field">
        <label>Title</label>
        <input type="text" name="title" required>
    </div>
    <div class="field">
        <label>Description</label>
        <textarea name="description" rows="3"></textarea>
    </div>
    <div class="field">
        <label>Due Date</label>
        <input type="date" name="due_date">
    </div>
    <button type="submit" class="btn-primary">Add Milestone</button>
</form>

<h2>Report a Problem</h2>
<form method="POST" action="{{ route('exchanges.dispute', $exchange) }}" class="card">
    @csrf
    <div class="field">
        <label>Describe the issue</label>
        <textarea name="dispute_reason" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn-secondary">File a Dispute</button>
</form>
@endif
@endsection