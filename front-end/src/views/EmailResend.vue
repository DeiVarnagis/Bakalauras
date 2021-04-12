<template>
  <div class="container">
    <ValidationObserver ref="form">
      <form
        class="formLogin"
        @submit.prevent="methodSelect"
      >
        <div>
          <div class="con">
            <header class="headerLogin">
              <h2>{{ title }}</h2>
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
                  v-model="email"
                  class="inputLogin"
                  type="email"
                >
                <p>{{ errors[0] }}</p>
              </div>
            </ValidationProvider>
            <button
              type="submit"
              :disabled="loading"
              class="buttonLogin"
            >
              Patvirtinti
            </button>
          </div>
          <router-link :to="'/login'">
            Prisijungimas
          </router-link>
        </div>
      </form>
    </ValidationObserver>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["title", "method"],
  data() {
    return {
      email: "",
      loading: false,
    };
  },
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
