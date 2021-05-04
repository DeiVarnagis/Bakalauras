<template>
  <transition name="fade">
    <div v-if="show" class="modal">
      <div class="modal__backdrop" @click="closeModal()" />
      <ValidationObserver ref="form">
        <form id="form"
         class="formLogin"
        @submit.prevent="update()"
          @keydown="backEndErrors.clear($event.target.name)">
          <div class="con">
            <div class="buttonDiv">
              <button type="button" class="modal__close" @click="closeModal()">
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
                />
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
                />
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>

            <ValidationProvider v-slot="{ errors }" mode="eager">
              <div class="textOnInput">
                <label for="inputText">Nauja nuotrauką</label>
                <input
                  class="inputLogin"
                  type="file"
                  name="file"
                  @change="upload_src"
                />
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>

            <button class="buttonLogin">Atnaujinti</button>
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
  name: "AccessoryEditModal",
  props: ["id", "updateAccessory", "index"],
  data() {
    return {
      accessory: {
        name: null,
        amount: null,
        longTerm_id: null,
        shortTerm_id: null,
        src: null,
      },
      show: false,
      backEndErrors: new Errors(),
    };
  },

  methods: {
    closeModal() {
      this.show = false;
    },
    openModal(data) {
      this.accessory.name = data.name;
      this.accessory.amount = data.amount;
      this.accessory.longTerm_id = data.longTerm_id;
      this.accessory.shortTerm_id = data.shortTerm_id;
      this.show = true;
    },
    update() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }

        var formData = this.gatherFormData();

        axios
          .post("accessories/" + this.id, formData, {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.$emit("updateAccessory", this.index, res.data),
              this.closeModal(),
              this.$vtNotify(
                "Aksesuaras " +
                  this.accessory.name +
                  " sėkmingai buvo atnaujintas"
              );
          })
          .catch((err) => {
             this.backEndErrors.record(err.response.data);
          });
      });
    },
    upload_src(e) {
      this.accessory.src = e.target.files[0];

      let reader = new FileReader();

      if (this.accessory.src) {
        if (/\.(jpe?g|png|gif)$/i.test(this.accessory.src.name)) {
          reader.readAsDataURL(this.accessory.src);
        }
      }
    },
    gatherFormData() {
      const data = new FormData();
      data.append("name", this.accessory.name);
      data.append("amount", this.accessory.amount);
      if (this.accessory.longTerm_id != null) {
        data.append("longTerm_id", this.accessory.longTerm_id);
      } else {
        data.append("shortTerm_id", this.accessory.shortTerm_id);
      }
      data.append("src", this.accessory.src);
      data.append("_method", "PUT");

      return data;
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>