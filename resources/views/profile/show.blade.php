@extends('layout')

@section('title', 'Home')

@section('customstyle', 'home')


@section('content')
@if ($user->role=="admin")
<section style="justify-content: center; background-color:rgb(164,55,65);color: white;">
    {{ $user->name }}
    @if ($user->birthday!=null)
    <br>
    Birthday:
    {{ $user->getBirthday() }}
    @endif
</section>
@else
<section style="justify-content: center; background-color:grey;color: white;">
    {{ $user->name }}
    @if ($user->birthday!=null)
    <br>
    Birthday:
    {{ $user->getBirthday() }}
    @endif
</section>
@endif

@auth
    @admin
    @if ($user->role=='user')
    <form action="{{ route('profile.promote',[$user]) }}" method="post">
        @csrf
        <button type="submit" name="id" value="{{ $user->id }}" style="background-color: green">Promote</button>
        </form>
    @else
    <form action="{{ route('profile.demote',[$user]) }}" method="post">
        @csrf
        <button type="submit" name="id" value="{{ $user->id }}" style="background-color: red">demote</button>
        </form>
    @endif
    @endadmin
@endauth

<section style="justify-content: center; background-color:white;color: black;">
    <img style=" width:50%; border-radius:30%" src=" {{ $user->avatar }}" alt="user avatar">
</section>

<section style="justify-content: center; background-color:black;color: white;">
    {{ $user->aboutme }}
</section>
@foreach ($user->instruments as $instrument)
    <section style="justify-content: center; background-color:grey;color: white;">
        <img src="{{ asset($instrument->img) }}" alt="{{ $instrument->name }}">
    </section>
@endforeach


@endsection
