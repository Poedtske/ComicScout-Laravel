@extends('layout')

@section('title', 'Create new category')

@section('content')
<h1>Create a New Category</h1>
<form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
    @csrf

    <label>Name</label>
    <input class="@error('name') error-border @enderror" type="text" name="name" value="{{ old('name') }}">
    @error('name')
    <div class="error">
        {{ $message }}
    </div>
    @enderror



    <button type="submit">Submit</button>
</form>
@endsection
