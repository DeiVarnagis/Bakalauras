<template>
  <transition name="fade">
    <div class="modal" v-if="show">
      <div class="modal__backdrop" @click="closeModal()" />
      <form class="formLogin">
        <div class="con">
          <div class="buttonDiv">
            <button type="button" class="modal__close" @click="closeModal()">
              <font-awesome-icon icon="times" />
            </button>
          </div>
          <header class="headerLogin">
            <h2>Ar tikrai norite ištrinti {{ device.name }}</h2>
          </header>
          <br />
          <div>
            <button
              @click.prevent="device.type != null ? deleteDevice(device.type, device.id) : deleteAccessory(device.id)"
              class="buttonLogin"
            >
              Taip
            </button>
            <button
              @click.prevent="closeModal()"
              class="buttonLogin"
              style="background: red"
            >
              Ne
            </button>
          </div>
        </div>
      </form>
    </div>
  </transition>
</template>
<script>
import axios from "axios";
export default {
  name: "deleteModal",
  data() {
    return {
      show: false,
    };
  },
  props: ["device", "type", "deleteValue", "index"],
  methods: {
    closeModal() {
      this.show = false;
    },
    openModal() {
      this.show = true;
    },
    deleteDevice(type, id) {
      axios
        .delete("devices/" + type + "/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then(
          this.$emit("deleteValue", this.index),
          this.closeModal(),
          this.$vToastify.success(
            "Prietaisas " + this.device.name + " sėkmingai buvo ištrintas"
          )
        )
        .catch((err) => {
          this.$vToastify.error(
            this.device.name + " " + err.response.data.error
          );
        });
    },
    deleteAccessory(id) {
      axios
        .delete("accessories/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res);
          this.$emit("deleteValue", this.index);
          this.closeModal();
          this.$vToastify.success(
            "Aksesuaras " + this.device.name + " sėkmingai buvo ištrintas"
          );
        })
        .catch((err) => {
          this.$vToastify.error(
            this.device.name + " " + err.response.data.error
          );
        });
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>