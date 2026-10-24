import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { JSDOM } from 'jsdom';
import { parse, compileScript, compileTemplate } from '@vue/compiler-sfc';

const componentPath = new URL('../../src/resources/js/components/GamesApp.vue', import.meta.url);

async function compileGamesApp() {
    const filename = componentPath.pathname;
    const source = await readFile(componentPath, 'utf8');
    const { descriptor, errors: parseErrors } = parse(source, { filename });
    assert.deepEqual(parseErrors, [], 'Vue component should parse');
    const script = compileScript(descriptor, { id: 'games-app-test', genDefaultAs: '__sfc__' });
    const template = compileTemplate({
        filename,
        id: 'games-app-test',
        source: descriptor.template.content,
        compilerOptions: { bindingMetadata: script.bindings },
    });
    assert.deepEqual(template.errors, [], 'Vue template should compile');

    const filterData = JSON.stringify(JSON.parse(await readFile(new URL('../../src/resources/js/data/filter-options.json', import.meta.url), 'utf8')));
    const scriptCode = script.content
        .replace("from 'axios'", `from ${JSON.stringify(import.meta.resolve('axios'))}`)
        .replaceAll("from 'vue'", `from ${JSON.stringify(import.meta.resolve('vue'))}`)
        .replace("import filterOptions from '../data/filter-options.json';", `const filterOptions = ${filterData};`);
    const templateCode = template.code
        .replaceAll('from "vue"', `from ${JSON.stringify(import.meta.resolve('vue'))}`)
        .replace('export function render', 'function render');
    const moduleCode = `${scriptCode}\n${templateCode}\n__sfc__.render = render;\nexport default __sfc__;`;
    return import(`data:text/javascript;base64,${Buffer.from(moduleCode).toString('base64')}`);
}

async function mountApp() {
    const dom = new JSDOM('<!doctype html><html><head><meta name="csrf-token" content="test-token"></head><body><div id="games-app"></div></body></html>', {
        url: 'http://localhost/',
        pretendToBeVisual: true,
    });
    globalThis.window = dom.window;
    globalThis.document = dom.window.document;
    globalThis.FormData = dom.window.FormData;
    globalThis.SVGElement = dom.window.SVGElement;
    globalThis.Element = dom.window.Element;
    globalThis.Node = dom.window.Node;
    globalThis.Document = dom.window.Document;
    const { createApp, nextTick } = await import('vue');
    const axiosModule = await import('axios');
    const axios = axiosModule.default;
    const requests = [];
    const openCalls = [];
    axios.post = (url, data, config) => new Promise((resolve, reject) => requests.push({
        url, data, config,
        resolve: body => resolve({ data: body }),
        reject,
    }));
    dom.window.open = (...args) => { openCalls.push(args); };
    dom.window.confirm = () => true;
    const { default: GamesApp } = await compileGamesApp();
    createApp(GamesApp).mount(dom.window.document.querySelector('#games-app'));

    async function flush() {
        await nextTick();
        await new Promise(resolve => setImmediate(resolve));
        await nextTick();
    }
    function take(url) {
        const index = requests.findIndex(request => request.url === url);
        assert.notEqual(index, -1, `expected request to ${url}; saw ${requests.map(request => request.url).join(', ')}`);
        return requests.splice(index, 1)[0];
    }
    async function respond(url, body) {
        const request = take(url);
        assert.equal(request.data instanceof FormData, false, 'JSON posts should use regular request data');
        assert.equal(request.data._token, 'test-token');
        request.resolve(body);
        await flush();
        return request;
    }
    function click(selector) {
        const element = dom.window.document.querySelector(selector);
        assert.ok(element, `expected element ${selector}`);
        element.click();
        return element;
    }
    function input(selector, value) {
        const element = dom.window.document.querySelector(selector);
        assert.ok(element, `expected element ${selector}`);
        element.value = value;
        element.dispatchEvent(new dom.window.Event('input', { bubbles: true }));
        return element;
    }
    return { dom, requests, take, respond, flush, click, input, openCalls };
}

