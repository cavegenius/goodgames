@extends('templates.main')

@section('content')
<?php //print_r($games); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h1 class="h2">Games</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>
            This week
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Prep Time</th>
                <th>Cook Time</th>
                <th>Have in Inventory <div class="float-right"><i class="fa fa-plus addGame"></i></div></th>
            </tr>
            </thead>
            <tbody id="gamesTableBody">
                <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                </tr>
                <tr>
                    <td>1,001</td>
                    <td>Lorem</td>
                    <td>ipsum</td>
                    <td>dolor</td>
                    <td>sit</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


<script type="text/x-handlebars-template" id="addGameRowTemplate">
    <tr id="addGameRow">
            <td>@{{ namething }}</td>
            <td>b</td>
            <td>c</td>
            <td>d</td>
            <td>e</td>
    </tr>
  </script>
  