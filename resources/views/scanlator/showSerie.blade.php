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
            @auth
                @if (true)
                    <button class="bookmarked">bookmarked</button>
                @else
                    <button class="notBookmarked">bookmark</button>
                @endif
            @endauth
        </div>
    </div>
  </section>

@endsection
