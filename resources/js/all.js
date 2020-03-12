const Handlebars = require("handlebars");

// Document ready Actions
    $(document).ready( function() {
        // Variable Definitions
            let currentRoute = $('#currentRoute').val();
        // End Variable Definitions

        // Run any functions that need to load initial data sets
        if(currentRoute == 'games') {
            loadGames();
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
                
                    // Define our data object
                    //var context={};
                
                    // Pass our data to the template
                    var theCompiledHtml = theTemplate();
                
                    // Add the compiled html to the page
                    $( '#gamesTableBody').prepend(theCompiledHtml);
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
                    showEditGameFields(this);
                });

                //TODO: Use this code as base when saving the added game
                /*$( '#searchBar' ).keyup(function() {
                //$( '#search' ).click(function() {
                    name = $( '#searchBar' ).val();

                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/games/add', // This is the url we gave in the route
                        data: {
                            "_token": ctoken,
                            'name' : name,
                            'userId' : 1,
                            'igdbId' : 0,
                            'status' : 'None',
                            'platform' : 'Playstation 4',
                            'platformType' : 'Console',
                            'favorite' : 'No',
                            'rating' : '0',
                            'format' : 'Physical',
                            'notes' : 'kkl',
                            'owned' : 1,
                            'wishlist' : 0,
                            'backlog' : 0
                        }, // a JSON object to send back
                        success: function(response){ // What to do if we succeed
                            showSearchResults(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                });*/
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
            // html += '<div class="cssload-shaft10"></div>';
            html += '</div>';

        return html;
    }
// End Global Functions

// Games Functions
    function showSearchResults(obj) {
        console.log(obj);
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
        $( '#gamesTableBody').html('');
        if (obj.response.Status == 'Success') {
            $.each(obj.response['Games'], function( key, value ) {
                // Grab the template script
                var theTemplateScript = $("#showGameRowTemplate").html();
                        
                // Compile the template
                var theTemplate = Handlebars.compile(theTemplateScript);
                let rating = '';
                for (let step = 1; step <= 5; step++) {
                    if(value.rating >= step) {
                        rating += '<i class="fas fa-star" data-value="'+step+'"></i>';
                    } else {
                        rating += '<i class="far fa-star" data-value="'+step+'"></i>';
                    }
                }
                // Define our data object
                var context={
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
                $( '#gamesTableBody').append(theCompiledHtml);
            });
        } else if(obj.response.Status == 'Error') {
            $( '#gamesTableBody').append('<tr><td colspan="9" class="text-center">'+obj.response.Message+'</td></tr>');
        }
    }

    function showEditGameFields( row ) {
        let favorite = $(row).find('.favorite').text();
        let name = $(row).find('.name').text();
        let status = $(row).find('.status').text();
        let platform = $(row).find('.platform').text();
        let platformType = $(row).find('.platformType').text();
        let format = $(row).find('.format').text();
        let genre = $(row).find('.genre').text();
        let rating = $(row).find('.rating').text();
        let ratingHTML = '';
        for (let step = 1; step <= 5; step++) {
            if(value.rating >= step) {
                rating += '<i class="fas fa-star"></i>';
            } else {
                rating += '<i class="far fa-star"></i>';
            }
        }
        //$(this).find('.name').html('changed');
        var theTemplateScript = $("#editGameRowTemplate").html();

        // Compile the template
        var theTemplate = Handlebars.compile(theTemplateScript);

        // Define our data object
        var context={
            "id": 1,
            "favorite": favorite,
            "name": name,
            "status": status,
            "platform": platform,
            "platformType": platformType,
            "format": format,
            "genre": genre,
            "rating": ratingHTML,
        };

        // Pass our data to the template
        var theCompiledHtml = theTemplate(context);

        // Add the compiled html to the page
        $( row ).html(theCompiledHtml);
    }
// End Games Functions
