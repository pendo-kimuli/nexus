@extends('layout')
@section('content')
<h1>Manage Users</h1>
<div class="list">
    @foreach ($users as $u)
        <div class="list-item">
            <h3>{{ $u->name }}</h3>
            <p class="tag">{{ ucfirst($u->role) }} — {{ $u->is_active ? 'Active' : 'Suspended' }}</p>
            <p>{{ $u->email }} &nbsp; {{ $u->phone_number }}</p>
            @if ($u->trustScore)
                <p>Trust Score: {{ $u->trustScore->score }}</p>
            @endif
            @if ($u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                    @csrf
                    <button type="submit" class="btn-secondary">{{ $u->is_active ? 'Suspend' : 'Reactivate' }}</button>
                </form>
            @endif
        </div>
    @endforeach
</div>
@endsection