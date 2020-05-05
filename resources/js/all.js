const Handlebars = require("handlebars");
var bootbox = require('bootbox');

// Document ready Actions
    $(document).ready( function() {
        // Variable Definitions
        let currentRoute = $('#currentRoute').val();
        saveAll = false;
        saveError = false;
        filters = {};
        searchTerm= '';
        filterHTML = '';
        listChanged = false;
        //filters['status'] = ['Backlog','Wishlist'];

        // End Variable Definitions

        // Run any functions that need to load initial data sets
        if(currentRoute == 'games') {
            loadGames();
            loadSavedFilters();
            load_platform_list();
            load_genre_list();
        }

        setTimeout( function() {
            $('.btn-search').trigger("click");
        }, 1 );

        // Event Actions
            // Games Actions
                //$( '#searchBar' ).keyup(function() {
                $( document ).on( 'click', '#search', function(){
                    name = $( '#searchBar' ).val();
                    url = '/games/search';
                    post_data = {
                        'name' : name
                    }
            
                    run_ajax(
                        url,
                        post_data,
                        showSearchResults
                    );
                });    

                $( '.addGame' ).click(function() {
                    // Grab the template script
                    var theTemplateScript = $("#addGameRowTemplate").html();
                        
                    // Compile the template
                    var theTemplate = Handlebars.compile(theTemplateScript);
                
                    var isWishlist = $('#selectedList').val() == 'wishlist' ? true : false;
                    var isBacklog = $('#selectedList').val() == 'backlog' ? true : false;
                    let platformHTML = '';
                    $.each(platforms, function( key, value ) {
                        platformHTML += '<option value="'+value+'">'+value+'</option>';
                    });

                    let genreHTML = '';
                    $.each(genres, function( key, value ) {
                        genreHTML += '<option value="'+value+'">'+value+'</option>';
                    });

                    // Define our data object
                    var context={
                        "wishlist": isWishlist,
                        "backlog": isBacklog,
                        "platforms": platformHTML,
                        "genres": genreHTML
                    };

                    let listType = $( '#selectedList' ).val();
                    if(listType == 'wishlist' || listType == 'backlog') {
                        context.rank = true
                    }
                    
                    // Pass our data to the template
                    var theCompiledHtml = theTemplate(context);
                
                    // Add the compiled html to the page
                    $( '#gamesTableBody').prepend(theCompiledHtml);

                    // Show the bulk options
                    $( '.add-edit-options' ).removeClass('hide-on-load');
                });

                $( '.listType' ).click(function() {
                    // Change the disgn of the buttons to highlight the correct list
                    clearFilters();
                    $('#selectedList').val($(this).val());
                    $('.listType').each(function() {
                        $(this).removeClass('btn-outline-primary');
                        $(this).addClass('btn-outline-secondary');
                    });
                    $(this).removeClass('btn-outline-secondary');
                    $(this).addClass('btn-outline-primary');
                    $(this).blur();
                    // Check for wishlist or backlog and sort by rank as default
                    if($(this).val() == 'backlog' || $(this).val() == 'wishlist') {
                        $('#sortCol').val('rank');
                    } else {
                        $('#sortCol').val('name');
                    }
                    // Now we load the new games list
                    listChanged = true;
                    loadGames();
                });

                $( '.right-menu-item' ).click(function() {
                    $('.right-menu-item').each(function() {
                        $(this).removeClass('btn-outline-primary');
                        $(this).addClass('btn-outline-secondary');
                    });
                    $(this).removeClass('btn-outline-secondary');
                    $(this).addClass('btn-outline-primary');
                    $(this).blur();
                });

                /*$(document).on('dblclick', '#gamerow', function() {
                    showUnsavedChanges();

                    showEditGameFields(this);
                });*/

                $(document).on('click', '.editSingleGame', function() {
                    showUnsavedChanges();

                    showEditGameFields($(this).closest('tr'));
                });

                $( document ).on( 'click', '.cancelSingleAddGame, .cancelSingleEditGame', function(){
                    $(this).closest('tr').remove();
                    if(!$( '#gamesTableBody').find('.saveSingleEditGame, .saveSingleAddGame, .saveSingleDeleteGame').length) {
                        // Reset the flags
                        saveError = false;
                        saveAll = false;
                        hideUnsavedChanges();
                    }
                    loadGames();
                });

                $( document ).on( 'click', '.saveSingleAddGame', function(){
                    var rank;
                    var name;
                    var status;
                    var platform;
                    var platformType;
                    var favorite;
                    var rating = 0;
                    var format;
                    var notes = '';

                    $(this).closest('tr').find('input, select').each(function() {
                        switch($(this).attr('name')) {
                            case 'favorite':
                                favorite = $(this).is(':checked') == true ? 1 : 0;
                                break;
                            case 'rating':
                                if ( $(this).is(':checked') ) {
                                    rating = $( this ).val();
                                }
                                break;
                            case 'name':
                                name = $( this ).val();
                                break;
                            case 'status':
                                status = $(this).val();
                                break;
                            case 'platformType':
                                platformType = $(this).val();
                                break;
                            case 'format':
                                format = $(this).val();
                                break;
                            case 'platform':
                                platform = $( this ).val();
                                break;
                            case 'genre':
                                genre = $( this ).val();
                                break;
                            case 'rank':
                                rank = $( this ).val();
                                break;
                        }
                    });

                    url = '/games/add';
                    post_data = {
                        'name' : name,
                        'rank' : rank,
                        'status' : status,
                        'platform' : platform,
                        'platformType' : platformType,
                        'favorite' : favorite,
                        'rating' : rating,
                        'format' : format,
                        'notes' : notes
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            if(!saveAll) {
                                message_pop(obj.response.status, obj.response.message, 2500);
                                hideUnsavedChanges();
                            } else {
                                if(obj.response.status == 'Error') {
                                    saveError = true;
                                }
                            }
                            loadGames();
                        }
                    );
                    $(this).closest('tr').remove();
                });

                $( document ).on( 'click', '.saveSingleEditGame', function(){
                    var id;
                    var rank;
                    var name;
                    var status;
                    var platform;
                    var platformType;
                    var favorite;
                    var rating = 0;
                    var format;
                    var notes = '';

                    $(this).closest('tr').find('input, select').each(function() {
                        switch($(this).attr('name')) {
                            case 'id':
                                id = $( this ).val();
                                break;
                            case 'favorite':
                                favorite = $(this).is(':checked') == true ? 1 : 0;
                                break;
                            case 'rating':
                                if ( $(this).is(':checked') ) {
                                    rating = $( this ).val();
                                }
                                break;
                            case 'name':
                                name = $( this ).val();
                                break;
                            case 'status':
                                status = $(this).val();
                                break;
                            case 'platformType':
                                platformType = $(this).val();
                                break;
                            case 'format':
                                format = $(this).val();
                                break;
                            case 'platform':
                                platform = $( this ).val();
                                break;
                            case 'genre':
                                genre = $( this ).val();
                                break;
                            case 'rank':
                                rank = $( this ).val();
                                break;
                        }
                    });

                    url = '/games/update';
                    post_data = {
                        'id' : id,
                        'rank': rank,
                        'name' : name,
                        'status' : status,
                        'platform' : platform,
                        'platformType' : platformType,
                        'favorite' : favorite,
                        'rating' : rating,
                        'format' : format,
                        'notes' : notes
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            if(!saveAll) {
                                message_pop(obj.response.status, obj.response.message, 2500);
                                hideUnsavedChanges();
                            } else {
                                if(obj.response.status == 'Error') {
                                    saveError = true;
                                }
                            }
                            loadGames();
                        }
                    );
                    $(this).closest('tr').remove();
                });

                $(document).on('click', '.cancelAll', function() {
                    cancelAll = true
                    $( '#gamesTableBody').find('.cancelSingleEditGame, .cancelSingleAddGame, .cancelSingleDeleteGame').each(function() {
                        $(this).click();
                    });
                    cancelAll = false;
                    hideUnsavedChanges();
                });

                $(document).on('click', '.saveAll', function() {
                    saveAll = true;
                    $( '#gamesTableBody').find('.saveSingleEditGame, .saveSingleAddGame, .saveSingleDeleteGame').each(function() {
                        $(this).click();
                    });
                    if (!saveError) {
                        message_pop( 'Success', 'All changes Saved Successfully', 2500);
                    } else {
                        message_pop( 'Error', 'There was an error saving one or more changes.', 2500);
                    }
                    // Reset the flags
                    saveError = false;
                    saveAll = false;
                    hideUnsavedChanges();
                });

                $(document).on('click', '.deleteSingleGame', function() {
                    showUnsavedChanges();

                    $(this).closest('tr').css('text-decoration','line-through');
                    $(this).closest('td').html('<i class="fas fa-check saveSingleDeleteGame"></i> <i class="fas fa-ban cancelSingleDeleteGame"></i>')
                });

                $(document).on('click', '.cancelSingleDeleteGame', function() {
                    $(this).closest('tr').css('text-decoration','none');
                    $(this).closest('td').html('<i class="fas fa-edit editSingleGame"></i> <i class="fas fa-trash deleteSingleGame"></i>')

                    if(!$( '#gamesTableBody').find('.saveSingleEditGame, .saveSingleAddGame, .saveSingleDeleteGame').length) {
                        // Reset the flags
                        saveError = false;
                        saveAll = false;
                        hideUnsavedChanges();
                    }
                });

                // Process Deletion
                $(document).on('click', '.saveSingleDeleteGame', function() {
                    let id = $(this).closest('tr').data('id');
                    url = '/games/delete';
                    post_data = {
                        'id' : id,
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            if(!saveAll) {
                                message_pop(obj.response.status, obj.response.message, 2500);
                                hideUnsavedChanges();
                            } else {
                                if(obj.response.status == 'Error') {
                                    saveError = true;
                                }
                            }
                            loadGames();
                        }
                    );
                    $(this).closest('tr').remove();
                });

                $(document).on('click', '.favoriteBox', function() {
                    let id = $(this).closest('tr').data('id');
                    url = '/games/update';
                    favorite = $(this).is(':checked') == true ? 1 : 0;

                    post_data = {
                        'id' : id,
                        'favorite': favorite
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            message_pop('success', 'Updated Successfully', 2500);
                            loadGames();
                        }
                    );
                });

                $(document).on('click', '.ratingBox', function() {
                    let id = $(this).closest('tr').data('id');
                    let currentRating = $(this).closest('td').data('rating');
                    url = '/games/update';
                    if (currentRating != $( this ).val()){
                        rating = $( this ).val();
                    } else {
                        rating = 0;
                    }
                    post_data = {
                        'id' : id,
                        'rating': rating
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            message_pop('success', 'Game Rating Updated', 2500);
                            loadGames();
                        }
                    );
                });

                $(document).on('click', '.fa-star', function() {
                    let clicked = $(this).siblings('input').val();

                    $(this).closest('td').find('input').each(function() {
                        if($(this).val() < clicked) {
                            if($(this).prop( "checked") == false) {
                                $(this).prop( "checked", true );
                            } else {
                                $(this).prop( "checked", false );
                            }
                        }
                    });
                });

                $(document).on('click', '.btn-search', function() {
                    var theTemplateScript = $("#sidebarSearchTemplate").html();

                    // Compile the template
                    var theTemplate = Handlebars.compile(theTemplateScript);

                    // Define our data object
                    var context={};

                    // Pass our data to the template
                    var theCompiledHtml = theTemplate(context);

                    // Add the compiled html to the page
                    $( '.sidebarRightContent' ).html(theCompiledHtml);
                });

                $(document).on('click', '.btn-import', function() {
                    var theTemplateScript = $("#sidebarImportTemplate").html();

                    // Compile the template
                    var theTemplate = Handlebars.compile(theTemplateScript);

                    // Define our data object
                    var context={};

                    // Pass our data to the template
                    var theCompiledHtml = theTemplate(context);

                    // Add the compiled html to the page
                    $( '.sidebarRightContent' ).html(theCompiledHtml);
                });

                $(document).on('click', '.btn-export', function() {
                    url = '/games/exportCSV';
                    window.open(
                        url,
                        '_blank' // <- This is what makes it open in a new window.
                      );
                      
                });

                $(document).on('click', '.addToInventory', function() {
                    // Grab the template script
                    var theTemplateScript = $("#addGameRowTemplate").html();
                        
                    // Compile the template
                    var theTemplate = Handlebars.compile(theTemplateScript);
                
                    var isWishlist = false;

                    let platformHTML = '';
                    $.each(platforms, function( key, value ) {
                        platformHTML += '<option value="'+value+'">'+value+'</option>';
                    });

                    let genreHTML = '';
                    $.each(genres, function( key, value ) {
                        genreHTML += '<option value="'+value+'">'+value+'</option>';
                    });

                    // Define our data object
                    var context={
                        "wishlist": isWishlist,
                        "name":$(this).data('name'),
                        "platforms": platformHTML,
                        "genres": genreHTML
                    };
                    
                    // Pass our data to the template
                    var theCompiledHtml = theTemplate(context);
                
                    // Add the compiled html to the page
                    $( '#gamesTableBody').prepend(theCompiledHtml);

                    // Show the bulk options
                    $( '.add-edit-options' ).removeClass('hide-on-load');
                });

                $(document).on('click', '.addToWishlist', function() {
                    // Grab the template script
                    var theTemplateScript = $("#addGameRowTemplate").html();
                        
                    // Compile the template
                    var theTemplate = Handlebars.compile(theTemplateScript);
                
                    var isWishlist = true;

                    let platformHTML = '';
                    $.each(platforms, function( key, value ) {
                        platformHTML += '<option value="'+value+'">'+value+'</option>';
                    });

                    let genreHTML = '';
                    $.each(genres, function( key, value ) {
                        genreHTML += '<option value="'+value+'">'+value+'</option>';
                    });

                    // Define our data object
                    var context={
                        "wishlist": isWishlist,
                        "name":$(this).data('name'),
                        "platforms": platformHTML,
                        "genres": genreHTML
                    };
                    
                    // Pass our data to the template
                    var theCompiledHtml = theTemplate(context);
                
                    // Add the compiled html to the page
                    $( '#gamesTableBody').prepend(theCompiledHtml);

                    // Show the bulk options
                    $( '.add-edit-options' ).removeClass('hide-on-load');
                });

                $(document).on('click', '.sortableCol', function() {
                    if( $( this ).hasClass('sortedAsc') ) {
                        $( this ).removeClass('sortedAsc');
                        $( this ).addClass('sortedDesc');
                        $('#sortOrder').val('desc');
                    } else if( $( this ).hasClass('sortedDesc') ) {
                        $( this ).addClass('sortedAsc');
                        $( this ).removeClass('sortedDesc');
                        $('#sortOrder').val('asc');
                    } else {
                        $('.sortableCol').each(function() {
                            $( this ).removeClass('sortedAsc');
                            $( this ).removeClass('sortedDesc');
                        });

                        $( this ).addClass('sortedAsc');
                        $('#sortCol').val( $(this).data('name') ),
                        $('#sortOrder').val('asc');
                    }
                    loadGames();
                });

                $( '#inventorySearch' ).keyup(function() {
                    searchTerm = $( '#inventorySearch' ).val();
                    processFilters()
                    loadGames();
                });

                $(document).on('change', '.filterItem', function() {
                    processFilters();
                    loadGames();
                });

                $(document).on('click', '.filterHeader', function() {
                    let section = $( this ).data('section');
                    if( $( this ).hasClass('filterClosed') ) {
                        $( '.'+section ).removeClass('hide-on-load');
                        $( this ).removeClass('filterClosed');
                        $( this ).addClass('filterOpened');
                    } else {
                        $( '.'+section ).addClass('hide-on-load');
                        $( this ).removeClass('filterOpened');
                        $( this ).addClass('filterClosed');
                    }
                });

                $(document).on('click', '.clearFilters', function() {
                    clearFilters();
                    loadGames();
                    $(this).blur();
                });

                $(document).on('click', '.saveFilter', function() {
                    if($('.filterNameDiv').hasClass('hide-on-load')) {
                        $('.filterNameDiv').removeClass('hide-on-load');
                        $('.saveFilterButtons').removeClass('hide-on-load');
                    } else {
                        $('.filterNameDiv').addClass('hide-on-load');
                        $('.saveFilterButtons').addClass('hide-on-load');
                    }
                });

                $(document).on('click', '.saveFilterSubmit', function() {
                    if($('#filterName').val() == '') {
                        message_pop('danger', 'Filter Name is Required', 2500);
                    } else if(jQuery.isEmptyObject(filters) && searchTerm.length == 0) {
                        message_pop('danger', 'A search term must be entered or one or more filters must be selected', 2500);
                    } else {
                        url = '/filters/add';
                        name = $('#filterName').val();

                        post_data = {
                            'name' : name,
                            'filter': filters,
                            'searchTerm': searchTerm
                        };

                        run_ajax(
                            url,
                            post_data,
                            function(obj) {
                                message_pop(obj.response.Status, obj.response.Message, 2500);
                                $('.saveFilterCancel').trigger('click');
                                loadSavedFilters();
                            }
                        );
                    }
                });

                $(document).on('click', '.saveFilterCancel', function() {
                    $('#filterName').val('');
                    $('.filterNameDiv').addClass('hide-on-load');
                    $('.saveFilterButtons').addClass('hide-on-load');
                });

                $(document).on('change', '.savedFilterRadio', function() {
                    clearFilters();
                    let url = '/filters/apply';
                    let id = $(this).val();

                    post_data = {
                        'id' : id
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            // Use obj.response.Filter.filter and obj.response.Filter.searchterm to set the values in the form
                            // Then run process filters and then load games.
                            let filter = $.parseJSON(obj.response.Filter.filter);
                            $('#inventorySearch').val(obj.response.Filter.searchTerm);
                            searchTerm = obj.response.Filter.searchTerm
                            $.each(filter, function( key, value ) {
                                $.each(value, function( key, value ) {
                                    $('.filterItem[value="'+value+'"]').prop("checked",true);
                                });
                            });
                            processFilters();
                            loadGames();
                            message_pop('success', 'Filter Applied', 2500);
                        }
                    );
                });

                $(document).on('click', '.deleteFilter', function() {
                    let id = $(this).data('id');
                    confirmAction( 'Are you sure you want to delete this filter', processFilterDelete, id);
                });

                $(document).on('click', '.editFilterName', function() {
                    let id = $(this).data('id');
                    let name = $('.savedFilterRadio[value="'+id+'"]').data('name');
                    // We can only edit one at a time so I need to reset the section before creating the editor.
                    $('#filterList').html(filterHTML);
                    $('.savedFilterNameWrapper[data-id="'+id+'"]').html('<input type="text" name="updateName" class="updateNameField" data-id="'+id+'" value="'+name+'">');
                    $('.savedFilterActionsWrapper[data-id="'+id+'"]').html('<i class="fas fa-ban cancelEditFilterName" data-id="'+id+'" data-name="'+name+'"></i><i class="fas fa-check saveEditFilterName" data-id="'+id+'"></i>');
                });

                $(document).on('click', '.cancelEditFilterName', function() {
                    $('#filterList').html(filterHTML);
                });

                $(document).on('click', '.saveEditFilterName', function() {
                    url = '/filters/updateName';
                    id = $(this).data('id');
                    name = $('.updateNameField[data-id="'+$(this).data('id')+'"]').val();

                    post_data = {
                        'id' : id,
                        'name': name
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            message_pop(obj.response.Status, obj.response.Message, 2500);
                            loadSavedFilters();
                        }
                    );
                });

                $(document).on('click', '.editFilterValues', function() {
                    let id = $(this).data('id');
                    confirmAction( 'Are you sure you want to update this filter', processEditFilterValues, id);
                });

                $(document).on("click", "#importCSVSubmit", function () {
                    var formData = new FormData();
                    file = $('#csvFile').prop('files')[0];
                    show_big_loader( );
                    formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                    formData.append("csvFile", file);
                    $.ajax({
                        url: '/games/importCSV',
                        type: 'POST',              
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(obj) {
                            let response = $.parseJSON(obj);
                            hide_big_loader();
                            loadGames();
                            setTimeout( function() {
                                message_pop(response.Status, response.Message, 5000);
                            }, 500 );
                        }
                    });
                }); 
            // End Games Actions
        // End Event Actions
    });
