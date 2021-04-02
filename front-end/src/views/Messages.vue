<template>
  <div>
    <div class="generaldata_container">
      <DataBlock class="data_text" :data="generalData.accepted"
        >PATVIRTINTOS UŽKLAUSOS:</DataBlock
      >
      <DataBlock class="data_text" :data="generalData.decliend"
        >ATŠAUKTOS UŽKLAUSOS:</DataBlock
      >
    </div>
    <div class="container">
      <div class="innerDiv">
        <div class="search">
          <div class="textOnInput">
            <label for="inputText">Paieška</label>
            <input
              v-model="searchQuery"
              @input="(current_page = 1), fetchData"
              class="searchInput"
              placeholder="Paieška"
              type="text"
              name="search"
            />
          </div>

          <div class="textOnInput">
            <label for="inputText">Filtras pagal tipą</label>
            <select
              @change="(current_page = 1), fetchData()"
              v-model="filter"
              class="filter"
            >
              <option value="0">Naujos užklausos</option>
              <option value="1">Patvirtintos užklausos</option>
              <option value="-1">Atšauktos užklausos</option>
            </select>
          </div>
        </div>
        <table class="home_table">
          <thead>
            <tr>
              <th>Savininkas</th>
              <th>Prietaiso Pavadinimas</th>
              <th>Prietaiso Kodas</th>
              <th>Veiksmas</th>
              <th>Statusas</th>
              <th>Funkcija</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(message, index) in messages" :key="index">
              <td data-label="Savininkas">
                {{ message.owner_name }} {{ message.owner_surname }}
              </td>

              <td
                data-label="Prietaiso pavadinimas"
                v-if="message.device_name_long"
              >
                {{ message.device_name_long }}
              </td>
              <td
                data-label="Prietaiso pavadinimas"
                v-if="message.device_name_short"
              >
                {{ message.device_name_short }}
              </td>
              <td
                data-label="Prietaiso pavadinimas"
                v-if="
                  message.device_name_short == null &&
                  message.device_name_long == null
                "
              >
                Visi
              </td>
              <td data-label="Prietaiso Kodas" v-if="message.device_code_long">
                {{ message.device_code_long }}
              </td>
              <td data-label="Prietaiso Kodas" v-if="message.device_code_short">
                {{ message.device_code_short }}
              </td>
              <td
                data-label="Prietaiso pavadinimas"
                v-if="
                  message.device_code_long == null &&
                  message.device_code_short == null
                "
              >
                --------------
              </td>
              <td data-label="Veiksmas" v-if="message.action == 1">
                Perdavimas
              </td>
              <td data-label="Veiksmas" v-if="message.action == 2">
                Skolinimas
              </td>
              <td data-label="Veiksmas" v-if="message.action == 3">
                Grąžinimas
              </td>
              <td data-label="Veiksmas" v-if="message.action == null">
                Visų prietaisų grąžinimas
              </td>
              <td data-label="Statusas" v-if="message.device_state == 0">
                Laukia patvirtinimo
              </td>
              <td data-label="Statusas" v-if="message.device_state == 1">
                Patvirtinta
              </td>
              <td data-label="Statusas" v-if="message.device_state == -1">
                Atšaukta
              </td>
              <td
                data-label="Prietaiso pavadinimas"
                v-if="
                  message.device_state == 0 &&
                  message.device_name_short == null &&
                  message.device_name_long == null
                "
              >
                <button
                  @click="confirmRequestAll(message.id, index)"
                  class="iconButton"
                >
                  <font-awesome-icon class="confirmButton" icon="check" />
                </button>
                <button
                  @click="declineRequestAll(message.id, index)"
                  class="iconButton"
                >
                  <font-awesome-icon class="deleteButton" icon="times" />
                </button>
              </td>
              <td
                v-if="
                  message.device_state == 0 &&
                  (message.device_name_short != null ||
                    message.device_name_long != null)
                "
              >
                <button
                  @click="confirmRequest(message.id, index)"
                  class="iconButton"
                >
                  <font-awesome-icon class="confirmButton" icon="check" />
                </button>
                <button
                  @click="declineRequest(message.id, index)"
                  class="iconButton"
                >
                  <font-awesome-icon class="deleteButton" icon="times" />
                </button>
              </td>

              <td v-if="message.device_state != 0">-</td>
            </tr>
          </tbody>
        </table>
        <h1 v-if="messages.length == 0 && !loading">Duomenų nėra</h1>
        <div v-if="messages.length !== 0 && !loading" class="pagination">
          <button
            @click="
              current_page > 1 ? current_page-- : current_page, fetchData()
            "
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
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import DataBlock from "../components/DataBlock";
export default {
  data() {
    return {
      messages: [],
      searchQuery: "",
      filter: 0,
      current_page: 1,
      last_page: null,
      loading: true,
      generalData: [],
    };
  },
  components: {
    DataBlock,
  },
  methods: {
    confirmRequest(id, index) {
      axios
        .get("devices/transfer/confirm/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res.data);
          this.messages[index].device_state = 1;
        })
        .catch((err) => {
          console.log(err.response);
          // this.messages = [];
        });
    },
    declineRequest(id, index) {
      axios
        .get("devices/transfer/decline/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res.data);
          this.messages[index].device_state = -1;
        })
        .catch((err) => {
          console.log(err.response.data);
        });
    },
    confirmRequestAll(id, index) {
      axios
        .get("leaveWork/confirm/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res.data);
          this.messages[index].device_state = 1;
        })
        .catch((err) => {
          console.log(err.response.data);
        });
    },
    declineRequestAll(id, index) {
      axios
        .get("leaveWork/decline/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          console.log(res.data);
          this.messages[index].device_state = -1;
        })
        .catch((err) => {
          console.log(err.response.data);
        });
    },
    fetchData() {
      axios
        .get(
          "users/messages?filter=" +
            this.filter +
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
          this.messages = res.data.data.data;
          this.last_page = res.data.data.last_page;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err.response.data);
          this.loading = false;
          this.messages = [];
        });
    },
    fetchCounts: async function () {
      await axios
        .get("users/messages/generalData", {
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
  },
  created() {
    this.fetchCounts();
    this.fetchData();
  },
};
</script>

<style scoped>
@import "../styles/messages.css";
</style>