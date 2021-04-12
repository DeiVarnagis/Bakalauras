<template>
  <div>
    <div class="generaldata_container">
      <DataBlock
        class="data_text"
        :data="generalData.allDevices"
      >
        PRIETAISŲ SKAIČIUS:
      </DataBlock>
      <DataBlock
        class="data_text"
        :data="generalData.borrowed"
      >
        PASISKOLINTI PRIETAISAI:
      </DataBlock>
      <DataBlock
        class="data_text"
        :data="null"
      >
        <vue-countdown
          v-slot="{ days, hours, minutes }"
          :time="time"
        >
          INVENTORIZACIJA: {{ days }} Dienos, {{ hours }} valandos,
          {{ minutes }} minutės.
        </vue-countdown>
      </DataBlock>
    </div>
    <div class="container">
      <div class="innerDiv">
        <div class="search">
          <div>
            <button
              class="addDevice"
              @click="$refs.addModal.openModal()"
            >
              <font-awesome-icon icon="plus" />
            </button>
          </div>
          <div class="textOnInput">
            <label for="inputText">Paieška</label>
            <input
              v-model="searchQuery"
              class="searchInput"
              placeholder="Paieška"
              type="text"
              name="search"
              @input="(current_page = 1), fetchData()"
            >
          </div>

          <div class="textOnInput">
            <label for="inputText">Filtras pagal tipą</label>
            <select
              v-model="type"
              class="filter"
              @change="(current_page = 1), fetchData()"
            >
              <option value="Yours">
                Visas turtas
              </option>
              <option value="ShortTerm">
                Trumpalaikis turtas
              </option>
              <option value="LongTerm">
                Ilgalaikis turtas
              </option>
              <option value="Borrowed">
                Pasiskolintas turtas
              </option>
              <option
                v-if="decoded.admin"
                value="All"
              >
                Kitų turtas
              </option>
            </select>
          </div>

          <div class="textOnInput">
            <label for="inputText">Filtras pagal statusą</label>
            <select
              v-model="filter"
              class="filter"
              @change="(current_page = 1), fetchData()"
            >
              <option value="all">
                Visi
              </option>
              <option value="0">
                Laisvi
              </option>
              <option value="1">
                Laukiantis užklausoje
              </option>
              <option value="2">
                Paskolintas turtas
              </option>
            </select>
          </div>

          <button
            class="pdfButton"
            @click="$refs.pdfModal.openModal()"
          >
            PDF
          </button>
        </div>
        <Table
          :decoded="decoded"
          :type="type"
          :devices="devices"
        />
        <div v-if="loading && devices.length == 0">
          <h1
            v-if="devices.length == 0"
            class="centered"
          >
            Duomenų nėra
          </h1>
        </div>
        <div
          v-if="devices.length !== 0"
          class="pagination"
        >
          <button
            @click="
              current_page > 1 ? current_page-- : current_page, fetchData()
            "
          >
            <font-awesome-icon
              class="arrow"
              icon="chevron-left"
            />
          </button>
          <button
            v-for="(times, ids) in last_page"
            :key="ids"
            :class="[times == current_page ? 'pageSelected' : 'page']"
            @click="(current_page = times), fetchData()"
          >
            {{ times }}
          </button>
          <button
            @click="
              current_page < last_page ? current_page++ : current_page,
              fetchData()
            "
          >
            <font-awesome-icon
              class="arrow"
              icon="chevron-right"
            />
          </button>
        </div>
        <DeviceAddModal ref="addModal" />
        <PdfGeneratorModal
          ref="pdfModal"
          :decoded="decoded"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Table from "../components/Table";
import axios from "axios";
import DeviceAddModal from "../components/DeviceAddModal";
import PdfGeneratorModal from "../components/PdfGeneratorModal";
import DataBlock from "../components/DataBlock";
import jwt_decode from "jwt-decode";
import VueCountdown from "@chenfengyuan/vue-countdown";

export default {
  components: {
    PdfGeneratorModal,
    DeviceAddModal,
    DataBlock,
    VueCountdown,
    Table,
  },
  data() {
    return {
      devices: [],
      generalData: [],
      time: null,
      type: "Yours",
      filter: "all",
      deviceType: null,
      clickedType: null,
      last_page: null,
      current_page: 1,
      searchQuery: "",
      clickedDevice: {},
      index: null,
      loading: false,
      decoded: jwt_decode(localStorage["token"]),
    };
  },
  created: async function () {
    await this.fetchData();
    await this.fetchCounts();
    await this.fetchInventorization();
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

          this.devices = res.data.data.data;
        })
        .catch((err) => {
          console.log(err.response.data);
          this.devices = [];
        });
    },
    fetchCounts: async function () {
      await axios
        .get("users/devices/count", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          this.generalData = res.data;
        })
        .catch((err) => {
          console.log(err);
        });
    },

    fetchInventorization: async function () {
      await axios
        .get("inventorization-closest", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          const now = new Date();
          const endDate = new Date(res.data);
          this.time = this.getDifferenceInSeconds(now, endDate);
          this.loading = true;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getDifferenceInSeconds(date1, date2) {
      var diff = date2.getTime() - date1.getTime();
      return Math.abs(Math.round(diff));
    },
  },
};
</script>
<style scoped>
@import "../styles/table.css";
</style>
