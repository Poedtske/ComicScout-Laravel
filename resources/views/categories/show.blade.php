@extends('layout')

@section('title', $category->name)

@section('content')

<div class="post-item">
    <div class="post-content">
        <h2>{{ $category->name }}</h2>
        @can('update',$category)
        <br>
        <a href="{{ route('categories.edit',[$category]) }}"><button class="update">Edit post</button></a>

        @endcan
        @can('delete',$category)
        <form method="POST" action="{{ route('categories.destroy',[$category]) }}">
            @csrf
            @method('DELETE')
            <button class="delete" type="submit">Delete post</button>
        </form>
        @endcan
        <small>Posted by <b>{{ $category->user->name }}</b></small>
        <small>Created: <b>{{ $category->created_at }}</b></small>
        @if ($category->updated_at!=$category->created_at )
        <small>Updated: <b>{{ $category->updated_at }}</b></small>
        @endif

        @foreach ($category->questions as $question)
            <div>
                {{ $question->title }}
                <!-- Display other question details as needed -->
            </div>
        @endforeach
    </div>
</div>

@endsection
