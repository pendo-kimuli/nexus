@extends('layout')
@section('content')
<h1>Your Matches</h1>

<div class="list">
    @forelse ($matches as $m)
        <div class="list-item">
            <h3>{{ $m['match']->title }}</h3>
            <p class="tag">by {{ $m['match']->user->name }} ({{ $m['match']->category }})</p>
            <p>{{ $m['match']->description }}</p>
            <p><strong>They offer:</strong> {{ $m['match']->skills_offered }}</p>
            <p><strong>They seek:</strong> {{ $m['match']->skills_sought }}</p>
            <p class="matched-on">Matched against your "{{ $m['mine']->title }}" declaration</p>

            <form method="POST" action="{{ route('exchanges.store') }}">
                @csrf
                <input type="hidden" name="counterpart_id" value="{{ $m['match']->user_id }}">
                <input type="hidden" name="value_declaration_id" value="{{ $m['mine']->id }}">
                <div class="field">
                    <label>Exchange Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="field">
                    <label>Contract Terms</label>
                    <textarea name="contract_terms" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn-primary">Start Exchange</button>
            </form>
        </div>
    @empty
        <p>No matches found yet — create a value declaration, or check back once more users have registered.</p>
    @endforelse
</div>
@endsection