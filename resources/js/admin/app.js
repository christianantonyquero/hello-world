/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import dayjs from 'dayjs';
import Vue from 'vue';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

// window.Vue = require('vue');

// Filter Date Format
Vue.filter('dateFormat', function(value) {
    if(!value) return '';
    return dayjs(value).format('M/DD/YYYY, HH:mm');
});

/**
 * Components
 */

Vue.component('create-store', require('./components/CreateStore.vue').default);
Vue.component('edit-store', require('./components/EditStore.vue').default);

Vue.component('create-user', require('./components/CreateUser.vue').default);
Vue.component('edit-user', require('./components/EditUser.vue').default);

Vue.component('product-list', require('./components/ProductList.vue').default);
Vue.component('create-product', require('./components/CreateProduct.vue').default);
Vue.component('edit-product', require('./components/EditProduct.vue').default);

Vue.component('create-store-page', require('./components/CreateStorePage.vue').default);
Vue.component('edit-store-page', require('./components/EditStorePage.vue').default);

Vue.component('edit-store-page-hotspots', require('./components/EditStorePageHotspots.vue').default);

Vue.component('create-store-song', require('./components/CreateStoreSong.vue').default);
Vue.component('edit-store-song', require('./components/EditStoreSong.vue').default);

Vue.component('manage-photo-booth-backdrops', require('./components/ManagePhotoBoothBackdrops.vue').default);

// Shared components
Vue.component('errors', require('./components/shared/Errors.vue').default);

Vue.use(VueToast);

const app = new Vue({
    el: '#app',
});

window.app = app; // for using $refs
