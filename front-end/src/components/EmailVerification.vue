<template>
  <h1>{{ message }}</h1>
</template>

<script>
//import axios from "axios";

export default {
  data() {
    return {
      message: "",
    };
  },
  mounted: function () {
    axios
      .get("email/verify".$route.params.id, this.user)
      .then((this.message = "email verified"))
      .catch((this.message = "email failed to verify"));
  },
};
</script>

<style>
</style>