import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';
import VueCarousel from 'vue-carousel';

Vue.use(Vuetify);
Vue.use(VueCarousel);

export default new Vuetify({
    theme: { dark: true },
});
