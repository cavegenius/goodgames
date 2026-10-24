<script>
import axios from 'axios';
import { markRaw } from 'vue';
import filterOptions from '../data/filter-options.json';

const statuses = ['None', 'Might Play', 'Backlog', 'In Progress', 'Completed', 'Wishlist', 'Paused', 'Replayable', 'Abandoned', 'Wont Play'];
const platformTypes = ['Other', 'PC', 'Console'];
const formats = ['Not Set', 'Physical', 'Digital'];
const columns = [
    { key: 'favorite', label: 'Favorite' }, { key: 'rank', label: 'Rank', rankedOnly: true },
    { key: 'name', label: 'Name' }, { key: 'status', label: 'Status' }, { key: 'platform', label: 'Platform' },
    { key: 'platformType', label: 'Platform Type' }, { key: 'format', label: 'Format' },
    { key: 'genre', label: 'Genre' }, { key: 'rating', label: 'Rating' }, { key: 'actions', label: 'Actions' },
];

function newGame(list = 'all', name = '') {
    const game = {
        id: `new-${Date.now()}-${Math.random().toString(16).slice(2)}`,
        name,
        status: list === 'wishlist' ? 'Wishlist' : list === 'backlog' ? 'Backlog' : 'None',
        rank: 0,
        platform: '',
        platformType: 'Other',
        format: 'Not Set',
        genre: 'Not Set',
        favorite: false,
        rating: 0,
        notes: '',
        _isNew: true,
        _editing: true,
        _deleting: false,
    };
    game._draft = {
        name: game.name,
        status: game.status,
        rank: game.rank,
        platform: game.platform,
        platformType: game.platformType,
        format: game.format,
        genre: game.genre,
        favorite: game.favorite,
        rating: game.rating,
        notes: game.notes,
    };
    return game;
}

function normalize(response) {
    const body = response?.data ?? response;
    return typeof body === 'string' ? JSON.parse(body) : body;
}

