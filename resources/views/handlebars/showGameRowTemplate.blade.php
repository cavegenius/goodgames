<script type="text/x-handlebars-template" id="showGameRowTemplate">
        <tr id="gamerow@{{gameID}}">
                <td class="favorite">
                @{{#if favorite}}
                        <i class="fa fa-heart favoriteIcon"></i>
                @{{else}}
                        <i class="far fa-heart favoriteIcon"></i>
                @{{/if}}
                </td>
                <td class="name">@{{ name }}</td>
                <td>@{{ status }}</td>
                <td>@{{ platform }}</td>
                <td>@{{ platformType }}</td>
                <td>@{{ format }}</td>
                <td>@{{ genre }}</td>
                <td>@{{ rating }}</td>
                <td></td>
        </tr>
</script>
