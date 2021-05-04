<template>
  <transition name="fade">
    <div v-if="show" class="modal">
      <div class="modal__backdrop" @click="closeModal()" />
      <form class="formLogin">
        <div class="con">
          <div class="buttonDiv">
            <button type="button" class="modal__close" @click="closeModal()">
              <font-awesome-icon icon="times" />
            </button>
          </div>
          <header class="headerLogin" v-if="device.name != null">
            <h2>Ar tikrai norite ištrinti {{ device.name }}</h2>
          </header>
           <header v-else class="headerLogin">
            <h2>Ar tikrai norite ištrinti</h2>
          </header>
          <br />
          <div>
            <button
              class="buttonLogin"
              @click.prevent="
                who == 'device'
                  ? deleteDevice(device.type, device.id)
                  : who == 'accessory' ? deleteAccessory(device.id)
                    : who == 'user' ? deleteUser(device.id) : who  == 'inventorization' ? deleteInventorization(device.id) : ''"
            >
              Taip
            </button>
            <button
              class="buttonLogin"
              style="background: red"
              @click.prevent="closeModal()"
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
  name: "DeleteModal",
  props: ["device", "type", "index", "who"],
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
    deleteUser(id) {
      axios
        .delete("users/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res);
          this.$emit("deleteValue", this.index);
          this.closeModal();
          this.$vToastify.success(
            "Vartotojas " + this.device.name + " sėkmingai buvo ištrintas"
          );
        })
        .catch((err) => {
          console.log(err.response);
        });
    },
    deleteInventorization(id) {
      axios
        .delete("inventorization/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res)
          this.$emit("deleteValue", this.index);
          this.closeModal();
          this.$vToastify.success(
            "Inventorizacijos data sėkmingai buvo ištrinta"
          );
        })
        .catch((err) => {
          console.log(err.response);
        });
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>