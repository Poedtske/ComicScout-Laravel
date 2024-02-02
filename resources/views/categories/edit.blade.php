@extends('layout')

@section('title', 'Update Category'.$category->name)

@section('content')
<h1>Update Post {{ $category->name }}</h1>
<form method="POST" action="{{ route('categories.update',[$category]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <label>name</label>
    <input class="@error('name') error-border @enderror" type="text" name="name" value="{{ old('name',$category->name) }}">
    @error('name')
    <div class="error">
        {{ $message }}
    </div>
    @enderror



    <button type="submit">Update</button>
</form>
@endsection
