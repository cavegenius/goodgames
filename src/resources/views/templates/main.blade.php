
<!doctype html>
<html lang="en">
  <head>
    <title>Your Game Lists</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  </head>
  <body id="gamesBody">
    <?php
      $route = Route::current();
      $route = $route->uri;
    ?>
    <!--Header-->
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      @if (Auth::check())
        <a class="navbar-brand col-sm-3 col-md-2 me-0" href="/games">Your Game Lists</a>
      @else
        <a class="navbar-brand col-sm-3 col-md-2 me-0" href="/">Your Game Lists</a>
      @endif
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          @if (Auth::check())
            <a class="nav-link" href="{{ route('logout') }}"
              onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
              {{ __('Logout') }}
            </a>
          @endif
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
      </ul>
    </nav>

    @if ($route == 'games' || $route == 'home')
      <div class="container-fluid"><div id="games-app"></div></div>
    @else
      <div class="container-fluid"><div class="row">
        <main role="main" id="mainDiv" class="col-md-8 ms-sm-auto px-4">@yield('content')</main>
      </div></div>
    @endif

  </body>
</html>
