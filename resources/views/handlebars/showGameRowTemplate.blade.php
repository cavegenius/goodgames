<script type="text/x-handlebars-template" id="showGameRowTemplate">
        <tr id="gamerow@{{gameID}}">
                <td>
                @{{#if favorite}}
                        <i class="fa fa-heart favoriteIcon"></i>
                @{{else}}
                        <i class="far fa-heart favoriteIcon"></i>
                @{{/if}}
                </td>
                <td>@{{ name }}</td>
                <td>@{{ status }}</td>
                <td>@{{ platform }}</td>
                <td>@{{ platformType }}</td>
                <td>@{{ format }}</td>
                <td>@{{ genre }}</td>
                <td>@{{ rating }}</td>
                <td></td>
        </tr>
</script>
