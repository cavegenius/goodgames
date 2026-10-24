import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.css';
import { createApp } from 'vue';
import GamesApp from './components/GamesApp.vue';

const gamesRoot = document.getElementById('games-app');
if (gamesRoot) {
    createApp(GamesApp).mount(gamesRoot);
}
