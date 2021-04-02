<template>
  <transition name="fade">
    <div class="modal" v-if="show">
      <div class="modal__backdrop" @click="closeModal()" />
      <Registration :buttonName="'Patvirtinti'" :header="'Pridekite nauja vartotoja'" v-bind:addUser="addUser" @closeModal="closeModal"></Registration>
    </div>
  </transition>
</template>

<script>
import Registration from "../views/Registration";
export default {
  props: ["addUser"],
  components: {
    Registration,
  },
  data() {
    return {
      show: false,
    };
  },
  methods: {
    closeModal() {
      this.show = false;
    },
    openModal() {
      this.show = true;
    },
  },
};
</script>

<style>
</style>