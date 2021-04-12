<template>
  <transition name="fade">
    <div
      v-if="show"
      class="modal"
    >
      <div
        class="modal__backdrop"
        @click="closeModal()"
      />
      <Registration
        :button-name="'Patvirtinti'"
        :header="'Pridekite nauja vartotoja'"
        :add-user="addUser"
        @closeModal="closeModal"
      />
    </div>
  </transition>
</template>

<script>
import Registration from "../views/Registration";
export default {
  components: {
    Registration,
  },
  props: ["addUser"],
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