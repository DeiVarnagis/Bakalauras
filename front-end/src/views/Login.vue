<template>
  <div class="container">
    <ValidationObserver ref="form">
      <form @submit.prevent="login" class="formLogin" @keydown="clearError()">
        <div>
          <div class="con">
            <header class="headerLogin">
              <h2>Prisijungimas</h2>
            </header>
            <ValidationProvider
              mode="eager"
              rules="required|email"
              v-slot="{ errors }"
            >
              <div class="textOnInput">
                <label for="inputText">Elektroninis paštas</label>
                <input
                  class="inputLogin"
                  id="txt-input"
                  type="email"
                  v-model="user.email"
                />
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>
            <ValidationProvider rules="required|min:6" mode="eager" v-slot="{ errors }">
              <div class="textOnInput">
                <label for="inputText">Slaptažodis</label>
                <input
                  class="inputLogin"
                  type="password"
                  v-model="user.password"
                  id="txt-input"
                  name="password"
                />
              </div>
              <p>{{ errors[0] }}</p>
              <p v-if="loginError !== ''">{{ loginError }}</p>
            </ValidationProvider> 
            <button type="submit" class="buttonLogin">Prisijungti</button>
          </div>
          <a href="/password/forgot" :method="'password'" :title="'Elektroninio pašto persiuntimas'">Pamiršai slaptažodį?</a>
          <a href="/register">Registruotis</a>
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
