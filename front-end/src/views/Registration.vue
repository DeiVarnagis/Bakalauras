<template>
  <div class="containerLogin">
    <ValidationObserver ref="form">
      <form
        @submit.prevent="register()"
        @keydown="backEndErrors.clear($event.target.name)"
        class="formLogin"
      >
        <div class="con">
          <header class="headerLogin">
            <h2>{{header}}</h2>
          </header>
          <ValidationProvider
            mode="eager"
            rules="required|alpha"
            v-slot="{ errors }"
          >
            <div class="textOnInput">
              <label for="inputText">Vardas</label>
              <input class="inputLogin" type="text" v-model="user.name" />
              <p>{{ errors[0] }}</p>
            </div>
          </ValidationProvider>

          <ValidationProvider
            mode="eager"
            rules="required|alpha"
            v-slot="{ errors }"
          >
            <div class="textOnInput">
              <label for="inputText">Pavardė</label>
              <input class="inputLogin" type="text" v-model="user.surname" />
              <p>{{ errors[0] }}</p>
            </div>
          </ValidationProvider>

          <ValidationProvider
            rules="email|required"
            v-slot="{ errors }"
            mode="eager"
          >
            <div class="textOnInput">
              <label for="inputText">Elektroninis paštas</label>
              <input
                class="inputLogin"
                id="txt-input"
                type="email"
                name="email"
                v-model="user.email"
              />
            </div>
            <p>{{ errors[0] }}</p>
            <p v-if="backEndErrors.has('email')" class="text-danger textSize">
              {{ backEndErrors.get("email") }}
            </p>
          </ValidationProvider>

          <ValidationProvider
            rules="required|min:8|password:@confirm"
            v-slot="{ errors }"
            mode="eager"
          >
            <div class="textOnInput">
              <label for="inputText">Slaptažodis</label>
              <input
                class="inputLogin"
                type="password"
                v-model="user.password"
                name="password"
              />
              <p class="marginLeft">{{ errors[0] }}</p>
            </div>
          </ValidationProvider>

          <ValidationProvider
            name="confirm"
            rules="required|min:8"
            v-slot="{ errors }"
            mode="eager"
          >
            <div class="textOnInput">
              <label for="inputText">Pakartokite slaptažodį</label>
              <input
                class="inputLogin"
                type="password"
                v-model="user.password_confirmation"
                name="password"
              />
              <p class="marginLeft">{{ errors[0] }}</p>
            </div>
          </ValidationProvider>
          <button class="buttonLogin">{{buttonName}}</button>
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



