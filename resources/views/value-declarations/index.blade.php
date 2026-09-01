@extends('layout')
@section('content')
<h1>My Value Declarations</h1>
<a href="{{ route('value-declarations.create') }}" class="btn-primary">+ New</a>

<div class="list">
    @forelse ($declarations as $d)
        <div class="list-item">
            <h3>{{ $d->title }}</h3>
            <p class="tag">{{ $d->category }}</p>
            <p>{{ $d->description }}</p>
            <p><strong>Offering:</strong> {{ $d->skills_offered }}</p>
            <p><strong>Seeking:</strong> {{ $d->skills_sought }}</p>
        </div>
    @empty
        <p>You haven't created any value declarations yet.</p>
    @endforelse
</div>
@endsection