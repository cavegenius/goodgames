
<!doctype html>
<html lang="en">
  <head>
    <title>Your Game Lists</title>
    <link rel="stylesheet" href="/css/app.css" />
    <script src="/js/app.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
  </head>
  <body id="gamesBody">
    <?php
      $route = Route::current();
      $route = $route->uri;
    ?>
    <!--Header-->
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      @if (Auth::check())
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/games">Your Game Lists</a>
      @else
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/">Your Game Lists</a>
      @endif
      <div id="messageBox"></div>
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

    <div class="container-fluid">
      <div class="row">
        <div id="leftSidebar" class="col-md-1 d-none d-md-block sidebar filtersBar">
          <div class="sidebar-sticky">
            <?php if($route == 'games') { ?>
              @include('inc.filters')
            <?php } ?>
          </div>
        </div>

        <main role="main" id="mainDiv" class="col-md-8 ml-sm-auto px-4">
          @yield('content')
        </main>
        
        <div id="rightSidebar" class="col-md-3 d-none d-md-block sidebar-right">
          <div class="sidebar-sticky">
            <?php if($route == 'games') { ?>
              <div class="btn-toolbar mb-2 mb-md-0 sidebarRightMenu">
                <div class="btn-group mr-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-search right-menu-item">Search</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-import right-menu-item">Import</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary right-menu-item btn-export">Export</button>
                </div>
              </div>
              <div class="sidebarRightContent"></div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div id="big_loader" class="hide-on-load">
      <div id="big_loader_html"></div>
    </div>
    <input type="hidden" value="<?php echo $route; ?>" id="currentRoute" />

    <div class="hide-on-load" id="response">
      <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <span class="response_message">Save</span>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="my_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body clearfix">
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-outline-secondary" data-dismiss="modal" value="Close" />
                        <!--<input type="button" class="btn btn-primary" value="Save" />-->
                    </div>
                </div>
            </div>
        </div>
  </body>
</html>
