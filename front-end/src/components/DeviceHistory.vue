<template>
  <div
    v-if="!loading"
    class="inner-div_column"
  >
    <div
      v-if="history.length > 0"
      id="scroller"
      class="inner-div_column_layout_right"
    >
      <div
        v-for="(action, index) in history"
        :key="index"
        class="inner-div_column_layout_right_row"
      >
        <p
          :class="
            action.action == 2
              ? 'inner-div_column_layout_right_bubbleLabelRight'
              : 'inner-div_column_layout_right_bubbleLabelLeft'
          "
        >
          {{ action.receiver_name }} {{ action.receiver_surname }}
          {{ action.updated_at }}
        </p>

        <p
          :class="
            action.action == 2
              ? 'inner-div_column_layout_right_bubble_yellow'
              : 'inner-div_column_layout_right_bubble_blue'
          "
        />

        <p :class="getClass(index)" />
      </div>
      <div class="inner-div_column_layout_right_row">
        <p class="inner-div_column_layout_right_bubbleLabelLeft">
          {{ history[history.length - 1].owner_name }}
          {{ history[history.length - 1].owner_surname }}
          {{ history[history.length - 1].created_at }}
        </p>
        <p class="inner-div_column_layout_right_bubble_blue" />
        <p :class="firstConnector()" />
      </div>
    </div>
    <div
      v-else
      class="inner-div_column_layout_right"
    >
      <h3>Prietaisas vis dar neturi istorijos</h3>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      history: [],
      loading: true,
    };
  },
  created: function () {
    this.getHistory();
  },
  methods: {
    getClass(index) {
      if (index == 0) {
        return "";
      }
      if (this.history[index].action == 2) {
        if (
          this.history[index - 1] != null &&
          this.history[index - 1].action == 2
        ) {
          return "inner-div_column_layout_right_connector_R_UP";
        }
        return "inner-div_column_layout_right_connector_R_L_UP";
      } else if (
        this.history[index - 1] != null &&
        this.history[index - 1].action == 1
      ) {
        return "inner-div_column_layout_right_connector_L_UP";
      }
      return "inner-div_column_layout_right_connector_L_R_UP";
    },
    firstConnector() {
      if (this.history[this.history.length-1].action == 2) {
        return "inner-div_column_layout_right_connector_L_R_UP";
      }
      return "inner-div_column_layout_right_connector_L_UP";
    },
    getHistory() {
      axios
        .get(
          "devices/" +
            this.$route.params.type +
            "/" +
            this.$route.params.id +
            "/history",
          {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          }
        )
        .then((response) => {
          this.history = response.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error.response.data);
        });
    },
  },
};
</script>

<style>
</style>