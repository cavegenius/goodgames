const Handlebars = require("handlebars");

// Document ready Actions
    $(document).ready( function() {
        // Variable Definitions
        let currentRoute = $('#currentRoute').val();
        saveAll = false;
        saveError = false;
        // End Variable Definitions

        // Run any functions that need to load initial data sets
        if(currentRoute == 'games') {
            loadGames();
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

                $( '.listType' ).click(function() {
                    // Change the disgn of the buttons to highlight the correct list
                    $('#selectedList').val($(this).val());
                    $('.listType').each(function() {
                        $(this).removeClass('btn-outline-primary');
                        $(this).addClass('btn-outline-secondary');
                    });
                    $(this).removeClass('btn-outline-secondary');
                    $(this).addClass('btn-outline-primary');
                    $(this).blur();
                    // Now we load the new games list
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
                    loadGames();
                });

                $( document ).on( 'click', '.saveSingleAddGame', function(){
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
                        }
                    });

                    url = '/games/add';
                    post_data = {
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

                $( document ).on( 'click', '.saveSingleEditGame', function(){
                    var id;
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
                        }
                    });

                    url = '/games/update';
                    post_data = {
                        'id' : id,
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
                        message_pop( 'Success', 'There was an error saving one or more changes.', 2500);
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
            sortOrder: $('#sortOrder').val()
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

        //$( '#gamesTableBody').html('');
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
        let name = $(row).find('.name').text();
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
// End Games Functions
