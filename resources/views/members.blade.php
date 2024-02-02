@extends('layout');

@section('title', 'Members')

@section('customstyle', 'home')


@section('content')

<ul>
    @foreach ($users as $user)
    <li>
        <a href="{{ route('profile.show',$user->id) }}">{{ $user->name }}</a>
    </li>
        @endforeach
</ul>

@endsection
