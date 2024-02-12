@extends('layout')


@section('title', 'Home')

@section('customstyle', 'home')


@section('content')

      <section class="scanlatorsFlex-Container" style="background-color: black;">
        @foreach ($scanlators as $scanlator)
        @if ($scanlator->name=='ReaperScans')
            <div class="scanlatorFlex-Item">
                <a href="{{ route('scanlator.show',[$scanlator]) }}">
                    <img src="{{ asset($scanlator->logo) }}" alt="{{ $scanlator->name }}">
                </a>
            </div>
        @elseif ($scanlator->name=='RizzComic')
            <div class="scanlatorFlex-Item">
                <a href="{{ route('scanlator.show',[$scanlator]) }}">
                    <img src="{{ $scanlator->logo }}" alt="{{ $scanlator->name }}" style="border-radius:100%; background-color:rgb(36,123,115); border:solid rgb(20,111,68); margin-top:10px; height:auto;">
                </a>
            </div>
        @else
            <div class="scanlatorFlex-Item">
                <a href="{{ route('scanlator.show',[$scanlator]) }}">
                    <img src="{{ $scanlator->logo }}" alt="{{ $scanlator->name }}">
                </a>
            </div>
        @endif
        @endforeach
      </section>
      <p>Todo:</p><br>
      <p>Scraper:</p>
      <p>check and create genres</p>
      <p>Relations:</p>
      <p>Genres</p>
      <p>Scheduling:</p>
      <p>let the scraper work on a schedule</p>

      @auth
      @admin
      <a href="{{route('posts.create')}}"><button class="create">Create Post</button></a>
      @forelse($posts as $post)
        <div class="post-item">
            <div class="post-content">
                <h2><a href="{{ route('posts.show',[$post]) }}">{{ $post->title }}</a></h2>
                <p>{{ $post->description }}</p>
                <small>Posted by <b>{{ $post->user->name }}</b></small>
            </div>
        </div>
        @empty
            <b>There are no posts yet</b>
        @endforelse
      @else
      @forelse($posts as $post)
      <div class="post-item">
          <div class="post-content">
              <h2>{{ $post->title }}</h2>
              <p>{{ $post->description }}</p>
              <small>Posted by <b>{{ $post->user->name }}</b></small>
          </div>
      </div>
      @empty
          <b>There are no posts yet</b>
      @endforelse
      @endadmin
      @else
      @forelse($posts as $post)
      <div class="post-item">
          <div class="post-content">
              <h2>{{ $post->title }}</h2>
              <p>{{ $post->description }}</p>
              <small>Posted by <b>{{ $post->user->name }}</b></small>
          </div>
      </div>
      @empty
          <b>There are no posts yet</b>
      @endforelse
      @endauth

@endsection
