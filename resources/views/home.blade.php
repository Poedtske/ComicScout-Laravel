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
                    <img src="{{ $scanlator->logo }}" alt="{{ $scanlator->name }}" style="border-radius:100%; background-color:rgb(36,123,115); border:solid rgb(20,111,68); margin-top:10px; height:auto; transform: scale(1.2);">
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

      <section id="activity" style=" background-color: white; color: black;">
        <p id="naam"></p>
        <p id="datum"></p>
        <p id="uur"></p>
        <div>
          <button style="width: 10em;"><a href="kalender/index.html">Kalender</a></button>
        </div>
          <div>
            <button style="width: 2em;" class="prev" id="prev">&#10094;</button>
            <button style="width: 2em;" class="next" id="next">&#10095;</button>
          </div>




      </section>

      <section style="width: 80%; max-width: 600px;">
        <button>
          <a href="https://www.trooper.be/nl/trooperverenigingen/kfdemoedigevrienden" target="_blank"><img class="fotos" src="{{ asset('images/trooper_logo.png') }}" alt="Hoofdsponsor" /></a>
        </button>
      </section>

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
