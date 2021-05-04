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
      <ValidationObserver ref="form">
        <form
          id="form"
          class="formLogin"
          @submit.prevent="add()"
           @keydown="backEndErrors.clear($event.target.name)"
        >
          <div class="con">
            <div class="buttonDiv">
              <button
                type="button"
                class="modal__close"
                @click="closeModal()"
              >
                <font-awesome-icon icon="times" />
              </button>
            </div>
            <header class="headerLogin">
              <h2>Atnaujinkite aksesuarą</h2>
            </header>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Aksesuaro Pavadinimas</label>
                <input
                  v-model="accessory.name"
                  class="inputLogin"
                  type="text"
                  name="name"
                >
                <p>{{ errors[0] }}</p>
                 <p
                  v-if="backEndErrors.has('name')"
                  class="textSize"
                >
                  {{ backEndErrors.get("name") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
             rules="required|numeric|minNumber:1|maxNumber:30"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Aksesuaro kiekis</label>
                <input
                  v-model="accessory.amount"
                  class="inputLogin"
                  type="number"
                  name="name"
                >
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Įkelkite nuotrauką</label>
                <input
                  class="inputLogin"
                  type="file"
                  name="file"
                  @change="upload_src"
                >
                <p>{{ errors[0] }}</p>
                <img
                  v-if="showPreview"
                  class="show_img"
                  :src="imagePreview"
                >
              </div>
            </ValidationProvider>

            <button class="buttonLogin">
              Sukurti
            </button>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </transition>
</template>

<script>
import axios from "axios";
import { Errors } from "../Errors";

export default {
  name: "AccessoryAddModal",
  props: ["type", "id", "addAccessory"],
  data() {
    return {
      accessory: {
        name: null,
        amount: 1,
        longTerm_id: null,
        shortTerm_id: null,
        src: null,
      },
      imagePreview: null,
      showPreview: false,
      show: false,
      backEndErrors: new Errors(),
    };
  },

  methods: {
    closeModal() {
      this.show = false;
    },
    openModal() {
      this.show = true;
    },
    add() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }

        let dev = new FormData();

        dev.append("name", this.accessory.name);
        dev.append("amount", this.accessory.amount);
        dev.append("src", this.accessory.src);

        if (this.type == "LongTerm") {
          dev.append("longTerm_id", this.id);
        } else {
          dev.append("shortTerm_id", this.id);
        }

        axios
          .post("accessories/", dev, {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          })
          .then((res) => {
            this.accessory.src = null;
            this.accessory.name = null;
            this.accessory.amount = null;
            this.$emit("addAccessory", res.data)
            this.closeModal();
            this.$vtNotify("Aksesuaras sėkmingai pridėtas");
          })
          .catch((err) => {
              this.backEndErrors.record(err.response.data);
          });
      });
    },
    upload_src(e) {
      this.accessory.src = e.target.files[0];

      let reader = new FileReader();

      reader.addEventListener(
        "load",
        function () {
          this.showPreview = true;
          this.imagePreview = reader.result;
        }.bind(this),
        false
      );

      if (this.accessory.src) {
        if (/\.(jpe?g|png|gif)$/i.test(this.accessory.src.name)) {
          reader.readAsDataURL(this.accessory.src);
        }
      }
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>