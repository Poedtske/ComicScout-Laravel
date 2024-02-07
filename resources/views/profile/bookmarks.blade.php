@extends('layout')

@section('title', Auth::user()->name)

@section('customstyle', 'home')

@section('content')

<section style="background-color: black;">
    <div class="scanlatorsFlex-Container">
    @foreach (Auth::user()->bookmarks as $serie)

            <div class="scanlatorFlex-Item">
                <a href="{{ route('serie.show',[$serie]) }}">
                    <img src="{{ $serie->cover }}" alt="{{ $serie->title }}">
                </a>
                {{ $serie->title }}
                <p></p>
            </div>

    @endforeach
    </div>
  </section>

@endsection
