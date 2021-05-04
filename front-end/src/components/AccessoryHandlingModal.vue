<template>
  <ValidationObserver ref="form">
    <form
      class="formLogin"
      @submit.prevent="submitAccessories"
    >
      <div class="con">
        <div class="buttonDiv">
          <button
            type="button"
            class="modal__close"
            @click="close()"
          >
            <font-awesome-icon icon="times" />
          </button>
        </div>
        <header class="headerLogin">
          <h2>Pasirinkite aksesuarus</h2>
        </header>
        <div
          id="scroller"
          class="tableAccessories"
        >
          <table class="accessoriesTable">
            <thead>
              <tr>
                <th>
                  <input
                    v-model="selectAll"
                    type="checkbox"
                    @click="select"
                  >
                </th>
                <th>Pavadinimas</th>
                <th>Kiekis</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(i, index) in accessories"
                :key="index"
              >
                <td>
                  <input
                    v-model="selected"
                    type="checkbox"
                    :value="{
                      id: i.id,
                      name: i.name,
                      amount: getAmount(i.id),
                      src: i.src,
                    }"
                  >
                </td>
                <td>{{ i.name }}</td>

                <td>
                  <div class="quantity">
                    <button
                      class="quantity_minus"
                      @click.prevent="decrement(i.id)"
                    >
                      <span>-</span>
                    </button>
                    <input
                      disabled="true"
                      name="quantity"
                      type="text"
                      class="quantity_input"
                      :value="
                        checkIfExist(i.id) != null
                          ? selected[checkIfExist(i.id)].amount
                          : 1
                      "
                    >
                    <button
                      class="quantity_plus"
                      @click.prevent="increment(i.id, index)"
                    >
                      <span>+</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
         <div v-if="loading" class="tableNoData">
            <ClipLoader :color="'#0054A6'"></ClipLoader>
        </div>
        <div v-if="!loading && accessories.length == 0" class="tableNoData">
          <h1>Duomenų nėra</h1>
        </div>
        </div>
        <button
          type="submit"
          class="buttonLogin"
        >
          Patvirtinti
        </button>
      </div>
    </form>
  </ValidationObserver>
</template>

<script>
import axios from "axios";
import ClipLoader from "vue-spinner/src/ClipLoader.vue";
export default {
  props: ["closeModal", "device", "updateValue", "sendRequest"],
  data() {
    return {
      amount: 1,
      accessories: [],
      selected: [],
      selectAll: false,
      loading: true,
    };
  },
  components:{
    ClipLoader
  },
  mounted: function () {
    this.getAccessories();
  },
  methods: {
    select() {
      this.selected = [];
      if (!this.selectAll) {
        for (let i in this.accessories) {
          this.selected.push({
            id: this.accessories[i].id,
            name: this.accessories[i].name,
            amount: 1,
            src: this.accessories[i].src,
          });
        }
      }
    },

    increment(index, accessoryIndex) {
      var id = this.checkIfExist(index);
      console.log(id);
      if (id != null) {
        console.log(
          this.accessories[accessoryIndex].amount > this.selected[id].amount
        );
        if (
          this.accessories[accessoryIndex].amount > this.selected[id].amount
        ) {
          this.selected[id].amount++;
        }
      }
    },

    decrement(index) {
      var id = this.checkIfExist(index);
      if (id != null) {
        if (this.selected[id].amount > 1) {
          this.selected[id].amount--;
        }
      }
    },

    getAmount(index) {
      var id = this.checkIfExist(index);
      if (id != null) {
        return this.selected[id].amount;
      }
      return 1;
    },

    checkIfExist(index) {
      let count = 0;
      for (let i in this.selected) {
        if (this.selected[i].id == index) {
          return count;
        }
        count++;
      }
      return null;
    },

    getAccessories() {
      axios
        .get("devices/accessories/" + this.device.type + "/" + this.device.id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res);
          this.accessories = res.data.data;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },

    submitAccessories() {
      this.$emit("sendRequest", 0, this.selected);
    },
    close() {
      this.$emit("closeModal");
    },
  },
};
</script>

<style>
@import "../styles/accessoriesSelect.css";
</style>