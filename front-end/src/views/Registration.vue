<template>
  <div class="containerLogin">
    <ValidationObserver ref="form">
      <form
        class="formLogin"
        @submit.prevent="register()"
        @keydown="backEndErrors.clear($event.target.name)"
      >
        <div class="con">
          <header class="headerLogin">
            <h2>{{ header }}</h2>
          </header>
          <ValidationProvider
            v-slot="{ errors }"
            mode="eager"
            rules="required|alpha"
          >
            <div class="textOnInput">
              <label for="inputText">Vardas</label>
              <input
                v-model="user.name"
                class="inputLogin"
                type="text"
              >
              <p>{{ errors[0] }}</p>
            </div>
          </ValidationProvider>

          <ValidationProvider
            v-slot="{ errors }"
            mode="eager"
            rules="required|alpha"
          >
            <div class="textOnInput">
              <label for="inputText">Pavardė</label>
              <input
                v-model="user.surname"
                class="inputLogin"
                type="text"
              >
              <p>{{ errors[0] }}</p>
            </div>
          </ValidationProvider>

          <ValidationProvider
            v-slot="{ errors }"
            rules="email|required"
            mode="eager"
          >
            <div class="textOnInput">
              <label for="inputText">Elektroninis paštas</label>
              <input
                id="txt-input"
                v-model="user.email"
                class="inputLogin"
                type="email"
                name="email"
              >
            </div>
            <p>{{ errors[0] }}</p>
            <p
              v-if="backEndErrors.has('email')"
              class="text-danger textSize"
            >
              {{ backEndErrors.get("email") }}
            </p>
          </ValidationProvider>

          <ValidationProvider
            v-slot="{ errors }"
            rules="required|min:8|password:@confirm"
            mode="eager"
          >
            <div class="textOnInput">
              <label for="inputText">Slaptažodis</label>
              <input
                v-model="user.password"
                class="inputLogin"
                type="password"
                name="password"
              >
              <p class="marginLeft">
                {{ errors[0] }}
              </p>
            </div>
          </ValidationProvider>

          <ValidationProvider
            v-slot="{ errors }"
            name="confirm"
            rules="required|min:8"
            mode="eager"
          >
            <div class="textOnInput">
              <label for="inputText">Pakartokite slaptažodį</label>
              <input
                v-model="user.password_confirmation"
                class="inputLogin"
                type="password"
                name="password"
              >
              <p class="marginLeft">
                {{ errors[0] }}
              </p>
            </div>
          </ValidationProvider>
          <button class="buttonLogin">
            {{ buttonName }}
          </button>
        </div>
      </form>
    </ValidationObserver>
  </div>
</template>

<script>
import axios from "axios";
import { Errors } from "../Errors";

export default {
  props:{
    addUser:{
      type:Boolean,
      default:false
    },
    header:{
      type:String,
      default:"Registracija"
    },
    buttonName:{
      type:String,
      default:"Registruotis"
    },
  },
  data() {
    return {
      user: {
        name: null,
        surname: null,
        email: null,
        password: null,
        password_confirmation: null,
      },
      backEndErrors: new Errors(),
    };
  },
  methods: {
    register() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        axios
          .post("auth/register", this.user)
          .then((res) => {
            console.log(res);
              this.user.name = null;
              this.user.surname = null;
              this.user.email = null;
              this.user.password = null;
              this.user.password_confirmation = null;
            if (this.addUser) {
              this.$emit("closeModal");
              this.$vToastify.success("Naujas vartotojas sėkmingai buvo sukurtas");
            } else {
              this.$router.push("/login");
              this.$vToastify.success("Sveikinu prisiregistravus!!");
              this.$refs.from.reset();
            }
          })
          .catch((err) => {
            console.log(err.response)
            this.backEndErrors.record(err.response.data);
          });
      });
    },
  },
};
</script>

<style scoped>
@import "../styles/login.css";
</style>



