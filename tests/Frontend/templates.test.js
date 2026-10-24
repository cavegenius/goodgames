import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import Handlebars from 'handlebars';

const viewRoot = new URL('../../src/resources/views/handlebars/', import.meta.url);

async function template(name, id) {
    const source = await readFile(new URL(`${name}.blade.php`, viewRoot), 'utf8');
    const escapedBladeExpressions = source.replaceAll('@{{', '{{');
    const match = escapedBladeExpressions.match(new RegExp(`<script[^>]*id="${id}"[^>]*>([\\s\\S]*?)<\\/script>`));
    assert.ok(match, `expected template ${id} in ${name}.blade.php`);
    return Handlebars.compile(match[1]);
}

test('add game form selects the requested queue and includes available catalog options', async () => {
    const render = await template('addGameRowTemplate', 'addGameRowTemplate');
    const html = render({ wishlist: true, backlog: false, rank: 2, platforms: '<option>Steam</option>', genres: '<option>Indie</option>' });

    assert.match(html, /name="rank" value="2"/);
    assert.match(html, /value="Wishlist"\s+selected/);
    assert.match(html, /<option>Steam<\/option>/);
    assert.match(html, /<option>Indie<\/option>/);
    assert.match(html, /name="favorite"/);
});

test('display row includes game fields, favorite state, rank, and edit/delete actions', async () => {
    const render = await template('showGameRowTemplate', 'showGameRowTemplate');
    const html = render({
        gameID: 42, favorite: true, showRank: true, rank: 3, name: 'Celeste',
        status: 'Backlog', platform: 'Steam', platformType: 'PC', format: 'Digital',
        genre: 'Indie', ratingValue: 5, rating: '★★★★★',
    });

    assert.match(html, /data-id="42"/);
    assert.match(html, /class="favoriteBox"\s+checked/);
    assert.match(html, /class="rank">3<\/td>/);
    for (const value of ['Celeste', 'Backlog', 'Steam', 'PC', 'Digital', 'Indie']) {
        assert.ok(html.includes(value), `row should render ${value}`);
    }
    assert.match(html, /editSingleGame/);
    assert.match(html, /deleteSingleGame/);
});

test('search results render metadata and both inventory actions with escaped game names', async () => {
    const render = await template('searchResultsTemplate', 'searchResultsTemplate');
    const html = render({
        name: '<script>alert(1)</script>', cover: 'https://example.test/cover.jpg',
        summary: 'A short description', genres: ['Adventure', 'Indie'], platforms: ['PC'],
    });

    assert.match(html, /&lt;script&gt;alert\(1\)&lt;\/script&gt;/);
    assert.doesNotMatch(html, /<script>alert\(1\)<\/script>/);
    assert.match(html, /Add To Inventory/);
    assert.match(html, /Add To Wishlist/);
    assert.match(html, /A short description/);
    assert.match(html, /Adventure;.*Indie;/s);
    assert.match(html, /PC;/);
});

test('editing row exposes all editable game fields and preserves supplied selected options', async () => {
    const render = await template('editGameRowTemplate', 'editGameRowTemplate');
    const html = render({
        id: 7, favorite: false, showRank: false, rank: 0, name: 'Portal 2',
        status: '<option selected>Completed</option>', platforms: '<option selected>Steam</option>',
        platformType: '<option selected>PC</option>', format: '<option selected>Digital</option>',
        genres: '<option selected>Puzzle</option>', rating: '<input name="rating" class="ratingBox" value="5" />',
    });

    for (const field of ['id="7"', 'name="name"', 'name="status"', 'name="platform"', 'name="platformType"', 'name="format"', 'name="genre"']) {
        assert.ok(html.includes(field), `edit row should contain ${field}`);
    }
    assert.match(html, /Portal 2/);
    assert.match(html, /<option selected>Steam<\/option>/);
    assert.match(html, /class="ratingBox" value="5"/);
});

test('sidebar templates expose search, CSV import, Steam import, and feedback controls', async () => {
    const search = await template('sidebarSearchTemplate', 'sidebarSearchTemplate');
    const importForm = await template('sidebarImportTemplate', 'sidebarImportTemplate');
    const feedback = await template('feedbackFormTemplate', 'feedbackFormTemplate');

    assert.match(search({}), /id="searchBar"/);
    assert.match(search({}), /id="searchResults"/);
    assert.match(importForm({}), /id="csvFile"/);
    assert.match(importForm({}), /id="importCSVSubmit"/);
    assert.match(importForm({}), /id="steamId"/);
    assert.match(importForm({}), /id="steamImportSubmit"/);
    assert.match(feedback({}), /id="feedbackName"/);
    assert.match(feedback({}), /id="feedbackEmail"/);
    assert.match(feedback({}), /id="feedbackComments"/);
    assert.match(feedback({}), /id="feedbackSubmit"/);
});

test('table headers switch rank visibility and indicate active sort direction', async () => {
    const render = await template('tableHeadersTemplate', 'tableHeadersTemplate');
    const ranked = render({ showRank: true, sortRank: true, asc: true, sortName: false });
    const unranked = render({ showRank: false, sortName: true, asc: false });

    assert.match(ranked, /data-name="rank"/);
    assert.match(ranked, /sortedAsc/);
    assert.doesNotMatch(unranked, /data-name="rank"/);
    assert.match(unranked, /sortedDesc/);
});

test('frontend event handlers are wired to the PHP routes for the visible actions', async () => {
    const source = await readFile(new URL('../../src/resources/js/all.js', import.meta.url), 'utf8');
    for (const endpoint of [
        '/games/search', '/games/showList', '/games/add', '/games/update',
        '/games/delete', '/games/importCSV', '/games/steamImport', '/games/exportCSV',
        '/games/get_platform_list', '/games/get_genre_list', '/filters/add', '/filters/delete',
        '/filters/apply', '/filters/updateName', '/filters/updateFilter', '/filters/filterList',
        '/feedback/submit',
    ]) {
        assert.ok(source.includes(endpoint), `frontend should call ${endpoint}`);
    }
    for (const action of ['.addGame', '.listType', '.saveAll', '.cancelAll', '.btn-import', '.btn-export', '.btn-feedback']) {
        assert.ok(source.includes(action), `frontend should wire ${action}`);
    }
});
