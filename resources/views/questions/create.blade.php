@extends('layout')

@section('title', 'Create new question')

@section('content')
<h1>Create a Question</h1>
<form method="POST" action="{{ route('questions.store') }}" enctype="multipart/form-data">
    @csrf

    <label>Title</label>
    <input class="@error('title') error-border @enderror" type="text" name="title" value="{{ old('title') }}">
    @error('title')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label>Question</label>
    <textarea class="@error('question') error-border @enderror" name="question">{{ old('question') }}</textarea>
    @error('question')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label>Anwser</label>
    <textarea class="@error('anwser') error-border @enderror" name="anwser">{{ old('anwser') }}</textarea>
    @error('anwser')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label for="category_id">Choose a category:</label>
        <select name="category_id" value="{{ old('category_id') }}">
            @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
        </select>
        <br>


    <button type="submit">Submit</button>
</form>
@endsection
