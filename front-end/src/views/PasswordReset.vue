<template>
  <div class="containerLogin">
    <ValidationObserver ref="form">
      <form
        class="formLogin"
        @submit.prevent="resetPassword()"
      >
        <div class="con">
          <header class="headerLogin">
            <h2>Slaptažodžio pakeitimas</h2>
          </header>

          <ValidationProvider
            v-slot="{ errors }"
            rules="required|min:6|password:@confirm"
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
            rules="required|min:6"
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
            Patvirtinti
          </button>
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
        password: null,
        password_confirmation: null,
      },
    };
  },
  methods: {
    resetPassword() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        var result = decodeURIComponent(this.$route.params.id);
        var last = result.substring(37, result.length);
        axios
          .post(last, this.user)
          .then((res) => {
            this.$router.push("/login"),
              this.$vToastify.success(res.data.message);
          })
          .catch((err) => {
            this.$router.push("/login"),
              this.$vToastify.warning(err.response.data.message);
          });
      });
    },
  },
};
</script>

<style scoped >
@import "../styles/login.css";
</style>