@extends('layout')
@section('content')
<h1>My Exchanges</h1>
<div class="list">
    @forelse ($exchanges as $e)
        <div class="list-item">
            <h3><a href="{{ route('exchanges.show', $e) }}">{{ $e->title }}</a></h3>
            <p class="tag">{{ ucfirst($e->status) }}</p>
            <p>With: {{ $e->initiator_id === auth()->id() ? $e->counterpart->name : $e->initiator->name }}</p>
        </div>
    @empty
        <p>No exchanges yet. Start one from your <a href="{{ route('matches') }}">matches</a>.</p>
    @endforelse
</div>
@endsection