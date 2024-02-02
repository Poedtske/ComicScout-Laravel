@extends('layout')


@section('title', 'Kalender')

@section('customstyle', 'main')


@section('content')
<section>
    <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=2&bgcolor=%23ffffff&ctz=Europe%2FBrussels&src=ay5mLmRlbW9lZGlnZXZyaWVuZGVuQGdtYWlsLmNvbQ&src=bmwuYmUjaG9saWRheUBncm91cC52LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23039BE5&color=%230B8043" style="border:solid 1px #777" width="100%" height="400" frameborder="0" scrolling="no"  id="calendar"></iframe>
  </section>

  @foreach ($posts as $post)
  <div class="post-item" style="background-image: url('{{ asset($post->cover) }}');background-size: cover;">
    <div class="post-content">
        <h2>{{ $post->title }}</h2>
        <p>{{ $post->description }}</p>
        <p>Date: {{ $post->date }}</p>
        <p>Time: {{ $post->getTime() }}</p>
        @can('update',$post)
        <br>
        <button class="update"><a href="{{ route('posts.edit',[$post]) }}">Edit post</a></button>

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

  @endforeach

@endsection