export default {
    name: 'GamesApp',
    data() {
        return {
            api: markRaw(axios),
            filterOptions,
            statuses,
            platformTypes,
            formats,
            columns,
            list: 'all',
            filtersOpen: false,
            toolsOpen: false,
            sortCol: 'name',
            sortOrder: 'asc',
            filters: Object.fromEntries(Object.keys(filterOptions).map(key => [key, []])),
            searchTerm: '',
            games: [],
            pendingAdds: [],
            platforms: [],
            genres: [],
            savedFilters: [],
            selectedSavedFilter: null,
            filterName: '',
            showFilterName: false,
            editingFilterId: null,
            editingFilterName: '',
            rightPanel: '',
            searchName: '',
            searchResults: [],
            feedback: { name: '', email: '', comments: '' },
            feedbackOpen: false,
            steamId: '',
            steamResults: [],
            message: null,
            loading: false,
            savingAll: false,
            draggedId: null,
            filterSections: { saved: true, favorite: false, status: false, platform: false, platformType: false, format: false, genre: false, rating: false },
        };
    },
    computed: {
        hasActiveFilters() {
            return this.searchTerm.length > 0 || Object.values(this.filters).some(values => values.length > 0);
        },
        rows() {
            return [...this.pendingAdds, ...this.games];
        },
        showRank() {
            return this.list === 'wishlist' || this.list === 'backlog';
        },
        visibleColumns() {
            return this.columns.filter(column => !column.rankedOnly || this.showRank);
        },
        hasPendingChanges() {
            return this.pendingAdds.length > 0 || this.games.some(game => game._editing || game._deleting);
        },
    },
    mounted() {
        if (window.matchMedia?.('(min-width: 1800px)').matches) {
            this.filtersOpen = true;
            this.toolsOpen = true;
        } else if (window.matchMedia?.('(min-width: 1000px)').matches) {
            this.filtersOpen = true;
        }
        this.loadInitialData();
    },
    methods: {
        token() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },
        async post(url, data = {}, config = {}) {
            const payload = data instanceof FormData ? data : { ...data, _token: this.token() };
            if (data instanceof FormData && !data.has('_token')) payload.append('_token', this.token());
            const response = await this.api.post(url, payload, config);
            return normalize(response);
        },
        async loadInitialData() {
            await Promise.all([this.loadGames(), this.loadSavedFilters(), this.loadCatalogs()]);
        },
        async loadCatalogs() {
            try {
                const [platformResponse, genreResponse] = await Promise.all([
                    this.post('/games/get_platform_list'), this.post('/games/get_genre_list'),
                ]);
                this.platforms = platformResponse.Platforms ?? [];
                this.genres = genreResponse.Genres ?? [];
            } catch (error) {
                this.showError(error);
            }
        },
        filterPayload() {
            return Object.fromEntries(Object.entries(this.filters).map(([key, values]) => [key, [...values]]));
        },
        filterLabel(name) {
            return ({ favorite: 'Favorites', status: 'Status', platform: 'Platform', platformType: 'Platform Type', format: 'Format', genre: 'Genre', rating: 'Rating' })[name] ?? name;
        },
        filterOptionLabel(name, value) {
            if (name === 'favorite') return value === 'true' ? 'Favorited' : 'Not Favorited';
            if (name === 'rating') return `${value} ${value === '1' ? 'Star' : 'Stars'}`;
            return value;
        },
        clearFilterState() {
            this.filters = Object.fromEntries(Object.keys(filterOptions).map(key => [key, []]));
            this.searchTerm = '';
            this.selectedSavedFilter = null;
        },
        async loadGames() {
            this.loading = true;
            try {
                const response = await this.post('/games/showList', {
                    list: this.list,
                    sortCol: this.sortCol,
                    sortOrder: this.sortOrder,
                    filtered: JSON.stringify(this.filterPayload(), null, 1),
                    searchTerm: this.searchTerm,
                });
                if (response.Status === 'Success') {
                    const existingDrafts = new Map(this.games.filter(game => game._editing).map(game => [game.id, game._draft]));
                    this.games = (response.Games ?? []).map(game => ({ ...game, favorite: Boolean(Number(game.favorite)), _editing: false, _deleting: false, _draft: existingDrafts.get(game.id) ?? null }));
                    this.message = null;
                } else {
                    this.games = [];
                    this.message = response.Message ?? 'No Games Found';
                }
            } catch (error) {
                this.showError(error);
            } finally {
                this.loading = false;
            }
        },
        async loadSavedFilters() {
            try {
                const response = await this.post('/filters/filterList');
                this.savedFilters = response.Status === 'Success' ? response.Filters : [];
            } catch (error) {
                this.showError(error);
            }
        },
        async chooseList(list) {
            this.clearFilterState();
            this.list = list;
            if (this.showRank) {
                this.sortCol = 'rank';
                if (list === 'wishlist' || list === 'backlog') this.filters.status = [list === 'wishlist' ? 'Wishlist' : 'Backlog'];
            } else {
                this.sortCol = 'name';
            }
            await this.loadGames();
        },
        async sortBy(column) {
            if (column === 'actions') return;
            if (this.sortCol === column) this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            else { this.sortCol = column; this.sortOrder = 'asc'; }
            await this.loadGames();
        },
        async filtersChanged() {
            this.selectedSavedFilter = null;
            await this.loadGames();
        },
        async clearFilters() {
            this.filters = Object.fromEntries(Object.keys(filterOptions).map(key => [key, []]));
            this.searchTerm = '';
            this.selectedSavedFilter = null;
            this.list = 'all';
            this.sortCol = 'name';
            this.sortOrder = 'asc';
            await this.loadGames();
        },
        toggleFilterSection(name) {
            this.filterSections[name] = !this.filterSections[name];
        },
        toggleFilters() {
            this.filtersOpen = !this.filtersOpen;
            if (this.filtersOpen && window.innerWidth < 1200) this.toolsOpen = false;
        },
        toggleTools() {
            this.toolsOpen = !this.toolsOpen;
            if (this.toolsOpen && window.innerWidth < 1200) this.filtersOpen = false;
        },
        toggleSaveFilterForm() {
            this.showFilterName = !this.showFilterName;
            if (!this.showFilterName) this.filterName = '';
        },
        async saveFilter() {
            if (!this.filterName.trim()) return this.notify('danger', 'Filter Name is Required');
            if (!this.hasActiveFilters) return this.notify('danger', 'A search term must be entered or one or more filters must be selected');
            try {
                const response = await this.post('/filters/add', { name: this.filterName, filter: this.filterPayload(), searchTerm: this.searchTerm });
                this.notify(response.Status, response.Message);
                if (response.Status === 'Success') {
                    this.showFilterName = false;
                    this.filterName = '';
                    await this.loadSavedFilters();
                }
            } catch (error) { this.showError(error); }
        },
        async applySavedFilter(filter) {
            this.selectedSavedFilter = filter.id;
            try {
                const response = await this.post('/filters/apply', { id: filter.id });
                if (response.Status !== 'Success') return this.notify('danger', response.Message);
                const saved = response.Filter;
                const savedValues = typeof saved.filter === 'string' ? JSON.parse(saved.filter) : saved.filter;
                this.filters = Object.fromEntries(Object.keys(filterOptions).map(key => [key, savedValues?.[key] ?? []]));
                this.searchTerm = saved.searchTerm ?? '';
                await this.loadGames();
                this.notify('success', 'Filter Applied');
            } catch (error) { this.showError(error); }
        },
        beginRenameFilter(filter) {
            this.editingFilterId = filter.id;
            this.editingFilterName = filter.name;
        },
        async renameFilter(filter) {
            try {
                const response = await this.post('/filters/updateName', { id: filter.id, name: this.editingFilterName });
                this.notify(response.Status, response.Message);
                if (response.Status === 'Success') {
                    this.editingFilterId = null;
                    await this.loadSavedFilters();
                }
            } catch (error) { this.showError(error); }
        },
        async deleteFilter(filter) {
            if (!window.confirm('Are you sure you want to delete this filter?')) return;
            try {
                const response = await this.post('/filters/delete', { id: filter.id });
                this.notify(response.Status, response.Message);
                await this.loadSavedFilters();
            } catch (error) { this.showError(error); }
        },
        async updateSavedFilter(filter) {
            if (!window.confirm('Are you sure you want to update this filter?')) return;
            if (!this.hasActiveFilters) return this.notify('danger', 'A search term must be entered or one or more filters must be selected');
            try {
                const response = await this.post('/filters/updateFilter', { id: filter.id, filter: this.filterPayload(), searchTerm: this.searchTerm });
                this.notify(response.Status, response.Message);
                await this.loadSavedFilters();
            } catch (error) { this.showError(error); }
        },
        draftFor(game) {
            if (!game._draft) game._draft = { ...game };
            game._editing = true;
            game._deleting = false;
        },
        addGame(name = '', status = null) {
            const game = newGame(this.list, name);
            if (status) game._draft.status = status;
            if (this.showRank && this.games.length) game._draft.rank = Math.max(...this.games.map(row => Number(row.rank) || 0)) + 1;
            this.pendingAdds.unshift(game);
        },
        cancelGame(game) {
            if (game._isNew) this.pendingAdds = this.pendingAdds.filter(row => row.id !== game.id);
            else { game._editing = false; game._deleting = false; game._draft = null; }
        },
        gamePayload(game) {
            const source = game._draft ?? game;
            return {
                ...(game._isNew ? {} : { id: game.id }),
                name: source.name,
                rank: source.rank ?? 0,
                status: source.status,
                platform: source.platform,
                platformType: source.platformType,
                favorite: source.favorite ? 1 : 0,
                rating: source.rating ?? 0,
                format: source.format,
                genre: source.genre,
                notes: source.notes ?? '',
            };
        },
        async saveGame(game) {
            try {
                const response = await this.post(game._isNew ? '/games/add' : '/games/update', this.gamePayload(game));
                this.notify(response.Status, response.Message);
                if (response.Status === 'Success') {
                    this.pendingAdds = this.pendingAdds.filter(row => row.id !== game.id);
                    await this.loadGames();
                }
            } catch (error) { this.showError(error); }
        },
        async deleteGame(game) {
            try {
                const response = await this.post('/games/delete', { id: game.id });
                this.notify(response.Status, response.Message);
                if (response.Status === 'Success') await this.loadGames();
            } catch (error) { this.showError(error); }
        },
        async toggleFavorite(game) {
            game.favorite = !game.favorite;
            await this.updateField(game, { favorite: game.favorite ? 1 : 0 }, 'Updated Successfully');
        },
        async setRating(game, value) {
            const rating = Number(game.rating) === value ? 0 : value;
            game.rating = rating;
            await this.updateField(game, { rating }, 'Game Rating Updated');
        },
        async updateField(game, payload, successMessage) {
            try {
                const response = await this.post('/games/update', { id: game.id, ...payload });
                this.notify(response.Status === 'Success' ? 'success' : response.Status, response.Status === 'Success' ? successMessage : response.Message);
                if (response.Status === 'Success') await this.loadGames();
            } catch (error) { this.showError(error); }
        },
        async saveAll() {
            const changes = [...this.pendingAdds, ...this.games.filter(game => game._editing || game._deleting)];
            if (!changes.length) return;
            this.savingAll = true;
            const results = await Promise.allSettled(changes.map(game => {
                if (game._deleting) return this.post('/games/delete', { id: game.id });
                return this.post(game._isNew ? '/games/add' : '/games/update', this.gamePayload(game));
            }));
            const failures = results.filter(result => result.status === 'rejected' || result.value?.Status !== 'Success');
            this.notify(failures.length ? 'danger' : 'success', failures.length ? 'There was an error saving one or more changes.' : 'All changes Saved Successfully');
            this.pendingAdds = [];
            this.savingAll = false;
            await this.loadGames();
        },
        cancelAll() {
            this.pendingAdds = [];
            this.games.forEach(game => { game._editing = false; game._deleting = false; game._draft = null; });
        },
        async reorder(fromId, toId) {
            if (!this.showRank || fromId === toId) return;
            const rows = [...this.games];
            const from = rows.findIndex(game => String(game.id) === String(fromId));
            const to = rows.findIndex(game => String(game.id) === String(toId));
            if (from < 0 || to < 0) return;
            const [moved] = rows.splice(from, 1);
            rows.splice(to, 0, moved);
            this.games = rows.map((game, index) => ({ ...game, rank: index + 1 }));
            try {
                const response = await this.post('/games/update', { id: moved.id, rank: rows.indexOf(moved) + 1, status: this.list });
                if (response.Status !== 'Success') this.notify('danger', response.Message);
                await this.loadGames();
            } catch (error) { this.showError(error); }
        },
        startDrag(game) { this.draggedId = game.id; },
        dropOn(game) { this.reorder(this.draggedId, game.id); this.draggedId = null; },
        async searchGames() {
            if (!this.searchName.trim()) return;
            try {
                const response = await this.post('/games/search', { name: this.searchName });
                this.searchResults = response.Games ?? [];
            } catch (error) { this.showError(error); }
        },
        openPanel(panel) { this.rightPanel = panel; this.searchResults = []; },
        async importCsv() {
            const file = this.$refs.csvFile?.files?.[0];
            if (!file) return this.notify('danger', 'Choose a CSV file to import');
            const form = new FormData();
            form.append('csvFile', file);
            try {
                const response = await this.post('/games/importCSV', form);
                this.notify(response.Status, response.Message);
                await this.loadGames();
            } catch (error) { this.showError(error); }
        },
        async importSteam() {
            try {
                const response = await this.post('/games/steamImport', { steamId: this.steamId });
                this.steamResults = response.Games ?? [];
                this.notify('success', this.steamResults.length ? 'Steam import completed' : 'No New Games Added');
                await this.loadGames();
            } catch (error) { this.showError(error); }
        },
        exportCsv() { window.open('/games/exportCSV', '_blank'); },
        async submitFeedback() {
            try {
                const response = await this.post('/feedback/submit', this.feedback);
                this.feedbackOpen = false;
                this.notify(response.Status, response.Message);
            } catch (error) { this.showError(error); }
        },
        notify(type, text) {
            this.message = { type: type === 'Error' ? 'danger' : type.toLowerCase(), text };
            window.setTimeout(() => { this.message = null; }, 5000);
        },
        showError(error) {
            const status = error?.response?.status;
            const body = normalize(error?.response ?? {});
            if (status === 422 && body.errors) {
                this.notify('danger', Object.values(body.errors).flat().join(' '));
            } else if (status === 500) {
                this.notify('danger', 'An unexpected error has occurred. Please try again or reload the page.');
            } else if (body.Logout) {
                window.location.assign('/login');
            } else {
                this.notify('danger', body.Message ?? 'An unexpected error has occurred. Please try again.');
            }
        },
    },
};
</script>

