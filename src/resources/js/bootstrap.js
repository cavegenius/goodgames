import axios from 'axios';
import $ from 'jquery';
import _ from 'lodash';
import 'bootstrap';

window.axios = axios;
window.$ = window.jQuery = $;
window._ = _;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}
