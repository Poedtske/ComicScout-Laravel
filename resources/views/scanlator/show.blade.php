@extends('layout')

@section('title', $scanlator->name)

@section('customstyle', 'scanlatorPage')

@section('content')

{{-- <div class="post-item" style="background-image: url('{{ $scanlator->logo }}');background-size: cover; height:500px">
    <div class="post-content">


    </div>
</div> --}}
@if ($scanlator->name=='ReaperScans')
<a id="scanlator" href="{{ $scanlator->url }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset($scanlator->logo) }}" alt="{{ $scanlator->name }}"></a>
@else
<a id="scanlator" href="{{ $scanlator->url }}" target="_blank" rel="noopener noreferrer"><img src="{{ $scanlator->logo }}" alt="{{ $scanlator->name }}"></a>
@endif

<section style="background-color: black;">
    <div class="scanlatorsFlex-Container">
    @foreach ($scanlator->series as $serie)

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