// End Document ready Actions

// Global Functions
    function run_ajax( url, data_obj, return_function, loader_message = false ) {
        if ( url != '' )
        {
            show_big_loader( loader_message );
            data_obj._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type:       "POST"
                , url:      url
                , data:     data_obj
                , dataType: "json"
                , success:  function( response ) {
                        if( response.Logout == true ) {
                            window.location.replace("/login");
                        }
                        if ( return_function != '' && return_function !== null && return_function !== undefined )
                        {
                            var obj          = {};
                                obj.data     = data_obj;
                                obj.response = response;

                                return_function( obj );
                        }

                        hide_big_loader();
                    }
            });
        }

        return true;
    }

    function show_big_loader( extra_note = false ) {
        jQuery( 'document' ).ready( function() {
            jQuery( '#big_loader' ).fadeIn( 250 );
            let html = loader();
            if ( extra_note )
            {
                html += '<span class="big_loader_note">' + extra_note + '</span>';
            }
            jQuery( '#big_loader_html' ).html( html );
        });
    }

    function hide_big_loader() {
        jQuery( 'document' ).ready( function() {
            jQuery( '#big_loader' ).fadeOut( 250 );
            jQuery( '#big_loader_html' ).html( '' );
        });
    }

    function loader() {
        var html  = '<div class="cssload-container">';
            html += '<div class="cssload-shaft1"></div>';
            html += '<div class="cssload-shaft2"></div>';
            html += '<div class="cssload-shaft3"></div>';
            html += '<div class="cssload-shaft4"></div>';
            html += '<div class="cssload-shaft5"></div>';
            html += '<div class="cssload-shaft6"></div>';
            html += '<div class="cssload-shaft7"></div>';
            html += '<div class="cssload-shaft8"></div>';
            html += '<div class="cssload-shaft9"></div>';
            html += '</div>';

        return html;
    }

    function message_pop( alert_status, message, delay, location = '#messageBox' ) {
        delay    = delay == '' ? 2500 : delay;
        location = location == '' ? false : location;

        jQuery( document ).ready( function() {
            jQuery( '#response .alert' ).attr( 'class', 'alert alert-' + alert_status );
            jQuery( '#response .response_message' ).html( message );

            if ( location )
            {
                jQuery( location ).html( jQuery( '#response' ).html() );
            }
            else
            {
                location = '#response';
            }

            jQuery( location ).slideDown( 250 );

            // only hide the message if delay is not set to forever
            if ( delay != 'forever' )
            {
                setTimeout( function() {
                    jQuery( location ).slideUp( 250 );
                }, delay );
            }
        });
    }

    function message_clear(location) {
        jQuery( location ).html( '' );
    }

    function confirmAction( confirmMessage, fn_callback, param1 ='', param2='', param3='' ) {
        bootbox.confirm({
            message: confirmMessage,
            title: 'Confirm?',
            buttons: {
                cancel: { label: 'Cancel' },
                confirm: { label: 'Ok' }
            },
            callback: function( result ){ fn_callback( result, param1, param2, param3 ); }
        });
    }
