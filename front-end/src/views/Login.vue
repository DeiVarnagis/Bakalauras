<template>
  <div class="containerLogin">
    <ValidationObserver ref="form">
      <form
        class="formLogin"
        @submit.prevent="login"
        @keydown="clearError()"
      >
        <div>
          <div class="con">
            <header class="headerLogin">
              <h2>Prisijungimas</h2>
            </header>
            <ValidationProvider
              v-slot="{ errors }"
              mode="eager"
              rules="required|email"
            >
              <div class="textOnInput">
                <label for="inputText">Elektroninis paštas</label>
                <input
                  id="txt-input"
                  v-model="user.email"
                  class="inputLogin"
                  type="email"
                >
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              rules="required|min:6"
              mode="eager"
            >
              <div class="textOnInput">
                <label for="inputText">Slaptažodis</label>
                <input
                  id="txt-input"
                  v-model="user.password"
                  class="inputLogin"
                  type="password"
                  name="password"
                >
              </div>
              <p>{{ errors[0] }}</p>
              <p v-if="loginError !== ''">
                {{ loginError }}
              </p>
            </ValidationProvider> 
            <button
              type="submit"
              class="buttonLogin"
            >
              Prisijungti
            </button>
          </div>
          <router-link
            :to="{name:'passwordForgot'}"
            :method="'password'"
            :title="'Elektroninio pašto persiuntimas'"
          >
            Pamiršai slaptažodį?
          </router-link>
          <router-link :to="'/register'">
            Registruotis
          </router-link>
        </div>
      </form>
    </ValidationObserver>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      user: {
        email: "",
        password: "",
      },
      loginError: "",
    };
  },
  methods: {
    login() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        axios
          .post("auth/login", this.user)
          .then((res) => {
            localStorage["token"] = res.data.access_token;
            this.$router.push("/");
            this.$vToastify.success("Sėkmingai prisijungėte!")
          })
          .catch((err) => {
            this.loginError = err.response.data.error;
          });
      });
    },
    clearError() {
      this.loginError = "";
    },
  },
};
</script>

<style scoped>
@import "../styles/login.css";
</style>
