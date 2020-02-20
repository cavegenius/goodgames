const Handlebars = require("handlebars");
// Expand text are on enter click
$(document)
    .one('focus.textarea', '.autogrow', function(){
        var savedValue = this.value;
        this.value = '';
        this.baseScrollHeight = this.scrollHeight;
        this.value = savedValue;
    })
    .on('input.textarea', '.autogrow', function(){
        var minRows = this.getAttribute('min-rows')|0, rows;
        var maxRows = this.getAttribute('max-rows')|5;
        rows = this.rows;

        $('.autogrow').on("keypress", function(e) {
            /* ENTER PRESSED*/
            if (e.keyCode == 13 && this.rows != maxRows) {
                rows += 1;
                //console.log(this.scrollHeight);
                this.rows = rows;
            }
        });
        
    });


// Document ready Actions
$(document).ready( function() {
    let ctoken = $('meta[name="csrf-token"]').attr('content');
    $( 'body' ).on( 'click', '#addIngredient', function(e) {
        e.preventDefault();
        
        let count = $( '#ingredientCount' ).val();
        count++;
        $( '#ingredientCount' ).val( count );
        var content = '<div class="form-row"><div class="col-2"><input class="form-control" placeholder="10" name="quantity'+count+'" type="text" value=""></div><div class="col-4"><input class="form-control unit" placeholder="Unit" name="unit'+count+'" type="text" value=""></div><div class="col-6"><input class="form-control" placeholder="Ingedient Name" name="itemName'+count+'" type="text" value=""></div></div>';
        $('#ingredients').append( content );
    });

    $( 'body' ).on( 'click', '#addStep', function(e) {
        e.preventDefault();
        
        let counts = $( '#stepCount' ).val();
        counts++;
        $( '#stepCount' ).val( counts );
        var contents = '<div class="form-row"><div class="col-12"><textarea class="form-control autogrow" min-rows="1" max-rows="5" rows="1" placeholder="Step Details" name="step'+counts+'" cols="50"></textarea></div></div>';
        $('#steps').append( contents );
    });

    //$( '#searchBar' ).keyup(function() {
    $( '#search' ).click(function() {
        name = $( '#searchBar' ).val();
        url = '/games/search';
        post_data = {
            "_token": ctoken,
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
        var context={
          "namething": "tis but a name"
        };
      
        // Pass our data to the template
        var theCompiledHtml = theTemplate(context);
      
        // Add the compiled html to the page
        $( '#gamesTableBody').prepend(theCompiledHtml);
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
});

//Function Definitions
function showSearchResults(obj) {
    console.log(obj);
}


// Global Functions
function run_ajax( url, data_obj, return_function, loader_message = false ) {
    if ( url != '' )
    {
        show_big_loader( loader_message );

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