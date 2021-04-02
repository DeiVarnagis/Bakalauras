<template>
  <div class="container">
    <ValidationObserver ref="form">
      <form @submit.prevent="methodSelect" class="formLogin">
        <div>
          <div class="con">
            <header class="headerLogin">
              <h2>{{ this.title }}</h2>
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
                  v-model="email"
                />
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>
            <button type="submit" :disabled="loading" class="buttonLogin">
              Patvirtinti
            </button>
          </div>
          <router-link :to="'/login'">Prisijungimas</router-link>
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
      email: "",
      loading: false,
    };
  },
  props: ["title", "method"],
  methods: {
    resend() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        this.loading = true;
        axios
          .post("email/resend", { email: this.email })
          .then((res) => {
            this.$router.push("/login");
            this.$vToastify.success(res.data.message);
          })
          .catch((err) => {
            this.loading = false;
            this.$vToastify.warning(err.response.data.message);
          });
      });
    },
    forgorPassword() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        this.loading = true;
        axios
          .post("password/forgot", { email: this.email })
          .then((res) => {
            this.$router.push("/login");
            this.$vToastify.success(res.data.message);
          })
          .catch((err) => {
            this.loading = false;
            this.$vToastify.warning(err.response.data.message);
          });
      });
    },
    methodSelect() {
      if (this.method == "email") {
        this.resend();
      } else if(this.method == "password") {
        this.forgorPassword();
      }
    },
  },
};
</script>

<style scoped>
@import "../styles/login.css";
</style>
