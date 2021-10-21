import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify'

Vue.config.productionTip = false


fetch(process.env.BASE_URL + "config.json")
  .then((response) => response.json())
  .then((config) => {
       Vue.prototype.$config = config
       new Vue({
        router,
        store,
        vuetify,
        render: h => h(App)
      }).$mount('#app')
  })
