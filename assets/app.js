window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue');
import SvgVue from 'svg-vue';

Vue.use(SvgVue);
Vue.component('app', require('./components/App').default);

new Vue({el: '#app'});
