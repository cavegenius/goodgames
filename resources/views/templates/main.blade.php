
<!doctype html>
<html lang="en">
  <head>
    <title>Good Games</title>
    <link rel="stylesheet" href="/css/app.css" />
    <script src="/js/app.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
  </head>
  <body id="gamesBody">
    <!--Header-->
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Good Games</a>
      <div id="messageBox"></div>
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="{{ route('logout') }}"
            onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
      </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-1 d-none d-md-block bg-light sidebar">
          <div class="sidebar-sticky">
            @include('inc.sidebarleft')
          </div>
        </div>

        <main role="main" class="col-md-8 ml-sm-auto px-4">
          @yield('content')
        </main>
        
        <div class="col-md-3 d-none d-md-block bg-light sidebar-right">
          <div class="btn-toolbar mb-2 mb-md-0 sidebarRightMenu">
            <div class="btn-group mr-2">
              <button type="button" class="btn btn-sm btn-outline-secondary btn-search right-menu-item">Search</button>
              <button type="button" class="btn btn-sm btn-outline-secondary btn-import right-menu-item">Import</button>
              <button type="button" class="btn btn-sm btn-outline-secondary right-menu-item">Export</button>
            </div>
          </div>
          <div class="sidebarRightContent"></div>
        </div>
      </div>
    </div>

    <div id="big_loader" class="hide-on-load">
      <div id="big_loader_html"></div>
    </div>
    <?php
      $route = Route::current();
      $route = $route->uri;
    ?>
    <input type="hidden" value="<?php echo $route; ?>" id="currentRoute" />

    <div class="hide-on-load" id="response">
      <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <span class="response_message">Save</span>
      </div>
    </div>
  </body>
</html>
