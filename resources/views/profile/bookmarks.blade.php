@extends('layout')

@section('title', Auth::user()->name)

@section('customstyle', 'home')

@section('content')

<section style="background-color: black;">
    <div class="scanlatorsFlex-Container">
    <?php $presentBookmarks=[]; ?>
    @foreach (Auth::user()->bookmarks as $serie)
        <?php $trimmedTitle = strtolower(trim($serie->title)); ?>
        @if (!in_array($trimmedTitle, $presentBookmarks))
            <div class="scanlatorFlex-Item">
                <a href="{{ route('serie.show',[$serie]) }}">
                    <img src="{{ $serie->cover }}" alt="{{ $serie->title }}">
                </a>
                {{ $serie->title }}
                <p></p>
            </div>
            <?php $presentBookmarks[] = $trimmedTitle; ?>
        @endif
    @endforeach
    </div>
</section>


@endsection
