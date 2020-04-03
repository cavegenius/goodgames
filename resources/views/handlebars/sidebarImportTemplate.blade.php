<script type="text/x-handlebars-template" id="sidebarImportTemplate">
    <div id="testImport">
        <a href="/games/getImportTemplate" target="_blank">Download Template</a>
        <form name="importCSV" action="games/importCSV" method="POST" enctype="multipart/form-data">
            @csrf
            CSV File: <input type="file" name="csvFile"/><br />
            <input id="importCSVSubmit" class="btn btn-dark" type="submit" name="submit" value="Import">
        </form>
    </div>
</script>