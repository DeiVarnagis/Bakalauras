<template>
  <div class="data_container">
    <h1>
      <slot></slot><span class="data_count">{{ data }}</span>
    </h1>
  </div>
</template>

<script>
export default {
  name: "DataBlock",
  props: ["data"],
  data() {
    return {};
  },
};
</script>

<style scoped>
.data_container {
  display: table;
  width: 25%;
  height: 100%;
  background: #ffffff;
  text-align: center;
  border-radius: 15px;
}

h1 {
  position: relative;
  font-weight: 400;
  font-size: 1.3rem;
  color: #5c5c5c;
  vertical-align: middle;
  display: table-cell;
  padding: 0.5rem 2.5rem;
  white-space: nowrap;
}

.data_count {
  padding-left: 3rem;
}
</style>