<script type="text/x-handlebars-template" id="addGameRowTemplate">
    <tr id="addGameRow" class="changeActive">
        <td>
                <label class="favoriteIcon">
                        <input type="checkbox" name="favorite" />
                        <i class="far fa-heart unchecked"></i>
                        <i class="fa fa-heart checked"></i>
                </label>
        </td>
        @{{#if rank}}
                <td>
                    <input type="text" name="rank" value="" size="2" /> 
                </td>
        @{{else}}
                <input type="hidden" name="rank" value="0" size="2" />
        @{{/if}}
        <td>
                <input type="text" name="name" value="@{{#if name}} @{{name}} @{{/if}}" />
        </td>
        <td>
                <select name="status">
                        <option value="None" selected>None</option>
                        <option value="Might Play">Might Play</option>
                        <option value="Backlog" @{{#if backlog}} selected @{{/if}}>Backlog</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Wishlist" @{{#if wishlist}} selected @{{/if}}>Wishlist</option>
                        <option value="Paused">Paused</option>
                        <option value="Replayable">Replayable</option>
                        <option value="Abandoned">Abandoned</option>
                        <option value="Wont Play">Wont Play</option>
                </select>
        </td>
        <td class="addPlatformCol">
                <select name="platform">
                        @{{{platforms}}}
                </select>
        </td>
        <td>
                <select name="platformType">
                        <option value="Other" selected>Other</option>
                        <option value="PC">PC</option>
                        <option value="Console">Console</option>
                </select>
        </td>
        <td>
                <select name="format">
                        <option value="Not Set" selected>Not Set</option>
                        <option value="Physical">Physical</option>
                        <option value="Digital">Digital</option>
                </select>
        </td>
        <td>
                <select name="genre">
                        @{{{genres}}}
                </select>
        </td>
        <td>
                <label class="ratingStar">
                        <input type="checkbox" name="rating" value="1">
                        <i class="far fa-star unchecked"></i>
                        <i class="fas fa-star checked"></i>
                </label>
                <label class="ratingStar">
                        <input type="checkbox" name="rating" value="2">
                        <i class="far fa-star unchecked"></i>
                        <i class="fas fa-star checked"></i>
                </label>
                <label class="ratingStar">
                        <input type="checkbox" name="rating" value="3">
                        <i class="far fa-star unchecked"></i>
                        <i class="fas fa-star checked"></i>
                </label>
                <label class="ratingStar">
                        <input type="checkbox" name="rating" value="4">
                        <i class="far fa-star unchecked"></i>
                        <i class="fas fa-star checked"></i>
                </label>
                <label class="ratingStar">
                        <input type="checkbox" name="rating" value="5">
                        <i class="far fa-star unchecked"></i>
                        <i class="fas fa-star checked"></i>
                </label>
        </td>
        <td><i class="fas fa-check saveSingleAddGame"></i> <i class="fas fa-ban cancelSingleAddGame"></i></td>
    </tr>
  </script>


  