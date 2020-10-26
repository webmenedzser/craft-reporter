import Vue from 'vue';

Vue.component('restoreform', require('../vue/RestoreForm').default);

const app = document.getElementById('craft-reporter-vue');
if (app) {
  new Vue({
    el: app
  });
}
