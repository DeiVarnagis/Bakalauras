<template>
  <transition name="fade">
    <div class="modal" v-if="show">
      <div class="modal__backdrop" @click="closeModal()" />
      <ValidationObserver ref="form">
        <form
          @submit.prevent="register()"
          @keydown="backEndErrors.clear($event.target.name)"
          class="formLogin"
        >
          <div class="con">
            <div class="buttonDiv">
              <button type="button" class="modal__close" @click="closeModal()">
                <font-awesome-icon icon="times" />
              </button>
            </div>
            <header class="headerLogin">
              <h2>Pridėkite naują prietaisą</h2>
            </header>
            <span>
              <div class="textOnInput">
                <label class="top" for="inputText">Tipas</label>
                <select v-model="device.type" class="select-css">
                  <option value="LongTerm">Ilgalaikis prietaisas</option>
                  <option value="ShortTerm">Trumpalaikis prietaisas</option>
                </select>
              </div>
            </span>

            <ValidationProvider
              rules="required"
              mode="eager"
              v-slot="{ errors }"
            >
              <div class="textOnInput">
                <label for="inputText">Kodas</label>
                <input
                  class="inputLogin"
                  type="text"
                  name="code"
                  v-model="device.code"
                />

                <p>{{ errors[0] }}</p>
                <p v-if="backEndErrors.has('code')" class="textSize">
                  {{ backEndErrors.get("code") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              rules="required"
              mode="eager"
              v-slot="{ errors }"
            >
              <div class="textOnInput">
                <label for="inputText">Prietaiso pavadinimas</label>
                <input
                  class="inputLogin"
                  type="text"
                  name="name"
                  v-model="device.name"
                />
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              rules="required"
              mode="eager"
              v-slot="{ errors }"
            >
              <div class="textOnInput">
                <label for="inputText">Serijos numeris</label>
                <input
                  class="inputLogin"
                  id="txt-input"
                  type="text"
                  name="serialNumber"
                  v-model="device.serialNumber"
                />
                <p>{{ errors[0] }}</p>
                <p v-if="backEndErrors.has('serialNumber')" class="textSize">
                  {{ backEndErrors.get("serialNumber") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider mode="eager" v-slot="{ errors }">
              <div class="textOnInput">
                <label for="inputText">Įkelkite nuotrauką</label>
                <input
                  class="inputLogin"
                  id="txt-input"
                  type="file"
                  name="file"
                  @change="upload_src"
                />
                <p>{{ errors[0] }}</p>
                <img v-if="showPreview" :src="imagePreview" />
              </div>
            </ValidationProvider>

            <ValidationProvider
              rules="required|numeric|minNumber:1"
              mode="eager"
              v-slot="{ errors }"
            >
              <div class="textOnInput">
                <label for="inputText">Kiekis</label>
                <input
                  class="inputLogin"
                  type="number"
                  v-model="device.amount"
                  name="amount"
                />
                <p class="marginLeft">{{ errors[0] }}</p>
              </div>
            </ValidationProvider>
            <button class="buttonLogin">Pridėti</button>
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
  props: ["addDevice"],
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
          .post("devices/".concat(dev.type), dev, {
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