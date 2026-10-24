<script type="text/x-handlebars-template" id="sidebarImportTemplate">
    <div id="importOptions">
        <div id="fileImportSection" class="importSection">
            <a href="/games/getImportTemplate" target="_blank">Download Template</a>
            <br />
            CSV File: <br />
            <input type="file" id="csvFile" name="csvFile"/>
            <input id="importCSVSubmit" class="btn btn-dark" type="button" name="submit" value="Import">
        </div>
        <div id="steamImportSection" class="importSection">
            Please enter your 17 digit Steam ID:<br />
            <input type="test" id="steamId" placeholder="Steam ID" name="steamId"/>
            <input id="steamImportSubmit"  class="btn btn-dark" type="button" name="submit" value="Import">
            <br />
            For information on how to find this ID please use the following link: <a href="https://support.steampowered.com/kb_article.php?ref=1558-QYAX-1965" target="blank">How can I find my SteamID?</a> 
            <div id="steamResults"></div>
        </div>
    </div>
</script>