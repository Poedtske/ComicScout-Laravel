@extends('layout')

@section('title', 'FAQ')

@section('customstyle', 'home')

@section('content')
@auth
@admin
<br>
<div class=".container">
    <a class="" href="{{route('categories.create')}}"><button class="create flex-item">Create Category</button></a>
    <a class="" href="{{route('questions.create')}}"><button class="create flex-item">Create Question</button></a>
</div>



@endadmin
@else
<br>
<div class=".container">
    <a class="" href="{{route('contact.create')}}"><button class="create flex-item">Ask a Question via de contactpage</button></a>
</div>
@endauth
@foreach ($categories as $category)
        <h1 style="scale:150%; margin-top:6rem; z-index:-5; overflow:hidden;">{{ $category->name }}</h1>
        @can('update',$category)
            <a href="{{ route('categories.edit',[$category]) }}"><button class="update adminFunction">Edit category</button></a>
        @endcan
        @can('delete',$category)
            <form method="POST" action="{{ route('categories.destroy',[$category]) }}">
                @csrf
                @method('DELETE')
                <button class="delete adminFunction" type="submit">Delete category</button>
            </form>
        @endcan

            @foreach ($category->questions as $question)
            <section class="item">
                <p style="padding-inline:2em;"><b>title:</b> {{ $question->title }}</p>
                <p><b>question:</b> {{ $question->question }}</p>
                <p><b>answer:</b> {{ $question->anwser }}</p>
            </section>
            @can('update',$question)
            <br>
            <a href="{{ route('questions.edit',[$question]) }}"><button class="update adminFunction">Edit question</button></a>

            @endcan
            @can('delete',$question)
            <form method="POST" action="{{ route('questions.destroy',[$question]) }}">
                @csrf
                @method('DELETE')
                <button class="delete adminFunction" type="submit">Delete question</button>
            </form>
            @endcan
            @endforeach

@endforeach
@endsection
