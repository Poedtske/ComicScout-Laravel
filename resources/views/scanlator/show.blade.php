@extends('layout')

@section('title', $scanlator->name)

@section('customstyle', 'home')

@section('content')

<div class="post-item" style="background-image: url('{{ $scanlator->logo }}');background-size: cover; height:500px">
    <div class="post-content">

        <a href="{{ $scanlator->url }}" target="_blank" rel="noopener noreferrer"><h2>{{ $scanlator->name }}</h2></a>
    </div>
</div>
<section style="background-color: black;">
    <div class="scanlatorsFlex-Container">
    @foreach ($scanlator->series as $serie)

            <div class="scanlatorFlex-Item">
                <a href="{{ route('scanlator.serie',[$serie]) }}">
                    <img src="{{ $serie->cover }}" alt="{{ $serie->title }}">
                </a>
                {{ $serie->title }}
                <p></p>
            </div>

    @endforeach
    </div>
  </section>

@endsection
