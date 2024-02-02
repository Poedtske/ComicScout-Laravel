@extends('layout')

@section('title', 'Edit Question'.$question->title)

@section('content')
<h1>Edit Question {{ $question->title }}</h1>
<form method="POST" action="{{ route('questions.update',[$question]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <label>Title</label>
    <input class="@error('title') error-border @enderror" type="text" name="title" value="{{ old('title',$question->title) }}">
    @error('title')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label>Question</label>
    <textarea class="@error('question') error-border @enderror" name="question">{{ old('question',$question->question) }}</textarea>
    @error('question')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label>Anwser</label>
    <textarea class="@error('anwser') error-border @enderror" name="anwser">{{ old('anwser',$question->anwser) }}</textarea>
    @error('anwser')
    <div class="error">
        {{ $message }}
    </div>
    @enderror

    <label for="category_id">Choose a category:</label>
        <select name="category_id" id="category_id" value="{{ old('category_id',$question->category_id) }}">
            @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
        </select>
        <br>

    <button type="submit">Update</button>
</form>
@endsection
