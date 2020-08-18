window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue');

Vue.component('app', require('./app.vue').default);

new Vue({el: '#app'});
