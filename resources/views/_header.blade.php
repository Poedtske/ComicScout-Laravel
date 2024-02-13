<header>
    <button class="hamburger" aria-expanded="false" aria-controls="main-nav">
      <div class="bar"></div>
    </button>
    <a href="#" class="logo">
      <img class="" src="{{ asset('images/logoFanfare.png') }}" />
    </a>
    <nav id="main-nav" aria-expanded="false">
        <a class="{{ request()->routeIs('home2') ? 'active' : '' }}" href="{{ route('home2') }}">Home</a>
      {{-- <div class="dropdown" aria-expanded="false">
        <button class="btn2 dropbtn" >Fanfare</button>
        <div class="dropdown-content">
            <a class="{{ request()->routeIs('fanfare.instrumenten') ? 'active' : '' }}" href="{{ route('fanfare.instrumenten') }}">Instrumenten</a>
            <a class="{{ request()->routeIs('fanfare.geschiedenis') ? 'active' : '' }}" href="{{ route('fanfare.geschiedenis') }}">Geschiedenis</a>
            <a class="{{ request()->routeIs('fanfare.bestuur') ? 'active' : '' }}" href="{{ route('fanfare.bestuur') }}">Bestuur</a>
            <a class="{{ request()->routeIs('fanfare.dirigent') ? 'active' : '' }}" href="{{ route('fanfare.dirigent') }}">Dirigent</a>
        </div>
      </div>
      <a class="{{ request()->routeIs('jeugd') ? 'active' : '' }}" href="{{ route('jeugd') }}">Jeugd</a>
      <div class="dropdown">
        <button class="dropbtn">Praktische Info</button>
        <div class="dropdown-content">
          <a class="{{ request()->routeIs('praktischeInfo.belangrijkeDocumenten') ? 'active' : '' }}" href="{{ route('praktischeInfo.belangrijkeDocumenten') }}">Belangrijke Documenten</a>
          <a class="{{ request()->routeIs('praktischeInfo.privacyverklaring') ? 'active' : '' }}" href="{{ route('praktischeInfo.privacyverklaring') }}">Privacyverklaring</a>
          <a class="{{ request()->routeIs('praktischeInfo.faq') ? 'active' : '' }}" href="{{ route('praktischeInfo.faq') }}">FAQ</a>
        </div>
      </div>
      <a class="{{request()->routeIs('about') ? 'active' : ''}}" href="{{route('about')}}">About</a> --}}
      @auth
      <a class="{{request()->routeIs('bookmarks') ? 'active' : ''}}" href="{{route('bookmarks')}}">Bookmarks</a>
      <a class="{{request()->routeIs('logout') ? 'active' : ''}}" href="{{route('logout')}}">Logout</a>
      <a class="{{request()->routeIs('profile') ? 'active' : ''}}" href="{{route('dashboard')}}"><img class="avatar" src="{{ Auth::user()->avatar }}" alt=""></a>
      @else
      <a class="{{request()->routeIs('register') ? 'active' : ''}}" href="{{route('register')}}">Register</a>
      <a class="{{request()->routeIs('login') ? 'active' : ''}}" href="{{route('login')}}">Login</a>
      @endauth
    </nav>
  </header>
