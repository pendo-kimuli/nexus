@extends('layout')
@section('content')
<h1>Capital Access Applications</h1>
<div class="list">
    @forelse ($applications as $a)
        <div class="list-item">
            <h3>{{ $a->user->name }} — KES {{ number_format($a->amount_requested, 2) }}</h3>
            <p class="tag">{{ ucfirst($a->status) }}</p>
            <p>{{ $a->purpose }}</p>
            <p>{{ $a->interests->count() }} investor(s) interested</p>
            @if ($a->daraja_transaction_id)
                <p><strong>Transaction ref:</strong> {{ $a->daraja_transaction_id }}</p>
            @endif
            @if ($a->status === 'pending')
                <form method="POST" action="{{ route('capital.approve', $a) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-primary">Approve</button>
                </form>
                <form method="POST" action="{{ route('capital.reject', $a) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-secondary">Reject</button>
                </form>
            @elseif ($a->status === 'approved')
                <form method="POST" action="{{ route('capital.disburse', $a) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-primary">Disburse via Daraja</button>
                </form>
            @endif
        </div>
    @empty
        <p>No applications yet.</p>
    @endforelse
</div>
@endsection