<template>
  <h1>Email verification</h1>
</template>

<script>
import axios from "axios";

export default {
  mounted: function () {
    var result = decodeURIComponent(this.$route.params.id);
    var last = result.substring(37, result.length);
    axios
      .get(last)
      .then((res) => {
        this.$router.push("/login"),
        this.$vToastify.success(res.data.message);
      })
      .catch((err) => {
        this.$router.push("/login"),
        this.$vToastify.warning(err.response.data.message);
      });
  },
};
</script>

<style>
</style>