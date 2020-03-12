<script type="text/x-handlebars-template" id="addGameRowTemplate">
    <tr id="addGameRow">
        <td>
                <i class="far fa-heart favoriteIcon"></i>
        </td>
        <td>
                <input type="text" name="name" value="" />
        </td>
        <td>
        <select name="status">
                <option value="None" selected>None</option>
                <option value="Might Play">Might Play</option>
                <option value="Backlog">Backlog</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="Wishlist">Wishlist</option>
                <option value="Paused">Paused</option>
                <option value="Unbeatable">Unbeatable</option>
                <option value="Abandoned">Abandoned</option>
                <option value="Wont Play">Wont Play</option>
        </select>
        </td>
        <td><input type="text" name="platform" value="" /></td>
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
        <td><input type="text" name="genre" value="" /></td>
        <td>
                <i class="far fa-star" data-value="1"></i>
                <i class="far fa-star" data-value="2"></i>
                <i class="far fa-star" data-value="3"></i>
                <i class="far fa-star" data-value="4"></i>
                <i class="far fa-star" data-value="5"></i>
        </td>
        <td></td>
    </tr>
  </script>


$table->enum('status', ['Might Play','Backlog','In Progress','Completed','Wont Play','Abandoned','Unbeatable','Paused','Wishlist','None'])->default('None');
$table->string('platform',20)->default('');
$table->enum('platformType', ['PC', 'Console', 'Other']);
$table->boolean('favorite')->default(false);;
$table->enum('format', ['Physical', 'Digital', 'Not Set']);