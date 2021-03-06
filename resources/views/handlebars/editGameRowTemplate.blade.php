<script type="text/x-handlebars-template" id="editGameRowTemplate">
        <td>
            <input type="hidden" name="id" value="@{{ id }}" />
            <label class="favoriteIcon">
                    <input type="checkbox" @{{#if favorite}} checked @{{/if}} />
                    <i class="far fa-heart unchecked"></i>
                    <i class="fa fa-heart checked"></i>
            </label>
        </td>
        @{{#if showRank}}
            <td><input type="text" name="rank" data-id="@{{ id }}" value="@{{ rank }}" size="2" /></td>
        @{{else}}
            <input type="hidden" name="rank" data-id="@{{ id }}" value="@{{ rank }}" size="2" />
        @{{/if}}
        <td><input type="text" name="name" data-id="@{{ id }}" value="@{{ name }}" /></td>
        <td>
            <select name="status" data-id ="@{{ id }}">
                @{{{status}}}
            </select>
        </td>
        <td class="addPlatformCol">
                <select name="platform">
                        @{{{platforms}}}
                </select>
        </td>
        <td>
            <select name="platformType" data-id ="@{{ id }}">
                @{{{platformType}}}
            </select>
        </td>
        <td>
            <select name="format" data-id ="@{{ id }}">
                @{{{format}}}
            </select>
        </td>
        <td>
            <select name="genre">
                @{{{genres}}}
            </select>    
        </td>
        <td>@{{{ rating }}}</td>
        <td><i class="fas fa-check saveSingleEditGame"></i> <i class="fas fa-ban cancelSingleEditGame"></i></td>
</script>
