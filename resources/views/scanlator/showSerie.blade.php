@extends('layout')

@section('title', $serie->title)

@section('customstyle', 'seriePage')

@section('content')

{{-- <div class="post-item" style="background-image: url('{{ $scanlator->logo }}');background-size: cover; height:500px">
    <div class="post-content">

        <a href="{{ $scanlator->url }}" target="_blank" rel="noopener noreferrer"><h2>{{ $scanlator->name }}</h2></a>
    </div>
</div> --}}
<section style="background-color: black;">
    <div class="serieFlex-Container">
        <div class="serieFlex-Item">
            <a href="{{ $serie->url }}">
                <img src="{{ $serie->cover }}" alt="serie cover">
            </a>

        </div>
        <div class="serieFlex-Item infoFlex-Container">
            <p class="infoFlex-Item">Title: {{ $serie->title }}</p>
            <p class="infoFlex-Item">Author: {{ $serie->author }}</p>
            <p class="infoFlex-Item">Artists: {{ $serie->artists }}</p>
            <p class="infoFlex-Item">Status : {{ $serie->status }}</p>
            <p class="infoFlex-Item">Publisher: {{ $serie->company }}</p>
            <p class="infoFlex-Item">Type: {{ $serie->type }}</p>
            {{-- if user has it bookmarked --}}
            {{-- Auth::user()->bookmarks()->where('serie_id', $serie->id)->exists() --}}
            @auth
                @if (Auth::user()->bookmarks()->where('serie_id', $serie->id)->exists())
                <form method="POST" action="{{ route('serie.bookmark',[$serie,Auth::user()]) }}">
                    @csrf
                    <button class="bookmarked" type="submit">bookmarked</button>
                </form>
                @else
                <form method="POST" action="{{ route('serie.bookmark',[$serie,Auth::user()]) }}">
                    @csrf
                    <button class="notBookmarked" type="submit">bookmark</button>
                </form>
                @endif
            @endauth
        </div>
    </div>
  </section>

  <section style="background-color:grey;" class="chaptersFlex-Container">
    @foreach ($serie->chapters as $chapter)
        <a href="{{ $chapter->url }}" class="chapterFlex-Item">
            <button>
                {{ $chapter->title }} <br>
                {{ date("d-m-y",strtotime($chapter->created_at)) }}
            </button>
        </a>
    @endforeach
</section>

@endsection
