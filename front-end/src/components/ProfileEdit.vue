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
              <h2>Atnaujinkite profilį</h2>
            </header>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required|alpha"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Vardas</label>
                <input
                  v-model="profile.name"
                  class="inputLogin"
                  type="text"
                  name="code"
                >
                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('name')"
                  class="text-danger textSize"
                >
                  {{ backEndErrors.get("name") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              rules="required|alpha"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Pavardė</label>
                <input
                  v-model="profile.surname"
                  class="inputLogin"
                  type="text"
                  name="surname"
                >
                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('surname')"
                  class="text-danger textSize"
                >
                  {{ backEndErrors.get("surname") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              mode="eager"
              rules="required|email"
            >
              <div class="textOnInput">
                <label for="inputText">Elektroninis paštas</label>
                <input
                  id="txt-input"
                  v-model="profile.email"
                  class="inputLogin"
                  type="email"
                >
                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('email')"
                  class="text-danger textSize"
                >
                  {{ backEndErrors.get("email") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              rules=""
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Telefono numeris</label>
                <input
                  id="txt-input"
                  v-model="profile.phoneNumber"
                  class="inputLogin"
                  type="text"
                  name="phoneNumber"
                >
                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('phoneNumber')"
                  class="text-danger textSize"
                >
                  {{ backEndErrors.get("phoneNumber") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Profilio nuotrauką</label>
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
              rules=""
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Gimimo data</label>
                <input
                  id="txt-input"
                  v-model="profile.birth"
                  class="inputLogin"
                  type="date"
                  name="birth"
                >
                <p>{{ errors[0] }}</p>
                <p
                  v-if="backEndErrors.has('birth')"
                  class="text-danger textSize"
                >
                  {{ backEndErrors.get("birth") }}
                </p>
              </div>
            </ValidationProvider>

            <ValidationProvider
              v-slot="{ errors }"
              rules=""
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Adresas</label>
                <input
                  v-model="profile.address"
                  class="inputLogin"
                  type="text"
                  name="amount"
                >
                <p class="marginLeft">
                  {{ errors[0] }}
                </p>
                <p
                  v-if="backEndErrors.has('address')"
                  class="text-danger textSize"
                >
                  {{ backEndErrors.get("address") }}
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
  data() {
    return {
      profile: {
        name: null,
        surname: null,
        email: null,
        address: null,
        birth: null,
        src: null,
      },
      id: null,
      show: false,
      backEndErrors: new Errors(),
    };
  },

  methods: {
    closeModal() {
      this.show = false;
    },
    openModal(data) {
      this.id = data.id;
      this.profile.name = data.name;
      this.profile.surname = data.surname;
      this.profile.email = data.email;
      this.profile.address = data.address;
      this.profile.birth = data.birth;
      this.profile.phoneNumber = data.phoneNumber;
      this.profile.src = data.src;
      this.show = true;
     
    },
    edit() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        var formData = this.gatherFormData();
        axios
          .post("users/" + this.id, formData, {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.$emit("updateProfile", res.data.data);
            this.closeModal();
            this.$vtNotify("Profilis sėkmingai buvo atnaujintas");
          })
          .catch((err) => {
            this.backEndErrors.record(err.response.data);
          });
      });
    },
    upload_src(e) {
      this.profile.src = e.target.files[0];

      let reader = new FileReader();

      if (this.profile.src) {
        if (/\.(jpe?g|png|gif)$/i.test(this.profile.src.name)) {
          reader.readAsDataURL(this.profile.src);
        }
      }
    },
    gatherFormData() {
      const data = new FormData();
      data.append("name", this.profile.name);
      data.append("surname", this.profile.surname);
      data.append("email", this.profile.email);
      if (this.profile.address != null) {
        data.append("address", this.profile.address);
      }
      if (this.profile.birth != null) {
        data.append("birth", this.profile.birth);
      }
      if (this.profile.phoneNumber != null) {
        data.append("phoneNumber", this.profile.phoneNumber);
      }
      data.append("src", this.profile.src);
      data.append("_method", "PUT");

      return data;
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>