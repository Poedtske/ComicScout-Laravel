@extends('layout')

@section('title', 'Bookmarks')

@section('customstyle', 'home')

@section('content')

<section style="background-color: black;">
    <div class="scanlatorsFlex-Container">
        <?php
// Initialize an array to store series titles and their corresponding chapters count
$seriesChaptersCount = [];

// Loop through each bookmarked series
foreach (Auth::user()->bookmarks as $serie) {
    // Get the chapters count for the current series
    $chaptersCount = $serie->chapters->count();

    // Check if the series title already exists in the array
    if (array_key_exists($serie->title, $seriesChaptersCount)) {
        // Compare the chapters count and update if the current series has more chapters
        if ($chaptersCount > $seriesChaptersCount[$serie->title]) {
            $seriesChaptersCount[$serie->title] = $chaptersCount;
        }
    } else {
        // Add the series title and its chapters count to the array
        $seriesChaptersCount[$serie->title] = $chaptersCount;
    }
}

// Sort the series titles based on the chapters count in descending order
arsort($seriesChaptersCount);

// Get the sorted series titles
$sortedSeriesTitles = array_keys($seriesChaptersCount);

// Loop through the sorted series titles and display them
foreach ($sortedSeriesTitles as $title) {
    // Find the corresponding serie object by title
    $serie = Auth::user()->bookmarks->where('title', $title)->sortByDesc(function ($serie) {
        return $serie->chapters->count();
    })->first();

    // Display the serie information
    // Replace this with your HTML code to display the serie
}
?>



@foreach ($sortedSeriesTitles as $title)
    <?php
    // Find the corresponding serie object by title
    $serie = Auth::user()->bookmarks->firstWhere('title', $title);

    // Get the latest chapter for the serie
    $latestChapter = $serie->chapters()->orderBy('id', 'desc')->first();
    ?>
    <div class="scanlatorFlex-Item">
        <a href="{{ route('serie.show', [$serie]) }}">
            <img src="{{ $serie->cover }}" alt="{{ $serie->title }}">
        </a>
        <p>{{ $serie->title }}</p>
        @if ($latestChapter)
            <p style="font-size:20px">Last Chapter Date: {{ date("d-m-Y", strtotime($latestChapter->created_at)) }}</p>
        @else
            <p>No chapters available</p>
        @endif
    </div>
@endforeach




    </div>
</section>



@endsection
