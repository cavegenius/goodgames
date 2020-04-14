@extends('templates.main')

@section('content')
<?php //print_r($games); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h1 id="mainHeading" class="h2">Games</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button type="button" value="all" class="btn btn-sm btn-outline-primary listType">All</button>
                <button type="button"  value="backlog"class="btn btn-sm btn-outline-secondary listType">Backlog</button>
                <button type="button" value="wishlist" class="btn btn-sm btn-outline-secondary listType">Wishlist</button>
                <input type="hidden" value="all" id="selectedList" />
            </div>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button type="button" class="btn btn-sm btn-outline-secondary addGame">Add Game</button>
            </div>
            <div class="btn-group mr-2 add-edit-options hide-on-load">
                <button type="button" class="btn btn-sm btn-outline-secondary saveAll">Save All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary cancelAll">Cancel All</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <input type="hidden" value="name" id="sortCol" />
        <input type="hidden" value="asc" id="sortOrder" />
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th class="sortableCol" data-name="favorite">Favorite</th>
                <th class="sortableCol sortedAsc" data-name="name">Name</th>
                <th class="sortableCol" data-name="status">Status</th>
                <th class="sortableCol" data-name="platform">Platform</th>
                <th class="sortableCol" data-name="platformType">Platform Type</th>
                <th class="sortableCol" data-name="format">Format</th>
                <th class="sortableCol" data-name="genre">Genre</th>
                <th class="sortableCol" data-name="rating">Rating</th>
                <th>Actions</th>
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
@include('handlebars.sidebarImportTemplate')
@include('handlebars.sidebarSearchTemplate')
@include('handlebars.searchResultsTemplate')