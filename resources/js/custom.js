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

        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: '/games/search', // This is the url we gave in the route
            data: {
                "_token": ctoken,
                'name' : name
            }, // a JSON object to send back
            success: function(response){ // What to do if we succeed
                showSearchResults(response);
            },
            error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
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
