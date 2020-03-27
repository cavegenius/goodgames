const Handlebars = require("handlebars");

// Document ready Actions
    $(document).ready( function() {
        // Variable Definitions
        let currentRoute = $('#currentRoute').val();
        // End Variable Definitions

        // Run any functions that need to load initial data sets
        if(currentRoute == 'games') {
            loadGames();
            load_platform_list();
        }

        // Event Actions
            // Games Actions
                //$( '#searchBar' ).keyup(function() {
                $( '#search' ).click(function() {
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

                    // Define our data object
                    var context={
                        "wishlist": isWishlist,
                        "platforms": platformHTML
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

                $(document).on('dblclick', '#gamerow', function() {
                    // Show the bulk options
                    $( '.add-edit-options' ).removeClass('hide-on-load');

                    showEditGameFields(this);
                });

                $(document).on('click', '.editSingleGame', function() {
                    // Show the bulk options
                    $( '.add-edit-options' ).removeClass('hide-on-load');

                    showEditGameFields($(this).closest('tr'));
                });

                $( document ).on( 'click', '.cancelSingleAddGame, .cancelSingleEditGame', function(){
                    $(this).closest('tr').remove();
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
                    var owned;

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
                                owned = $(this).val() != 'wishlist' ? 1 : 0;
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
                        'notes' : notes,
                        'owned' : owned
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
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
                    var owned;

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
                                owned = $(this).val() != 'wishlist' ? 1 : 0;
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
                        'notes' : notes,
                        'owned' : owned
                    };

                    run_ajax(
                        url,
                        post_data,
                        function(obj) {
                            loadGames();
                        }
                    );
                    $(this).closest('tr').remove();
                });

                $(document).on('click', '.cancelAll', function() {
                    $( '#gamesTableBody').find('.cancelSingleEditGame, .cancelSingleAddGame').each(function() {
                        $(this).click();
                    });
                });

                $(document).on('click', '.saveAll', function() {
                    $( '#gamesTableBody').find('.saveSingleEditGame, .saveSingleAddGame').each(function() {
                        $(this).click();
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
// End Global Functions

// Games Functions
    function showSearchResults(obj) {
        
    }

    function loadGames() {
        url = '/games/showList';
        post_data = {
            list: $('#selectedList').val()
        }

        run_ajax(
            url,
            post_data,
            showGameList
        );
    }

    function showGameList(obj) {
        let savedRows = [];
        $( '#gamesTableBody').find('tr').each(function() {
            if(!$(this).find('.saveSingleAddGame, .saveSingleEditGame').length) {
                $(this).remove();
            } else {
                // Check if it is editing a game if so:
                // Save the HTML of this row with an identified based on the id 
                // Then during the loop later writing the rows write this code instead of the template 
                if($(this).find('.saveSingleEditGame').length) {
                    savedRows[$(this).data('id')] = $(this).html();
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
                        rating += '<label class="ratingStar"><input type="checkbox" checked value="'+step+'" /><i class="far fa-star unchecked"></i><i class="fas fa-star checked"></i></label>';
                    } else {
                        rating += '<label class="ratingStar"><input type="checkbox" value="'+step+'" /><i class="far fa-star unchecked"></i><i class="fas fa-star checked"></i></label>';
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
        statusHTML += '<option value="Unbeatable" '+(status == 'Unbeatable' ? 'selected' : '')+'>Unbeatable</option>';
        statusHTML += '<option value="Abandoned" '+(status == 'Abandoned' ? 'selected' : '')+'>Abandoned</option>';
        statusHTML += '<option value="Wont Play" '+(status == 'Wont Play' ? 'selected' : '')+'>Wont Play</option>';

        let platformHTML = '';
        $.each(platforms, function( key, value ) {
            platformHTML += '<option value="'+value+'" '+(platform == value ? 'selected' : '')+'>'+value+'</option>';
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
            "genre": genre,
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
// End Games Functions
