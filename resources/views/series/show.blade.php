@extends('layout')

@section('title', $serie->title)

{{-- @section('customstyle', 'home') --}}

@section('content')

{{ $serie->title }}
{{-- <div class="post-item" style="background-image: url('{{ $scanlator->logo }}');background-size: cover; height:500px">
    <div class="post-content">

        <a href="{{ $scanlator->url }}" target="_blank" rel="noopener noreferrer"><h2>{{ $scanlator->name }}</h2></a>
    </div>
</div> --}}
<section style="background-color: black;">
    <div class="scanlatorsFlex-Container">
    {{$serie->title}}
    </div>
  </section>

@endsection
