@extends('layout')
@section('content')
<h1>Capital-Eligible Users</h1>
<div class="list">
    @forelse ($eligibleUsers as $u)
        <div class="list-item">
            <h3>{{ $u->name }}</h3>
            <p class="tag">Trust Score: {{ $u->trustScore->score }}</p>
        </div>
    @empty
        <p>No users have reached the trust threshold yet.</p>
    @endforelse
</div>

<h1>Capital Access Applications</h1>
<div class="list">
    @forelse ($applications as $a)
        <div class="list-item">
            <h3>{{ $a->user->name }} — KES {{ number_format($a->amount_requested, 2) }}</h3>
            <p class="tag">{{ ucfirst($a->status) }}</p>
            <p>{{ $a->purpose }}</p>
            <p>{{ $a->interests->count() }} investor(s) interested</p>
            @if (!$a->interests->where('investor_id', auth()->id())->count())
                <form method="POST" action="{{ route('investors.interest', $a) }}">
                    @csrf
                    <button type="submit" class="btn-primary">Express Interest</button>
                </form>
            @else
                <p><em>You've expressed interest.</em></p>
            @endif
        </div>
    @empty
        <p>No applications yet.</p>
    @endforelse
</div>
@endsection