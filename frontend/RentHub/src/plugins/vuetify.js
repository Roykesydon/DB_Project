import Vue from "vue";
import Vuetify from "vuetify/lib/framework";
import VueCarousel from "vue-carousel";
import VueCompositionAPI from "@vue/composition-api";
import colors from 'vuetify/lib/util/colors'
import VueCookies from 'vue-cookies'
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import VTooltip from 'v-tooltip'
import 'v-tooltip/dist/v-tooltip.css'

Vue.use(Vuetify);
Vue.use(VueCarousel);
Vue.use(VueCompositionAPI);
Vue.use(VueCookies);
Vue.use(VueToast);
Vue.use(VTooltip);

export default new Vuetify({
  theme: {
    dark: true,
    themes: {
      dark: {
        primary: '#42A5F5',
        secondary: '#B388FF'
      },
    },
  },
});
