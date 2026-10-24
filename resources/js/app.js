import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.css';
import './all';
import 'jquery-ui';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

const vueRoot = document.querySelector('[data-vue-app]');
if (vueRoot) {
    createApp({})
        .component('example-component', ExampleComponent)
        .mount(vueRoot);
}
