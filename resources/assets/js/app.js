import "./bootstrap";
import Vue from "vue";
import store from "~/store";
import router from "~/router";
import App from "~/components/App";
import Flutterwave from 'vue-flutterwave'

Vue.use(Flutterwave, { publicKey: 'FLWPUBK_TEST-a6f76f90c3fd5f28efcdd463b3368aca-X' })

import "~/plugins";
import "~/components";

Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  router,
  store,
  ...App
});
