<template>
  <div id="app">
    <div v-if="ifLogged()">
      <NavBar class="navigation-component" />
    </div>
    <router-view class="router-view" />
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
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Oswald&display=swap');
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
html {
  background-color: #efefefef;
}
#app {
  display: flex;
  justify-content: space-between;
}
.navigation-component {
  margin-right: 15px;
}
.router-view {
  width: 100%;
}
</style>
