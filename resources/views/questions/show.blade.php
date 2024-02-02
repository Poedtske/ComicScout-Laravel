@extends('layout')

@section('title', $question->title)

@section('content')

<div class="post-item">
    <div class="post-content">
        <h2>{{ $question->title }}</h2>
        <p>{{ $question->question}}</p>
        <p>{{ $question->anwser }}</p>
        @can('update',$question)
        <br>
        <a href="{{ route('questions.edit',[$question]) }}"><button class="update">Edit question</button></a>

        @endcan
        @can('delete',$question)
        <form method="POST" action="{{ route('questions.destroy',[$question]) }}">
            @csrf
            @method('DELETE')
            <button class="delete" type="submit">Delete question</button>
        </form>
        @endcan
        <small>Posted by <b>{{ $question->user->name }}</b></small>
        <small>Created: <b>{{ $question->created_at }}</b></small>
        @if ($question->updated_at!=$question->created_at )
        <small>Updated: <b>{{ $question->updated_at }}</b></small>
        @endif

    </div>
</div>

@endsection
