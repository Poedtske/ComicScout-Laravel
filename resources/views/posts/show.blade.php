@extends('layout')

@section('title', $post->title)

@section('content')

<div class="post-item" style="background-image: url('{{ asset($post->cover) }}');background-size: cover;">
    <div class="post-content">
        <h2>{{ $post->title }}</h2>
        <p>{{ $post->description }}</p>
        @can('update',$post)
        <br>
        <a href="{{ route('posts.edit',[$post]) }}"><button class="update">Edit post</button></a>

        @endcan
        @can('delete',$post)
        <form method="POST" action="{{ route('posts.destroy',[$post]) }}">
            @csrf
            @method('DELETE')
            <button class="delete" type="submit">Delete post</button>
        </form>
        @endcan
        <small>Posted by <b>{{ $post->user->name }}</b></small>
        <small>Created: <b>{{ $post->created_at }}</b></small>
        @if ($post->updated_at!=$post->created_at )
        <small>Updated: <b>{{ $post->updated_at }}</b></small>
        @endif

    </div>
</div>

@endsection
