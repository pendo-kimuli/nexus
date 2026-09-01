@extends('layout')
@section('content')
<div class="card">
    <h1>New Value Declaration</h1>
    <form method="POST" action="{{ route('value-declarations.store') }}">
        @csrf
        <div class="field">
            <label>Category</label>
            <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. Design, Events, Tutoring" required>
        </div>
        <div class="field">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
        </div>
        <div class="field">
            <label>Description</label>
            <textarea name="description" rows="4" required>{{ old('description') }}</textarea>
        </div>
        <div class="field">
            <label>Skills/Services Offered (comma-separated)</label>
            <input type="text" name="skills_offered" value="{{ old('skills_offered') }}" placeholder="e.g. graphic design, branding, photoshop" required>
        </div>
        <div class="field">
            <label>Skills/Services Sought (comma-separated)</label>
            <input type="text" name="skills_sought" value="{{ old('skills_sought') }}" placeholder="e.g. photography, social media" required>
        </div>
        <button type="submit" class="btn-primary">Submit</button>
    </form>
</div>
@endsection