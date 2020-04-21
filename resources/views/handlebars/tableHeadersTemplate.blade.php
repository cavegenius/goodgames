<script type="text/x-handlebars-template" id="tableHeadersTemplate">
    <th class="sortableCol @{{#if sortFavorite}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="favorite">Favorite</th>
    @{{#if showRank}} <th class="sortableCol @{{#if sortRank}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="rank">Rank</th> @{{/if}}
    <th class="sortableCol @{{#if sortName}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="name">Name</th>
    <th class="sortableCol @{{#if sortStatus}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="status">Status</th>
    <th class="sortableCol @{{#if sortPlatform}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="platform">Platform</th>
    <th class="sortableCol @{{#if sortPlatformType}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="platformType">Platform Type</th>
    <th class="sortableCol @{{#if sortFormat}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="format">Format</th>
    <th class="sortableCol @{{#if sortGenre}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="genre">Genre</th>
    <th class="sortableCol @{{#if sortRating}} 
    @{{#if asc}} 
        sortedAsc
    @{{else}}
        sortedDesc
    @{{/if}}
@{{/if}}" data-name="rating">Rating</th>
    <th>Actions</th>
</script>