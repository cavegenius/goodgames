<script type="text/x-handlebars-template" id="editGameRowTemplate">
        <td>
            @{{#if favorite}}
                <i class="fa fa-heart favoriteIcon"></i>
            @{{else}}
                <i class="far fa-heart favoriteIcon"></i>
            @{{/if}}
        </td>
        <td><input type="text" name="name" data-id="@{{ id }}" value="@{{ name }}" /></td>
        <td>
            <select name="status" data-id="@{{ id }}">
                <option value="None"></option>
                <option value=""></option>
            </select>
        </td>
        <td><input type="text" name="" data-id ="@{{ id }}" value="@{{ name }}" /></td>
        <td><input type="text" name="name" data-id ="@{{ id }}" value="@{{ name }}" /></td>
        <td><input type="text" name="name" data-id ="@{{ id }}" value="@{{ name }}" /></td>
        <td><input type="text" name="name" data-id ="@{{ id }}" value="@{{ name }}" /></td>
        <td><input type="text" name="genre" data-id ="@{{ id }}" value="@{{ name }}" /></td>
        <td>@{{{ rating }}}</td>
</script>
