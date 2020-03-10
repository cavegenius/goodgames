@extends('templates.main')

@section('content')
<?php //print_r($games); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h1 id="mainHeading" class="h2">Games</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button type="button" value="owned" class="btn btn-sm btn-outline-primary listType">Owned</button>
                <button type="button" value="wishlist" class="btn btn-sm btn-outline-secondary listType">Wishlist</button>
                <button type="button"  value="backlog"class="btn btn-sm btn-outline-secondary listType">Backlog</button>
                <input type="hidden" value="owned" id="selectedList" />
            </div>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button type="button" class="btn btn-sm btn-outline-secondary addGame">Add Game</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Import</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th>Favorite</th>
                <th>Name</th>
                <th>Status</th>
                <th>Platform</th>
                <th>Platform Type</th>
                <th>Format</th>
                <th>Genre</th>
                <th>Rating</th>
                <th>Actions <div class="float-right"><i class="fa fa-plus addGame"></i></div></th>
            </tr>
            </thead>
            <tbody id="gamesTableBody">

            </tbody>
        </table>
    </div>
@endsection


@include('handlebars.addGameRowTemplate')
@include('handlebars.showGameRowTemplate')
@include('handlebars.editGameRowTemplate')