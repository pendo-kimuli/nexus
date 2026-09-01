@extends('layout')
@section('content')
<div class="card">
    <h1>Apply for Capital Access</h1>
    <p class="subtitle">Your trust score qualifies you (threshold: {{ $threshold }})</p>
    <form method="POST" action="{{ route('capital.store') }}">
        @csrf
        <div class="field">
            <label>Amount Requested (KES)</label>
            <input type="number" name="amount_requested" min="1" required>
        </div>
        <div class="field">
            <label>Purpose</label>
            <textarea name="purpose" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn-primary">Submit Application</button>
    </form>
</div>
@endsection