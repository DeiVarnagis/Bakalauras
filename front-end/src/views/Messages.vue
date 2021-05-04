<template>
  <div>
    <div class="generaldata_container">
      <DataBlock class="data_text" :data="generalData.accepted">
        PATVIRTINTOS UŽKLAUSOS:
      </DataBlock>
      <DataBlock class="data_text" :data="generalData.decliend">
        ATŠAUKTOS UŽKLAUSOS:
      </DataBlock>
    </div>
    <div class="container">
      <div class="innerDiv">
        <div class="search">
          <div class="textOnInput">
            <label for="inputText">Paieška</label>
            <input
              v-model="searchQuery"
              class="searchInput"
              placeholder="Paieška"
              type="text"
              name="search"
              @input="(current_page = 1), fetchData"
            />
          </div>

          <div class="textOnInput">
            <label for="inputText">Filtras pagal tipą</label>
            <select
              v-model="filter"
              class="filter"
              @change="(current_page = 1), fetchData()"
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
              <th>Peržiūrėti</th>
              <th>Funkcija</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(message, index) in messages" :key="index">
              <td data-label="Savininkas">
                {{ message.owner_name }} {{ message.owner_surname }}
              </td>

              <td
                v-if="message.device_name_long"
                data-label="Prietaiso pavadinimas"
              >
                {{ message.device_name_long }}
              </td>
              <td
                v-if="message.device_name_short"
                data-label="Prietaiso pavadinimas"
              >
                {{ message.device_name_short }}
              </td>
              <td
                v-if="
                  message.device_name_short == null &&
                  message.device_name_long == null
                "
                data-label="Prietaiso pavadinimas"
              >
                Visi
              </td>
              <td v-if="message.device_code_long" data-label="Prietaiso Kodas">
                {{ message.device_code_long }}
              </td>
              <td v-if="message.device_code_short" data-label="Prietaiso Kodas">
                {{ message.device_code_short }}
              </td>
              <td
                v-if="
                  message.device_code_long == null &&
                  message.device_code_short == null
                "
                data-label="Prietaiso pavadinimas"
              >
                --------------
              </td>
              <td v-if="message.action == 1" data-label="Veiksmas">
                Perdavimas
              </td>
              <td v-if="message.action == 2" data-label="Veiksmas">
                Skolinimas
              </td>
              <td v-if="message.action == 3" data-label="Veiksmas">
                Grąžinimas
              </td>
              <td v-if="message.action == null" data-label="Veiksmas">
                Visų prietaisų grąžinimas
              </td>

              <td v-if="message.device_state == 0" data-label="Statusas">
                Laukia patvirtinimo
              </td>
              <td v-if="message.device_state == 1" data-label="Statusas">
                Patvirtinta
              </td>
              <td v-if="message.device_state == -1" data-label="Statusas">
                Atšaukta
              </td>
              <td v-if="message.device_code_long || message.device_code_short">
                <button
                  class="iconButton"
                  @click="$refs.deviceInfo.openModal(message.id)"
                >
                  <img height="30px" src="../assets/eye.svg" />
                </button>
              </td>
              <td v-else>-</td>
              <td
                v-if="
                  message.device_state == 0 &&
                  message.device_name_short == null &&
                  message.device_name_long == null
                "
                data-label="Prietaiso pavadinimas"
              >
                <button
                  class="iconButton"
                  @click="confirmRequestAll(message.id, index)"
                >
                  <font-awesome-icon class="confirmButton" icon="check" />
                </button>
                <button
                  class="iconButton"
                  @click="declineRequestAll(message.id, index)"
                >
                  <font-awesome-icon class="confirmButton" icon="times" />
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
                  class="iconButton"
                  @click="confirmRequest(message.id, index)"
                >
                  <font-awesome-icon class="confirmButton" icon="check" />
                </button>
                <button
                  class="iconButton"
                  @click="declineRequest(message.id, index)"
                >
                  <font-awesome-icon class="confirmButton" icon="times" />
                </button>
              </td>

              <td v-if="message.device_state != 0">-</td>
            </tr>
          </tbody>
        </table>
        <div v-if="loading" class="tableNoData">
          <ClipLoader :color="'#0054A6'"></ClipLoader>
        </div>
        <div v-if="!loading && messages.length == 0" class="tableNoData">
          <h1>Duomenų nėra</h1>
        </div>
        <div v-if="messages.length !== 0" class="pagination">
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
            <font-awesome-icon class="arrow" icon="chevron-right" />
          </button>
        </div>
      </div>
      <MessageInformationModal ref="deviceInfo" />
    </div>
  </div>
</template>

<script>
import ClipLoader from "vue-spinner/src/ClipLoader.vue";
import axios from "axios";
import DataBlock from "../components/DataBlock";
import MessageInformationModal from "../components/MessageInformationModal";
export default {
  components: {
    ClipLoader,
    DataBlock,
    MessageInformationModal,
  },
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
  created() {
    this.fetchCounts();
    this.fetchData();
  },
  methods: {
    confirmRequest(id, index) {
      axios
        .get("devices/transfer/confirm/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((this.messages[index].device_state = 1))
        .catch();
    },
    declineRequest(id, index) {
      axios
        .get("devices/transfer/decline/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((this.messages[index].device_state = -1))
        .catch();
    },
    confirmRequestAll(id, index) {
      axios
        .get("leaveWork/confirm/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((this.messages[index].device_state = 1))
        .catch();
    },
    declineRequestAll(id, index) {
      axios
        .get("leaveWork/decline/" + id, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((this.messages[index].device_state = -1))
        .catch();
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
    },
    fetchCounts: async function () {
      await axios
        .get("users/messages/generalData", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
            this.generalData = res.data
        })
        .catch();
    },
  },
};
</script>

<style scoped>
@import "../styles/messages.css";
</style>