<template>
    <div class="games-page" :class="{ 'filters-open': filtersOpen, 'tools-open': toolsOpen }">
            <button v-if="filtersOpen || toolsOpen" type="button" class="drawer-backdrop" aria-label="Close side panels" @click="filtersOpen = false; toolsOpen = false"></button>
            <Transition name="drawer-left">
            <aside v-show="filtersOpen" id="leftSidebar" class="sidebar-drawer sidebar-drawer-left filtersBar" aria-label="Filters">
                <div class="sidebar-sticky">
                    <div class="drawer-heading"><h2>Filters</h2><button type="button" class="drawer-close" aria-label="Close filters" @click="filtersOpen = false">×</button></div>
                    <button type="button" class="filterHeader" :aria-expanded="filterSections.saved" @click="filterSections.saved = !filterSections.saved">Saved Filters:</button>
                    <ul id="filterList" class="nav flex-column mb-2" v-show="filterSections.saved !== false">
                        <li v-for="filter in savedFilters" :key="filter.id" class="nav-item savedFilterItem">
                            <input type="radio" name="activateSavedFilter" class="savedFilterRadio" :aria-label="`Select ${filter.name}`" :value="filter.id" v-model="selectedSavedFilter" @change="applySavedFilter(filter)">
                            <input v-if="String(editingFilterId) === String(filter.id)" v-model="editingFilterName" class="updateNameField" aria-label="New filter name" @keyup.enter="renameFilter(filter)">
                            <span v-else>{{ filter.name }}</span>
                            <span class="savedFilterActionsWrapper">
                                <button v-if="String(editingFilterId) === String(filter.id)" type="button" class="saved-filter-action fas fa-check saveEditFilterName" aria-label="Save filter name" title="Save filter name" @click="renameFilter(filter)"></button>
                                <button v-else type="button" class="saved-filter-action fas fa-edit editFilterName" aria-label="Rename filter" title="Rename filter" @click="beginRenameFilter(filter)"></button>
                                <button type="button" class="saved-filter-action fas fa-wrench editFilterValues" aria-label="Update saved filter from current selections" title="Update Filter to Currently Selected Filters" @click="updateSavedFilter(filter)"></button>
                                <button type="button" class="saved-filter-action fas fa-trash deleteFilter" aria-label="Delete filter" title="Delete Filter" @click="deleteFilter(filter)"></button>
                            </span>
                        </li>
                        <li v-if="!savedFilters.length" class="text-muted">No saved filters</li>
                    </ul>
                    <div class="btn-group me-2 filterActions">
                        <button type="button" class="btn btn-sm btn-outline-secondary saveFilter" :disabled="!hasActiveFilters" @click="toggleSaveFilterForm">Save Filter</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary clearFilters" :disabled="!hasActiveFilters" @click="clearFilters">Clear Filters</button>
                    </div>
                    <div v-if="showFilterName" class="filterNameDiv">
                        <input v-model="filterName" type="text" id="filterName" placeholder="Filter Name" @keyup.enter="saveFilter">
                    </div>
                    <div v-if="showFilterName" class="btn-group me-2 saveFilterButtons">
                        <button type="button" class="btn btn-sm btn-outline-secondary saveFilterSubmit" @click="saveFilter">Submit</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary saveFilterCancel" @click="toggleSaveFilterForm">Cancel</button>
                    </div>
                    <div class="nav-item mt-3">Search Your Games:</div>
                    <input type="text" id="inventorySearch" v-model="searchTerm" @input="filtersChanged">
                    <template v-for="(values, name) in filterOptions" :key="name">
                        <button type="button" class="filterHeader" :aria-expanded="filterSections[name]" @click="toggleFilterSection(name)">{{ filterLabel(name) }}:</button>
                        <ul class="nav flex-column mb-2" :class="`${name}Filters`" v-show="filterSections[name]">
                            <li v-for="value in values" :key="value" class="nav-item">
                                <label>
                                    <input type="checkbox" class="filterItem" :name="name" :value="value" v-model="filters[name]"
                                        :disabled="name === 'status' && showRank" @change="filtersChanged">
                                    {{ filterOptionLabel(name, value) }}
                                </label>
                            </li>
                        </ul>
                    </template>
                </div>
            </aside>
            </Transition>

            <main role="main" id="mainDiv" class="games-main">
                <div v-if="message" id="messageBox" class="alert" :class="`alert-${message.type}`" role="alert">{{ message.text }}</div>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 id="mainHeading" class="h2">Games</h1>
                        <div class="btn-group me-2" role="group" aria-label="Panels">
                            <button type="button" class="btn btn-sm btn-outline-secondary" aria-controls="leftSidebar" :aria-expanded="filtersOpen" @click="toggleFilters">Filters</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" aria-controls="rightSidebar" :aria-expanded="toolsOpen" @click="toggleTools">Tools</button>
                        </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button v-for="item in [{value:'all',label:'All'},{value:'backlog',label:'Backlog'},{value:'wishlist',label:'Wishlist'},{value:'In Progress',label:'In Progress'}]" :key="item.value" type="button" class="btn btn-sm listType" :value="item.value" :class="list === item.value ? 'btn-outline-primary' : 'btn-outline-secondary'" @click="chooseList(item.value)">{{ item.label }}</button>
                        </div>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2"><button type="button" class="btn btn-sm btn-outline-secondary addGame" @click="addGame()">{{ list === 'backlog' ? 'Add to Backlog' : list === 'wishlist' ? 'Add to Wishlist' : 'Add Game' }}</button></div>
                        <div class="btn-group me-2" :class="{ 'hide-on-load': !hasPendingChanges }">
                            <button type="button" class="btn btn-sm btn-outline-secondary saveAll" :disabled="savingAll" @click="saveAll">Save All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary cancelAll" @click="cancelAll">Cancel All</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-feedback" @click="feedbackOpen = true">Beta Feedback</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="gamesTable" class="table table-striped table-sm games-table" :class="{ 'is-ranked': showRank }">
                        <colgroup><col v-for="column in visibleColumns" :key="column.key" :class="`column-${column.key}`"></colgroup>
                        <thead><tr id="gameTableHeading">
                            <th v-for="column in visibleColumns" :key="column.key" :class="{ sortableCol: column.key !== 'actions', sortedAsc: sortCol === column.key && sortOrder === 'asc', sortedDesc: sortCol === column.key && sortOrder === 'desc' }" @click="sortBy(column.key)">{{ column.label }}</th>
                        </tr></thead>
                        <tbody id="gamesTableBody">
                            <tr v-for="game in rows" :key="game.id" :data-id="game._isNew ? null : game.id" :draggable="showRank && !game._editing" @dragstart="startDrag(game)" @dragover.prevent @drop="dropOn(game)">
                                <template v-if="game._editing">
                                    <td><label class="favoriteIcon"><input v-model="game._draft.favorite" name="favorite" type="checkbox" :aria-label="`Favorite ${game.name || 'game'}`"><i class="far fa-heart unchecked" aria-hidden="true"></i><i class="fa fa-heart checked" aria-hidden="true"></i></label></td>
                                    <td v-if="showRank"><input v-model.number="game._draft.rank" name="rank" inputmode="numeric"></td>
                                    <td><input v-model="game._draft.name" name="name" required></td>
                                    <td><select v-model="game._draft.status" name="status"><option v-for="status in statuses" :key="status">{{ status }}</option></select></td>
                                    <td class="addPlatformCol"><select v-model="game._draft.platform" name="platform"><option value="">Select platform</option><option v-for="platform in platforms" :key="platform">{{ platform }}</option></select></td>
                                    <td><select v-model="game._draft.platformType" name="platformType"><option v-for="type in platformTypes" :key="type">{{ type }}</option></select></td>
                                    <td><select v-model="game._draft.format" name="format"><option v-for="format in formats" :key="format">{{ format }}</option></select></td>
                                    <td><select v-model="game._draft.genre" name="genre"><option value="Not Set">Not Set</option><option v-for="genre in genres" :key="genre">{{ genre }}</option></select></td>
                                    <td><label v-for="star in 5" :key="star" class="ratingStar"><input type="checkbox" name="rating" :value="star" :aria-label="`${star} star rating`" :checked="game._draft.rating >= star" @change="game._draft.rating = star"><i class="far fa-star unchecked" aria-hidden="true"></i><i class="fas fa-star checked" aria-hidden="true"></i></label></td>
                                    <td><button type="button" class="saveSingleAddGame saveSingleEditGame" @click="saveGame(game)">✓</button> <button type="button" class="cancelSingleAddGame cancelSingleEditGame" @click="cancelGame(game)">×</button></td>
                                </template>
                                <template v-else>
                                    <td class="favorite"><label class="favoriteIcon"><input type="checkbox" class="favoriteBox" :aria-label="`Favorite ${game.name}`" :checked="!!game.favorite" @change="toggleFavorite(game)"><i class="far fa-heart unchecked" aria-hidden="true"></i><i class="fa fa-heart checked" aria-hidden="true"></i></label></td>
                                    <td v-if="showRank" class="rank">{{ game.rank }}</td>
                                    <td class="name"><button type="button" class="name-edit-button" :aria-label="`Edit ${game.name}`" title="Edit this game" @click="draftFor(game)">{{ game.name }}</button></td><td class="status">{{ game.status }}</td><td class="platform">{{ game.platform }}</td>
                                    <td class="platformType">{{ game.platformType }}</td><td class="format">{{ game.format }}</td><td class="genre">{{ game.genre }}</td>
                                    <td class="stratingatus" :data-rating="game.rating"><label v-for="star in 5" :key="star" class="ratingStar"><input type="checkbox" class="ratingBox" :value="star" :aria-label="`${star} star rating for ${game.name}`" :checked="game.rating >= star" @change="setRating(game, star)"><i class="far fa-star unchecked" aria-hidden="true"></i><i class="fas fa-star checked" aria-hidden="true"></i></label></td>
                                    <td v-if="game._deleting"><button type="button" class="saveSingleDeleteGame" @click="deleteGame(game)">✓</button> <button type="button" class="cancelSingleDeleteGame" @click="game._deleting = false">×</button></td>
                                    <td v-else><button type="button" class="editSingleGame" @click="draftFor(game)">Edit</button> <button type="button" class="deleteSingleGame" @click="game._deleting = true">Delete</button></td>
                                </template>
                            </tr>
                            <tr v-if="!rows.length && !loading"><td :colspan="visibleColumns.length" class="text-center">{{ message?.text || 'No Games Found' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </main>

            <Transition name="drawer-right">
            <aside v-show="toolsOpen" id="rightSidebar" class="sidebar-drawer sidebar-drawer-right" aria-label="Tools">
                <div class="drawer-heading"><h2>Tools</h2><button type="button" class="drawer-close" aria-label="Close tools" @click="toolsOpen = false">×</button></div>
                <div class="sidebarRightMenu"><div class="btn-group" role="group" aria-label="Game tools">
                    <button type="button" class="btn btn-sm btn-outline-secondary right-menu-item btn-search" :aria-pressed="rightPanel === 'search'" @click="openPanel('search')">Search</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary right-menu-item btn-import" :aria-pressed="rightPanel === 'import'" @click="openPanel('import')">Import</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary right-menu-item btn-export" @click="exportCsv">Export</button>
                </div></div>
                <section v-if="rightPanel === 'search'" class="sidebarRightContent">
                    <form id="searchForm" class="input-group p-5" @submit.prevent="searchGames"><input id="searchBar" v-model="searchName" class="form-control w-50" type="text" placeholder="Search" aria-label="Search"><button class="btn btn-dark">Submit</button></form>
                    <div id="searchResults">
                        <article v-for="(result, index) in searchResults" :key="`${result.name}-${index}`" class="searchResultRow row">
                            <div class="searchResultImage col-md-4"><img :src="result.cover" :alt="`${result.name} cover`"><br><br>
                                <button type="button" class="addToInventory btn btn-sm btn-outline-primary" @click="addGame(result.name)">Add To Inventory</button><br><br>
                                <button type="button" class="addToBacklog btn btn-sm btn-outline-primary" @click="addGame(result.name, 'Backlog')">Add To Backlog</button><br><br>
                                <button type="button" class="addToWishlist btn btn-sm btn-outline-primary" @click="addGame(result.name, 'Wishlist')">Add To Wishlist</button>
                            </div>
                            <div class="searchResultDetails col-md-8"><h3>{{ result.name }}</h3><p v-if="result.summary">Summary: {{ result.summary }}</p>
                                <p v-if="result.genres?.length">Genre(s): {{ result.genres.join('; ') }};</p><p v-if="result.platforms?.length">Platform(s): {{ result.platforms.join('; ') }};</p></div>
                        </article>
                    </div>
                </section>
                <section v-if="rightPanel === 'import'" class="sidebarRightContent">
                    <div id="fileImportSection" class="importSection"><a href="/games/getImportTemplate" target="_blank">Download Template</a><br>CSV File:<br>
                        <input ref="csvFile" type="file" id="csvFile" name="csvFile" accept=".csv,text/csv"><button id="importCSVSubmit" class="btn btn-dark" type="button" @click="importCsv">Import</button></div>
                    <div id="steamImportSection" class="importSection">Please enter your 17 digit Steam ID:<br><input v-model="steamId" id="steamId" placeholder="Steam ID" name="steamId">
                        <button id="steamImportSubmit" class="btn btn-dark" type="button" @click="importSteam">Import</button>
                        <p v-if="steamResults.length">The Following Games have been added: {{ steamResults.join(', ') }}</p></div>
                </section>
            </aside>
            </Transition>
        <div v-if="loading" id="big_loader" class="loadingOverlay" role="status" aria-label="Loading"><div class="spinner-border text-primary"></div></div>
        <div v-if="feedbackOpen" class="modal fade show d-block" id="my_modal" role="dialog" aria-modal="true" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">Beta Feedback</h4><button type="button" class="btn-close" aria-label="Close" @click="feedbackOpen = false"></button></div>
                <form id="feedbackForm" @submit.prevent="submitFeedback"><div class="modal-body clearfix"><p>Please provide any feedback you have for the beta of this application.</p>
                    <div class="form-group"><label for="feedbackName">Name:</label><input v-model="feedback.name" type="text" class="form-control" id="feedbackName"></div>
                    <div class="form-group"><label for="feedbackEmail">Email address:(Optional)</label><input v-model="feedback.email" type="email" class="form-control" id="feedbackEmail"></div>
                    <div class="form-group"><label for="feedbackComments">Comments:</label><textarea v-model="feedback.comments" class="form-control" id="feedbackComments"></textarea></div>
                    <button id="feedbackSubmit" type="submit" class="btn btn-outline-secondary">Submit</button></div></form>
            </div></div><div class="modal-backdrop fade show" @click="feedbackOpen = false"></div>
        </div>
    </div>
</template>
