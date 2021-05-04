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
          class="formLogin"
          @submit.prevent="register()"
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
              <h2>Pridėkite naują prietaisą</h2>
            </header>
            <span>
              <div class="textOnInput">
                <label
                  class="top"
                  for="inputText"
                >Tipas</label>
                <select
                  v-model="device.type"
                  class="select-css"
                >
                  <option value="LongTerm">Ilgalaikis prietaisas</option>
                  <option value="ShortTerm">Trumpalaikis prietaisas</option>
                </select>
              </div>
            </span>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Kodas</label>
                <input
                  v-model="device.code"
                  class="inputLogin"
                  type="text"
                  name="code"
                >

                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('code')"
                  class="textSize"
                >
                  {{ backEndErrors.get("code") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Prietaiso pavadinimas</label>
                <input
                  v-model="device.name"
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
              rules="required"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Serijos numeris</label>
                <input
                  id="txt-input"
                  v-model="device.serialNumber"
                  class="inputLogin"
                  type="text"
                  name="serialNumber"
                >
                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('serialNumber')"
                  class="textSize"
                >
                  {{ backEndErrors.get("serialNumber") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Įkelkite nuotrauką</label>
                <input
                  id="txt-input"
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

            <ValidationProvider
              v-slot="{ errors }"
              rules="required|numeric|minNumber:1|maxNumber:2"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Kiekis</label>
                <input
                  v-model="device.amount"
                  class="inputLogin"
                  type="number"
                  name="amount"
                >
                <p class="marginLeft">
                  {{ errors[0] }}
                </p>
              </div>
            </ValidationProvider>
            <button class="buttonLogin">
              Pridėti
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
  name: "DeviceAddModal",
  props: ["addDevice"],
  data() {
    return {
      device: {
        code: null,
        name: null,
        serialNumber: null,
        amount: 1,
        src: null,
        type: "LongTerm",
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
    register() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        let dev = this.gatherFormData();

        axios
          .post("devices/".concat(this.device.type), dev, {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          })
          .then((res) => {
            this.device.serialNumber = null;
            this.device.code = null;
            this.device.name = null;
            this.device.amount = null;
            this.device.src = null;
            this.imagePreview= null;
            this.showPreview= false;

            this.$vToastify.success(res.data.message);
            this.closeModal();
          })
          .catch((err) => {
            console.log(err.response.data);
            this.backEndErrors.record(err.response.data);
          });
      });
    },
    upload_src(e) {
      this.device.src = e.target.files[0];

      let reader = new FileReader();

      reader.addEventListener(
        "load",
        function () {
          this.showPreview = true;
          this.imagePreview = reader.result;
        }.bind(this),
        false
      );

      if (this.device.src) {
        if (/\.(jpe?g|png|gif)$/i.test(this.device.src.name)) {
          reader.readAsDataURL(this.device.src);
        }
      }
    },
      gatherFormData() {
        const data = new FormData();
        data.append("code", this.device.code);
        data.append("name", this.device.name);
        data.append("serialNumber", this.device.serialNumber);
        data.append("amount", this.device.amount);
        data.append("src", this.device.src);
        data.append("type", this.device.type);
        
        return data;
    }
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>