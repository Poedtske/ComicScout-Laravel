<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <title>
        @yield('title','Blog')
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    @auth
    @admin
    <link rel="stylesheet" href="{{asset('css/mainAdmin.css')}}">
    @else
    <link rel="stylesheet" href="{{asset('css/mainFanfare.css')}}">
    @endadmin
    @else
    <link rel="stylesheet" href="{{asset('css/mainFanfare.css')}}">
    @endauth
    <link rel="stylesheet" href="{{ asset('css/' . (trim($__env->yieldContent('customstyle','main')) ?? '') . '.css') }}">
    <script src="{{ asset('js/main.js') }}" type="module"></script>
    <script src="{{ asset('js/' . (trim($__env->yieldContent('customstyle')) ?? '') . '.js') }}" type="module"></script>

</head>
<body>

@include('_header')

{{-- <ul class="nav">
    <li><a class="{{ request()->routeIs('home2') ? 'active' : '' }}" href="{{ route('home2') }}">Home</a></li>
    <li><a class="{{request()->routeIs('about') ? 'active' : ''}}" href="{{route('about')}}">About</a></li>
    @auth
    <li><a class="{{request()->routeIs('posts.create') ? 'active' : ''}}" href="{{route('posts.create')}}">Create Post</a></li>
    <li><a class="{{request()->routeIs('logout') ? 'active' : ''}}" href="{{route('logout')}}">Logout</a></li>
    <li class="username"><p>Logged in as <b>{{ Auth::user()->name }}</b></p></li>
    @else
    <li><a class="{{request()->routeIs('register') ? 'active' : ''}}" href="{{route('register')}}">Register</a></li>
    <li><a class="{{request()->routeIs('login') ? 'active' : ''}}" href="{{route('login')}}">Login</a></li>
    @endauth
</ul> --}}



<main>
@includeWhen($errors->any(),'_errors')

@if (session('success'))
<div class="flash-success">
    {{session('success')}}
</div>
@endif

@yield('content')
</main>

@include('_footer')

</body>
</html>
