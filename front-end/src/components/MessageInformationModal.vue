<template>
  <transition name="fade">
    <div
      v-if="show"
      class="modal"
    >
      <div
        class="modal__backdrop"
        @click="closeModal()"
      />
      <form
        id="form"
        class="formLogin"
      >
        <div
          v-if="!loading"
          class="con"
        >
          <div class="buttonDiv">
            <button
              type="button"
              class="modal__close"
              @click="closeModal()"
            >
              <font-awesome-icon icon="times" />
            </button>
          </div>
          <header class="headerLogin">
            <h2>Užklausos duomenys</h2>
          </header>

          <span>
            <div class="textOnDiv">
              <label for="inputText">Pavadinimas</label>
              <div class="inputDiv">{{ device.name }}</div>
            </div>
          </span>

          <span>
            <div class="textOnDiv">
              <label for="inputText">Kodas</label>
              <div class="inputDiv">{{ device.code }}</div>
            </div>
          </span>

          <span>
            <div class="textOnDiv">
              <label for="inputText">Serijos numeris</label>
              <div class="inputDiv">{{ device.serialNumber }}</div>
            </div>
          </span>

          <h4 class="accessoriesH4">
            Aksesuarai
          </h4>

          <div
            id="scroller"
            class="infoTableDiv"
          >
            <table
              class="deviceInfo_table"
              cellspacing="0"
              cellpadding="0"
            >
              <thead>
                <tr>
                  <th />
                  <th>Pavadinimas</th>
                  <th>Kiekis</th>
                  <th>Data</th>
                </tr>
              </thead>
              <tbody v-if="device.accessories.length !== 0">
                <tr
                  v-for="(accessory, index) in device.accessories"
                  :key="index"
                >
                  <td>
                    <img
                      v-if="accessory.src != null"
                      alt=""
                      :src="'http://127.0.0.1:8000/storage/' + accessory.src"
                    >
                    <img
                      v-else
                      alt=""
                      src="../images/devices.png"
                    >
                  </td>
                  <td data-label="Pavadinimas">
                    {{ accessory.name }}
                  </td>
                  <td data-label="Kiekis">
                    {{ accessory.amount }}
                  </td>
                  <td data-label="Data">
                    {{ accessory.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
            <h4
              v-if="device.accessories.length == 0"
              class="accessoriesH4"
            >
              Prietaisas neturi aksesuarų
            </h4>
          </div>
        </div>
      </form>
    </div>
  </transition>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      transfer_id: null,
      device: null,
      show: false,
      loading: true,
    };
  },
  methods: {
    closeModal() {
      this.loading = true;
      this.show = false;
      this.device = null;
    },
    openModal(id) {
      this.transfer_id = id;
      this.getData();
      this.show = true;
    },
    getData() {
      axios
        .get("devices/transfer/info/" + this.transfer_id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res.data);
          this.device = res.data;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
};
</script>

<style>
</style>