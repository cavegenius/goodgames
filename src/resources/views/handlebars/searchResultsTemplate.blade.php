<script type="text/x-handlebars-template" id="searchResultsTemplate">
    <div class="searchResultRow row">
        <div class="searchResultImage col-md-4">
            <img src="@{{{cover}}}" />
            <br/><br/>
            <button class="addToInventory btn btn-sm btn-outline-primary" data-name="@{{name}}">Add To Inventory</button>
            <br/><br/>
            <button class="addToWishlist btn btn-sm btn-outline-primary" data-name="@{{name}}">Add To Wishlist</button>
        </div>
        <div class="searchResultDetails col-md-8">
            <h3>@{{name}}</h3>
            @{{#if summary}}<p>Summary: @{{summary}}</p>@{{/if}}
            @{{#if genres}}<p> Genre(s): @{{#each genres}} @{{this}}; @{{/each}}</p>@{{/if}}
            @{{#if platforms}}<p> Platform(s): @{{#each platforms}} @{{this}}; @{{/each}}</p>@{{/if}}
        </div>
    </div>
</script>