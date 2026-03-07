import Vue from 'vue';

// Is there a better way to import and handle this
import vuexStore from './vuex-store';

import StoreApp from './StoreApp.vue'
import router from './router';
import api from './services/api';

import gtagHelpers from './services/gtag-mixin';

import VueCurrencyFilter from 'vue-currency-filter'
import VueToast from 'vue-toast-notification';
import PortalVue from 'portal-vue'

import 'vue-toast-notification/dist/theme-default.css';


window.Vue = Vue;
Vue.prototype.$api = api;

// hook in some gtag functions
Vue.prototype.$gtag = gtagHelpers;

Vue.use(PortalVue);

Vue.use(VueCurrencyFilter, {
    symbol: '$',
    thousandsSeparator: ',',
    fractionCount: 2,
    fractionSeparator: ".",
    symbolSpacing: false,
});
Vue.use(VueToast);

// Init Vuex state before creating app
// otherwise they are not available in beforeEnter router
// https://stackoverflow.com/questions/45290003/router-beforeeach-guard-executed-before-state-loaded-in-vue-created
vuexStore.dispatch('setStore', window.__INIT_STATE__.store);
vuexStore.dispatch('setUser', window.__INIT_STATE__.user);

const app = new Vue({
 router,
 store : vuexStore,
 render: h => h(StoreApp),
}).$mount('#app');

window.app = app;
