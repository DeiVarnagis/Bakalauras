<template>
  <div id="app">
    <div class="row">
      <div class="column" v-if="ifLogged()">
        <NavBar></NavBar>
      </div>
      <router-view />
    </div>
  </div>
</template>

<script>
import NavBar from "../src/components/NavBar.vue";

export default {
  components: {
    NavBar,
  },
  methods: {
    ifLogged() {
      if (localStorage["token"]) {
        return true;
      }
      return false;
    },
  },
};
</script>

<style>
html {
  background-color: #e5e5e5;
}
#app {
  text-align: center;
}
</style>
