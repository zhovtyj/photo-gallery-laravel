
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('gallery-component', require('./components/gallery/index.vue'));
//Vue.component('gallery-show-component', require('./components/gallery/show.vue'));

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)
Vue.use(require('vue-moment'));


const routes = [
    { name: 'gallery', path: '/', component: require('./components/gallery/index') },
    { name: 'gallery.show', path: '/gallery/:id/show', component: require('./components/gallery/show') }
]

const router = new VueRouter({
    mode: 'history',
    routes // сокращение от `routes: routes`
})

const app = new Vue({
    el: '#app',
    router
});
