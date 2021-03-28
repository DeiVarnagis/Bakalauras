<template>
  <div class="container">
    <ValidationObserver ref="form">
      <form
        @submit.prevent="resetPassword()"
        class="formLogin"
      >
        <div class="con">
          <header class="headerLogin">
            <h2>Slaptažodžio pakeitimas</h2>
          </header>

          <ValidationProvider
            rules="required|min:6|password:@confirm"
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
            rules="required|min:6"
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
          <button class="buttonLogin">Patvirtinti</button>
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

<style>
</style>