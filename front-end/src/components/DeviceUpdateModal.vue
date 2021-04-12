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
          enctype="multipart/form-data"
          class="formLogin"
          @submit.prevent="edit()"
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
              <h2>Atnaujinkite prietaisą</h2>
            </header>
            <div class="textOnInput">
              <label
                class="top"
                for="inputText"
              >Tipas</label>
              <select
                v-model="device.deviceType"
                class="select-css"
              >
                <option value="LongTerm">
                  Ilgalaikis prietaisas
                </option>
                <option value="ShortTerm">
                  Trumpalaikis prietaisas
                </option>
              </select>
            </div>

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
                <label for="inputText">Nauja nuotrauką</label>
                <input
                  class="inputLogin"
                  type="file"
                  name="file"
                  @change="upload_src"
                >
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required|numeric|minNumber:1|maxNumber:30"
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
              Atnaujinti
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
  name: "DeviceUpdateModal",
  props: ["id", "updateDevice", "index"],
  data() {
    return {
      device: {
        name: null,
        code: null,
        serialNumber: null,
        amount: 1,
        deviceType: null,
        user_id:null,
        src: null,
      },
      type: null,
      show: false,
      backEndErrors: new Errors(),
    };
  },

  methods: {
    closeModal() {
      this.show = false;
    },
    openModal(data) {
      this.device.name = data.name;
      this.device.code = data.code;
      this.device.serialNumber = data.serialNumber;
      this.device.amount = data.amount;
      this.device.user_id = data.user_id;
      this.device.deviceType = data.type;
      this.type = data.type;
      this.show = true;
    },
    edit() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        var formData = this.gatherFormData();
        axios
          .post("devices/".concat(this.type) + "/" + this.id, formData, {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
              'Content-Type': 'multipart/form-data'
            },
          })
          .then((res) => {
            this.device.src = res.data.src
            this.$emit("updateDevice", this.index, this.device);
            this.closeModal();
            this.imagePreview = null;
            this.showPreview = false;
            this.$vtNotify(
              "Prietaisas " + this.device.name + " sėkmingai buvo atnaujintas"
            );
          })
          .catch((err) => {
            console.log(err.response)
            this.backEndErrors.record(err.response.data);
          });
      });
    },
    upload_src(e) {
      this.device.src = e.target.files[0];

      let reader = new FileReader();

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
        data.append("user_id", this.device.user_id);
        data.append("serialNumber", this.device.serialNumber);
        data.append("amount", this.device.amount);
        data.append("src", this.device.src);
        data.append("deviceType", this.device.deviceType);
        data.append('_method', 'PUT')
        
        return data;
    }
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>