test('Vue game library preserves the list, editing, filtering, and utility workflows', async t => {
    const app = await mountApp();
    const { dom, respond, take, flush, click, input, openCalls } = app;
    const page = dom.window.document;

    await t.test('loads and renders games and catalogs', async () => {
        const initialList = take('/games/showList');
        assert.equal(initialList.data.list, 'all');
        assert.equal(initialList.data.sortCol, 'name');
        assert.deepEqual(JSON.parse(initialList.data.filtered).status, []);
        initialList.resolve({ Status: 'Success', Games: [{ id: 12, name: 'Hades', status: 'Completed', platform: 'Steam', platformType: 'PC', format: 'Digital', genre: 'Roguelike', favorite: 1, rating: 5, rank: 0 }] });
        take('/filters/filterList').resolve({ Status: 'Error', Message: 'No Saved Filters' });
        take('/games/get_platform_list').resolve({ Platforms: ['Steam', 'Switch'] });
        take('/games/get_genre_list').resolve({ Genres: ['Roguelike', 'Indie'] });
        await flush();
        assert.equal(page.querySelector('#gamesTableBody tr[data-id="12"] .name').textContent, 'Hades');
        assert.equal(page.querySelectorAll('#gamesTableBody .favoriteBox:checked').length, 1);
        assert.equal(page.querySelectorAll('#gamesTableBody .ratingBox:checked').length, 5);
        assert.ok(page.querySelector('.filterItem[value="ZX Spectrum"]'));

        const themeSelect = page.querySelector('#themeSelect');
        assert.deepEqual([...themeSelect.options].map(option => option.value), ['light', 'dark', 'aqua']);
        themeSelect.value = 'dark';
        themeSelect.dispatchEvent(new dom.window.Event('change', { bubbles: true }));
        await flush();
        assert.equal(page.body.dataset.theme, 'dark');
        assert.equal(dom.window.localStorage.getItem('goodgames-theme'), 'dark');
        themeSelect.value = 'light';
        themeSelect.dispatchEvent(new dom.window.Event('change', { bubbles: true }));
        await flush();
        assert.equal(page.body.dataset.theme, 'light');
    });

    await t.test('switches lists, sorts, and searches for catalog games', async () => {
        click('.listType[value="backlog"]');
        const backlog = take('/games/showList');
        assert.equal(backlog.data.list, 'backlog');
        assert.equal(backlog.data.sortCol, 'rank');
        assert.deepEqual(JSON.parse(backlog.data.filtered).status, ['Backlog']);
        await flush();
        assert.equal(page.querySelector('.filterItem[name="status"]').disabled, true);
        backlog.resolve({ Status: 'Success', Games: [] });
        await flush();

        click('#gameTableHeading th:nth-child(3)');
        const sorted = take('/games/showList');
        assert.equal(sorted.data.sortCol, 'name');
        assert.equal(sorted.data.sortOrder, 'asc');
        sorted.resolve({ Status: 'Error', Message: 'No Games Found' });
        await flush();

        click('.btn-search');
        await flush();
        input('#searchBar', 'Hades');
        page.querySelector('#searchForm').dispatchEvent(new dom.window.Event('submit', { bubbles: true, cancelable: true }));
        const search = take('/games/search');
        assert.equal(search.data.name, 'Hades');
        search.resolve({ Games: [{ name: 'Hades', cover: '', summary: 'Action RPG', genres: ['Action'], platforms: ['PC'] }] });
        await flush();
        assert.match(page.querySelector('#searchResults').textContent, /Hades/);
        click('#searchResults .addToWishlist');
        await flush();
        assert.equal(page.querySelector('#gamesTableBody [name="name"]').value.trim(), 'Hades');
        assert.equal(page.querySelector('#gamesTableBody [name="status"]').value, 'Wishlist');
        click('#gamesTableBody .cancelSingleAddGame');
    });

    await t.test('filters inventory and saves, applies, renames, and deletes saved filters', async () => {
        click('.listType[value="all"]');
        await respond('/games/showList', { Status: 'Success', Games: [] });
        const completed = page.querySelector('.filterItem[value="Completed"]');
        completed.click();
        const filtered = take('/games/showList');
        assert.deepEqual(JSON.parse(filtered.data.filtered).status, ['Completed']);
        filtered.resolve({ Status: 'Success', Games: [] });
        await flush();

        click('.saveFilter');
        await flush();
        input('#filterName', 'Finished games');
        click('.saveFilterSubmit');
        const addFilter = take('/filters/add');
        assert.equal(addFilter.data.name, 'Finished games');
        assert.deepEqual(addFilter.data.filter.status, ['Completed']);
        addFilter.resolve({ Status: 'Success', Message: 'Saved' });
        await flush();
        await respond('/filters/filterList', { Status: 'Success', Filters: [{ id: 31, name: 'Finished games' }] });

        const savedRadio = page.querySelector('.savedFilterRadio[value="31"]');
        savedRadio.click();
        const apply = take('/filters/apply');
        assert.equal(apply.data.id, 31);
        apply.resolve({ Status: 'Success', Filter: { filter: JSON.stringify({ status: ['Completed'] }), searchTerm: '' } });
        await flush();
        const appliedGames = take('/games/showList');
        assert.deepEqual(JSON.parse(appliedGames.data.filtered).status, ['Completed']);
        appliedGames.resolve({ Status: 'Success', Games: [] });
        await flush();

        input('#inventorySearch', 'Portal');
        const searchedInventory = take('/games/showList');
        assert.equal(searchedInventory.data.searchTerm, 'Portal');
        searchedInventory.resolve({ Status: 'Success', Games: [] });
        await flush();

        click('.editFilterName');
        await flush();
        input('.updateNameField', 'Done');
        click('.saveEditFilterName');
        const rename = take('/filters/updateName');
        assert.equal(rename.data.name, 'Done');
        rename.resolve({ Status: 'Success', Message: 'Renamed' });
        await flush();
        await respond('/filters/filterList', { Status: 'Success', Filters: [{ id: 31, name: 'Done' }] });

        dom.window.confirm = () => true;
        click('.deleteFilter');
        const deletion = take('/filters/delete');
        assert.equal(deletion.data.id, 31);
        deletion.resolve({ Status: 'Success', Message: 'Deleted' });
        await flush();
        await respond('/filters/filterList', { Status: 'Error', Message: 'No Saved Filters' });
    });

    await t.test('adds, edits, favorites, rates, deletes, and cancels games', async () => {
        click('.listType[value="all"]');
        const list = take('/games/showList');
        list.resolve({ Status: 'Success', Games: [] });
        await flush();
        click('.addGame');
        await flush();
        input('#gamesTableBody [name="name"]', 'New Game');
        for (const [selector, value] of [['status','Completed'],['platform','Steam'],['platformType','PC'],['format','Digital'],['genre','Indie']]) {
            const element = page.querySelector(`#gamesTableBody [name="${selector}"]`);
            element.value = value;
            element.dispatchEvent(new dom.window.Event('change', { bubbles: true }));
        }
        page.querySelector('#gamesTableBody [name="favorite"]').click();
        page.querySelector('#gamesTableBody [name="rating"][value="4"]').click();
        click('.saveSingleAddGame');
        const add = take('/games/add');
        assert.deepEqual({ name: add.data.name, status: add.data.status, platform: add.data.platform, platformType: add.data.platformType, format: add.data.format, favorite: add.data.favorite, rating: add.data.rating, genre: add.data.genre },
            { name: 'New Game', status: 'Completed', platform: 'Steam', platformType: 'PC', format: 'Digital', favorite: 1, rating: 4, genre: 'Indie' });
        add.resolve({ Status: 'Success', Message: 'Added' });
        await flush();
        take('/games/showList').resolve({ Status: 'Success', Games: [{ id: 7, name: 'Portal 2', status: 'Completed', platform: 'Steam', platformType: 'PC', format: 'Digital', genre: 'Puzzle', favorite: 1, rating: 3, rank: 0 }] });
        await flush();

        click('.editSingleGame');
        await flush();
        assert.equal(page.querySelector('#gamesTableBody [name="favorite"]').checked, true);
        input('#gamesTableBody [name="name"]', 'Portal 2 Updated');
        click('.saveSingleEditGame');
        const update = take('/games/update');
        assert.equal(update.data.id, 7);
        assert.equal(update.data.name, 'Portal 2 Updated');
        assert.equal(update.data.favorite, 1);
        update.resolve({ Status: 'Success', Message: 'Updated' });
        await flush();

        take('/games/showList').resolve({ Status: 'Success', Games: [{ id: 7, name: 'Portal 2 Updated', status: 'Completed', platform: 'Steam', platformType: 'PC', format: 'Digital', genre: 'Puzzle', favorite: 1, rating: 3, rank: 0 }] });
        await flush();
        click('#gamesTableBody .editSingleGame');
        await flush();
        input('#gamesTableBody [name="name"]', 'Portal 2 Bulk Edit');
        click('.addGame');
        await flush();
        input('#gamesTableBody tr:first-child [name="name"]', 'Bulk Add');
        click('.saveAll');
        const bulkAdd = take('/games/add');
        const bulkUpdate = take('/games/update');
        assert.equal(bulkAdd.data.name, 'Bulk Add');
        assert.equal(bulkUpdate.data.name, 'Portal 2 Bulk Edit');
        bulkAdd.resolve({ Status: 'Success', Message: 'Added' });
        bulkUpdate.resolve({ Status: 'Success', Message: 'Updated' });
        await flush();
        take('/games/showList').resolve({ Status: 'Success', Games: [] });
        await flush();

        click('.listType[value="backlog"]');
        take('/games/showList').resolve({ Status: 'Success', Games: [{ id: 9, name: 'Celeste', status: 'Backlog', platform: 'Steam', platformType: 'PC', format: 'Digital', genre: 'Indie', favorite: 0, rating: 0, rank: 2 }] });
        await flush();
        click('#gamesTableBody .favoriteBox');
        const favorite = take('/games/update');
        assert.equal(favorite.data.favorite, 1);
        favorite.resolve({ Status: 'Success' });
        await flush();
        await respond('/games/showList', { Status: 'Success', Games: [{ id: 9, name: 'Celeste', status: 'Backlog', platform: 'Steam', platformType: 'PC', format: 'Digital', genre: 'Indie', favorite: 1, rating: 0, rank: 2 }] });
        click('#gamesTableBody .ratingBox[value="4"]');
        const rating = take('/games/update');
        assert.equal(rating.data.rating, 4);
        rating.resolve({ Status: 'Success' });
        await flush();
        await respond('/games/showList', { Status: 'Success', Games: [{ id: 9, name: 'Celeste', status: 'Backlog', platform: 'Steam', platformType: 'PC', format: 'Digital', genre: 'Indie', favorite: 1, rating: 4, rank: 2 }] });
        click('#gamesTableBody .deleteSingleGame');
        await flush();
        click('#gamesTableBody .saveSingleDeleteGame');
        const deletion = take('/games/delete');
        assert.equal(deletion.data.id, 9);
        deletion.resolve({ Status: 'Success', Message: 'Deleted' });
        await flush();

        click('.addGame');
        await flush();
        click('.cancelAll');
        await flush();
        assert.equal(page.querySelector('#gamesTableBody .saveSingleAddGame'), null);
    });

    await t.test('supports CSV and Steam imports, feedback, export, and validation feedback', async () => {
        click('.btn-import');
        await flush();
        const file = new dom.window.File(['name,status'], 'games.csv', { type: 'text/csv' });
        Object.defineProperty(page.querySelector('#csvFile'), 'files', { configurable: true, value: [file] });
        click('#importCSVSubmit');
        const csv = take('/games/importCSV');
        assert.equal(csv.data.get('_token'), 'test-token');
        assert.equal(csv.data.get('csvFile').name, 'games.csv');
        csv.resolve({ Status: 'Success', Message: 'Imported' });
        await flush();
        take('/games/showList').resolve({ Status: 'Success', Games: [] });
        await flush();

        input('#steamId', '76561198000000000');
        click('#steamImportSubmit');
        const steam = take('/games/steamImport');
        assert.equal(steam.data.steamId, '76561198000000000');
        steam.resolve({ Games: ['Portal 2'] });
        await flush();
        take('/games/showList').resolve({ Status: 'Success', Games: [] });
        await flush();

        click('.btn-feedback');
        await flush();
        input('#feedbackName', 'Dev User');
        input('#feedbackEmail', 'dev@example.test');
        input('#feedbackComments', 'A useful note');
        page.querySelector('#feedbackForm').dispatchEvent(new dom.window.Event('submit', { bubbles: true, cancelable: true }));
        const feedback = take('/feedback/submit');
        assert.deepEqual({ name: feedback.data.name, email: feedback.data.email, comments: feedback.data.comments },
            { name: 'Dev User', email: 'dev@example.test', comments: 'A useful note' });
        feedback.resolve({ Status: 'Success', Message: 'Sent' });
        await flush();
        click('.btn-export');
        assert.deepEqual(openCalls, [['/games/exportCSV', '_blank']]);

        page.querySelector('.filterItem[value="Completed"]').click();
        const filtered = take('/games/showList');
        filtered.resolve({ Status: 'Success', Games: [] });
        await flush();
        click('.saveFilter');
        await flush();
        click('.saveFilterSubmit');
        await flush();
        assert.match(page.querySelector('#messageBox').textContent, /Filter Name is Required/);
    });

    dom.window.close();
});
