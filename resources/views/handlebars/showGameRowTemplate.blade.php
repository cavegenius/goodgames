<script type="text/x-handlebars-template" id="showGameRowTemplate">
        <tr id="gamerow" data-id="@{{gameID}}">
                <td class="favorite">
                        <label class="favoriteIcon">
                                <input type="checkbox" class="favoriteBox" @{{#if favorite}} checked @{{/if}} />
                                <i class="far fa-heart unchecked"></i>
                                <i class="fa fa-heart checked"></i>
                        </label>
                </td>
                <td class="name">@{{ name }}</td>
                <td class="status">@{{ status }}</td>
                <td class="platform">@{{ platform }}</td>
                <td class="platformType">@{{ platformType }}</td>
                <td class="format">@{{ format }}</td>
                <td class="genre">@{{ genre }}</td>
                <td class="stratingatus">@{{{ rating }}}</td>
                <td><i class="fas fa-edit editSingleGame"></i> <i class="fas fa-trash deleteSingleGame"></i></td>
        </tr>
</script>
