<template>
  <div class="container">
    <div class="innerDiv">
      <div class="search">
        <div>
          <button class="addDevice" @click="$refs.addModal.openModal()">
            <font-awesome-icon icon="plus" />
          </button>
        </div>
        <div class="textOnInput">
          <label for="inputText">Paieška</label>
          <input
            v-model="searchQuery"
            @input="(current_page = 1), fetchData()"
            class="searchInput"
            placeholder="Paieška"
            type="text"
            name="search"
          />
        </div>

        <div class="textOnInput">
          <label for="inputText">Filtras pagal tipą</label>
          <select
            v-model="type"
            class="filter"
            @change="(current_page = 1), fetchData()"
          >
            <option value="All">Tavo įrenginiai</option>
            <option value="ShortTerm">Trumpalaikiai įrenginiai</option>
            <option value="LongTerm">Ilgalaikiai įrenginiai</option>
            <option value="Borrowed">Pasiskolinti įrenginiai</option>
          </select>
        </div>

        <div class="textOnInput">
          <label for="inputText">Filtras pagal statusą</label>
          <select
            v-model="filter"
            class="filter"
            @change="(current_page = 1), fetchData()"
          >
            <option value="all">Visi</option>
            <option value="0">Laisvi</option>
            <option value="1">Laukiantis užklausoje Įrenginiai</option>
            <option value="2">Paskolinti Įrenginiai</option>
          </select>
        </div>
      </div>
      <Table v-bind:type="type" v-bind:devices="devices"></Table>
      <div v-if="loading && devices.length == 0">
        <h1 class="centered" v-if="devices.length == 0">Duomenų nėra</h1>
      </div>
      <div v-if="devices.length !== 0" class="pagination">
        <button
          @click="current_page > 1 ? current_page-- : current_page, fetchData()"
        >
          <font-awesome-icon class="arrow" icon="chevron-left" />
        </button>
        <button
          v-for="(times, index) in last_page"
          :key="index"
          @click="(current_page = times), fetchData()"
          v-bind:class="[times == current_page ? 'pageSelected' : 'page']"
        >
          {{ times }}
        </button>
        <button
          @click="
            current_page < last_page ? current_page++ : current_page,
              fetchData()
          "
        >
          <font-awesome-icon class="arrow" icon="chevron-right" />
        </button>
      </div>
      <DeviceAddModal ref="addModal"> </DeviceAddModal>
    </div>
  </div>
</template>

<script>
import Table from "../components/Table";
import axios from "axios";
import DeviceAddModal from "../components/DeviceAddModal";
 
export default {
  data() {
    return {
      devices: [],
      type: "All",
      filter: "all",
      deviceType: null,
      clickedType: null,
      last_page: null,
      current_page: 1,
      searchQuery: "",
      clickedDevice: {},
      index: null,
      loading: false,
    };
  },
  components: {
    DeviceAddModal,
    Table,
  },
  methods: {
    fetchData: async function () {
      await axios
        .get(
          "userDevices?state=" +
            this.filter +
            "&type=" +
            this.type +
            "&search=" +
            this.searchQuery +
            "&page=" +
            this.current_page,
          {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          }
        )
        .then((res) => {
          this.last_page = res.data.data.last_page;
          this.loading = true;
          this.devices = res.data.data.data;
          return this.devices;
        })
        .catch((err) => {
          console.log(err.response.data);
          this.devices = [];
        });
    },
  },
  created: async function () {
    await this.fetchData();
  },
};
</script>
<style scoped>
@import "../styles/table.css";
</style>
