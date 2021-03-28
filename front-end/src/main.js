import Vue from 'vue'
import App from './App.vue'
import router from './router'
import { ValidationProvider, ValidationObserver } from 'vee-validate';
import './validations';
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import VueToastify from "vue-toastify";
import './axios';
import axios from 'axios';

library.add(fas)


Vue.use(VueToastify, {
  position: "top-center",
  theme: "light",
  successDuration:4000,
  warningInfoDuration:4000,
  errorDuration: 4000,
  defaultTitle:false

});
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

Vue.config.productionTip = false

axios.interceptors.response.use(
  res => res,
  err => {
    if (err.response.status === 401 && err.response.data.message == "Unauthenticated.") {
      localStorage.removeItem('token')
      router.push('login')
    }
    throw err;
  }
);

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