// End Global Functions

// Games Functions
    function showSearchResults(obj) {
        $('#searchResults').html('');
        $.each(obj.response['Games'], function( key, value ) {
            var theTemplateScript = $("#searchResultsTemplate").html();

            // Compile the template
            var theTemplate = Handlebars.compile(theTemplateScript);

            // Define our data object
            var context={
                "cover": value.cover,
                "name": value.name,
                "genres": value.genres,
                "platforms": value.platforms,
                "summary": value.summary
            };

            // Pass our data to the template
            var theCompiledHtml = theTemplate(context);

            // Add the compiled html to the page
            $('#searchResults').append(theCompiledHtml);
        });
    }

    function showUnsavedChanges() {
        // Show the bulk options
        message_pop( 'warning', 'You have unsaved Changes. Click \'Save All\' to process your changes.', 'forever', '#messageBox' );
        $( '.add-edit-options' ).removeClass('hide-on-load');
    }

    function hideUnsavedChanges() {
        // Show the bulk options
        $( '#messageBox' ).html('');
        $( '.add-edit-options' ).addClass('hide-on-load');
    }

    function loadGames() {
        url = '/games/showList';
        post_data = {
            list: $('#selectedList').val(),
            sortCol: $('#sortCol').val(),
            sortOrder: $('#sortOrder').val(),
            filtered: JSON.stringify( filters, null, 1 ),
            searchTerm: searchTerm
        }

        run_ajax(
            url,
            post_data,
            showGameList
        );
    }

    function showGameList(obj) {
        savedRows = [];

        // Clear all messages
        message_clear('#messageBox');
        if(listChanged){
            $( '#gamesTableBody').html('');
        } else {
            $( '#gamesTableBody').find('tr').each(function() {
                if(!$(this).find('.saveSingleAddGame, .saveSingleEditGame, .saveSingleDeleteGame').length) {
                    $(this).remove();
                } else {
                    showUnsavedChanges();
                    // Check if it is editing a game if so:
                    // Save the HTML of this row with an identified based on the id 
                    // Then during the loop later writing the rows write this code instead of the template 
                    if($(this).find('.saveSingleEditGame').length) {
                        savedRows[$(this).data('id')] = '<tr id="gamerow" data-id="'+$(this).data('id')+'">'+$(this).html()+'</tr>';
                        $(this).html(''); 
                    }
                }
            });
        }
        // Showing the table headers
        var theTemplateScript = $("#tableHeadersTemplate").html();
        // Compile the template
        var theTemplate = Handlebars.compile(theTemplateScript);
        // Define our data object
        var context={
        };
        let listType = $( '#selectedList' ).val();
        if(listType == 'wishlist' || listType == 'backlog') {
            context.showRank = true;
        }

        if($('#sortOrder').val() == 'asc') {
            context.asc = true;
        }

        switch($('#sortCol').val()) {
            case 'favorite':
                context.sortFavorite = true;
                break;
            case 'rating':
                context.sortRating = true;
                break;
            case 'name':
                context.sortName = true;
                break;
            case 'status':
                context.sortStatus = true;
                break;
            case 'platformType':
                context.sortPlatformType = true;
                break;
            case 'format':
                context.sortFormat = true;
                break;
            case 'platform':
                context.sortPlatform = true;
                break;
            case 'genre':
                context.sortGenre = true;
                break;
            case 'rank':
                context.sortRank = true;
                break;
        }

        // Pass our data to the template
        var theCompiledHtml = theTemplate(context);
        $( '#gameTableHeading').html(theCompiledHtml);

        if (obj.response.Status == 'Success') {
            $.each(obj.response['Games'], function( key, value ) {
                // Grab the template script
                var theTemplateScript = $("#showGameRowTemplate").html();
                        
                // Compile the template
                var theTemplate = Handlebars.compile(theTemplateScript);
                let rating = '';
                for (let step = 1; step <= 5; step++) {
                    if(value.rating >= step) {
                        rating += '<label class="ratingStar"><input type="checkbox" checked value="'+step+'" class="ratingBox" /><i class="far fa-star unchecked"></i><i class="fas fa-star checked"></i></label>';
                    } else {
                        rating += '<label class="ratingStar"><input type="checkbox" value="'+step+'" class="ratingBox" /><i class="far fa-star unchecked"></i><i class="fas fa-star checked"></i></label>';
                    }
                }
                // Define our data object
                var context={
                    "gameID": value.id,
                    "favorite": value.favorite,
                    "name": value.name,
                    "status": value.status,
                    "platform": value.platform,
                    "platformType": value.platformType,
                    "format": value.format,
                    "genre": value.genre,
                    "rating": rating,
                    "ratingValue": value.rating,
                };

                let listType = $( '#selectedList' ).val();
                if(listType == 'wishlist' || listType == 'backlog') {
                    context.showRank = true;
                }
                context.rank = value.rank
                
                // Pass our data to the template
                var theCompiledHtml = theTemplate(context);

                // Add the compiled html to the page
                if(savedRows.hasOwnProperty(value.id) ) {
                    $( '#gamesTableBody').append(savedRows[value.id]);
                } else {
                    $( '#gamesTableBody').append(theCompiledHtml);
                }
            });
        } else if(obj.response.Status == 'Error') {
            $( '#gamesTableBody').append('<tr><td colspan="9" class="text-center">'+obj.response.Message+'</td></tr>');
        }
    }

    function showEditGameFields( row ) {
        let id = $(row).data('id');
        let listType = $( '#selectedList' ).val();
        let name = $(row).find('.name').text();
        if(listType == 'all') {
            var rank = $(row).find('.rank').val();
        } else {
            var rank = $(row).find('.rank').text();
        }
        let status = $(row).find('.status').text();
        let platform = $(row).find('.platform').text();
        let platformType = $(row).find('.platformType').text();
        let format = $(row).find('.format').text();
        let genre = $(row).find('.genre').text();
        var rating = 0;

        // Need to handle the select sections differently. Creating the option html to pass through
        let statusHTML = '';
        statusHTML +='<option value="None" '+(status == 'None'? 'selected' : '')+'>None</option>';
        statusHTML += '<option value="Might Play" '+(status == 'Might Play' ? 'selected' : '')+'>Might Play</option>';
        statusHTML += '<option value="Backlog" '+(status == 'Backlog' ? 'selected' : '')+'>Backlog</option>';
        statusHTML += '<option value="In Progress" '+(status == 'In Progress' ? 'selected' : '')+'>In Progress</option>';
        statusHTML += '<option value="Completed" '+(status == 'Completed' ? 'selected' : '')+'>Completed</option>';
        statusHTML += '<option value="Wishlist" '+(status == 'Wishlist' ? 'selected' : '')+'>Wishlist</option>';
        statusHTML += '<option value="Paused" '+(status == 'Paused' ? 'selected' : '')+'>Paused</option>';
        statusHTML += '<option value="Replayable" '+(status == 'Replayable' ? 'selected' : '')+'>Replayable</option>';
        statusHTML += '<option value="Abandoned" '+(status == 'Abandoned' ? 'selected' : '')+'>Abandoned</option>';
        statusHTML += '<option value="Wont Play" '+(status == 'Wont Play' ? 'selected' : '')+'>Wont Play</option>';

        let platformHTML = '';
        $.each(platforms, function( key, value ) {
            platformHTML += '<option value="'+value+'" '+(platform == value ? 'selected' : '')+'>'+value+'</option>';
        });

        let genreHTML = '';
        $.each(genres, function( key, value ) {
            genreHTML += '<option value="'+value+'" '+(genre == value ? 'selected' : '')+'>'+value+'</option>';
        });

        let platformTypeHTML = '';
        platformTypeHTML += '<option value="Other" '+(platformType == 'Other'? 'selected' : '')+'>Other</option>';
        platformTypeHTML += '<option value="PC" '+(platformType == 'PC'? 'selected' : '')+'>PC</option>';
        platformTypeHTML += '<option value="Console" '+(platformType == 'Console'? 'selected' : '')+'>Console</option>';

        let formatHTML = '';
        formatHTML += '<option value="Not Set" '+(format == 'Not Set'? 'selected' : '')+'>Not Set</option>';
        formatHTML += '<option value="Physical" '+(format == 'Physical'? 'selected' : '')+'>Physical</option>';
        formatHTML += '<option value="Digital" '+(format == 'Digital'? 'selected' : '')+'>Digital</option>';

        $(row).find('.ratingStar').each(function() {
            if( $(this).children('input[type=checkbox]').is(':checked') ) {
                rating = $(this).children('input[type=checkbox]').val();
            }
        });

        let ratingHTML = '';
        for (let step = 1; step <= 5; step++) {
            if(rating >= step) {
                ratingHTML += '<label class="ratingStar"><input type="checkbox" name="rating" checked value="'+step+'" /><i class="far fa-star unchecked"></i><i class="fas fa-star checked"></i></label>';
            } else {
                ratingHTML += '<label class="ratingStar"><input type="checkbox" name="rating" value="'+step+'" /><i class="far fa-star unchecked"></i><i class="fas fa-star checked"></i></label>';
            }
        }
        //$(this).find('.name').html('changed');
        var theTemplateScript = $("#editGameRowTemplate").html();

        // Compile the template
        var theTemplate = Handlebars.compile(theTemplateScript);

        // Define our data object
        var context={
            "id": id,
            "name": name,
            "status": statusHTML,
            "platforms": platformHTML,
            "platformType": platformTypeHTML,
            "format": formatHTML,
            "genres": genreHTML,
            "rating": ratingHTML,
        };
        if(listType == 'wishlist' || listType == 'backlog') {
            context.showRank = true;
        }
        context.rank = rank;

        if($(row).find('.favoriteIcon').children('input[type=checkbox]').is(':checked') ){
            context.favorite = true;
        }

        // Pass our data to the template
        var theCompiledHtml = theTemplate(context);

        // Add the compiled html to the page
        $( row ).html(theCompiledHtml);
    }

    function load_platform_list() {
        url = '/games/get_platform_list';
        post_data = {}

        run_ajax(
            url,
            post_data,
            function(obj) {
                platforms = obj.response.Platforms;
            }
        );
    }

    function load_genre_list() {
        url = '/games/get_genre_list';
        post_data = {}

        run_ajax(
            url,
            post_data,
            function(obj) {
                genres = obj.response.Genres;
            }
        );
    }

    function resetListAll() {
        $('#selectedList').val('all');
        $('.listType').each(function() {
            $(this).removeClass('btn-outline-primary');
            $(this).addClass('btn-outline-secondary');
        });
        
        $('.listType[value="all"]').removeClass('btn-outline-secondary');
        $('.listType[value="all"]').addClass('btn-outline-primary');
        $('.listType[value="all"]').blur();
    }

    function processFilters() {
        filters = {};
        // resetListAll(); 
        $('input.filterItem').each(function(key,elem){
            let name  = $(this).attr('name');
            let value = $(this).val();
            if ( typeof filters[ name ] == "undefined" ) {
                filters[ name ] = [];
            }
            if ( $(this).is( ":checked" ) ) {
                filters[ name ].push( value );
            }
        });
    }

    function clearFilters() {
        filters = {};
        resetListAll();
        $('#inventorySearch').val('');
        searchTerm = '';
        $('input.filterItem').each(function(key,elem){
            let name  = $(this).attr('name');

            if ( typeof filters[ name ] == "undefined" ) {
                filters[ name ] = [];
            }
            if ( $(this).is( ":checked" ) ) {
                $(this).prop("checked",false);
            }
        });
    }

    function loadSavedFilters() {
        $('#filterList').html('');
        url = '/filters/list';
        post_data = {}

        run_ajax(
            url,
            post_data,
            function(obj) {
                //savedFilters = obj.response.Genres;
                $.each(obj.response.Filters, function( key, value ) {
                    $('#filterList').append(' <li class="nav-item savedFilterItem" data-id="'+value.id+'"><div class="savedFilterNameWrapper" data-id="'+value.id+'"><input type="radio" name="activateSavedFilter" value="'+value.id+'" data-name="'+value.name+'" class="savedFilterRadio" /> '+value.name+'</div><div class="savedFilterActionsWrapper" data-id="'+value.id+'"><i class="fas fa-trash deleteFilter" title="Delete Filter" data-id="'+value.id+'"></i> <i class="fas fa-wrench editFilterValues" title="Update Filter to Currently Selected Filters" data-id="'+value.id+'"></i> <i class="fas fa-edit editFilterName" title="Edit Filter Name" data-id="'+value.id+'"></i></div></li>')
                });
                filterHTML = $('#filterList').html();
            }
        );
    }

    function processFilterDelete(result, id) {
        if( result ) {
            url = '/filters/delete';

            post_data = {
                'id' : id,
            };

            run_ajax(
                url,
                post_data,
                function(obj) {
                    message_pop(obj.response.Status, obj.response.Message, 2500);
                    loadSavedFilters();
                }
            );
        }
    }

    function processEditFilterValues( result, id ) {
        if(jQuery.isEmptyObject(filters) && searchTerm.length == 0) {
            message_pop('danger', 'A search term must be entered or one or more filters must be selected', 2500);
        } else {
            url = '/filters/updateFilter';
            

            post_data = {
                'id' : id,
                'filter': filters,
                'searchTerm': searchTerm
            };

            run_ajax(
                url,
                post_data,
                function(obj) {
                    message_pop(obj.response.Status, obj.response.Message, 2500);
                    loadSavedFilters();
                }
            );
        }
    }
// End Games Functions
