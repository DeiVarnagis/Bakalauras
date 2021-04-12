<template>
  <div class="container">
    <div class="inner-div">
      <Tabs class="inner-div_tabs">
        <Tab
          name="Prietaisas"
          :selected="true"
        >
          <div
            v-if="!loading"
            class="inner-div_column_layout_left"
          >
            <div class="inner-div_column_layout_left_row">
              <div class="inner-div_column_layout_left_baseColumn">
                <div class="inner-div_img-div">
                  <img
                    v-if="device.src != null"
                    alt=""
                    :src="'http://127.0.0.1:8000/storage/' + device.src"
                  >
                  <img
                    v-else
                    alt=""
                    src="../images/devices.png"
                  >
                </div>
              </div>

              <div class="inner-div_column_layout_left_column">
                <div class="inner-div_column_layout_left_baseColumn">
                  <h4 class="">
                    Pavadinimas
                  </h4>
                  <div class="inner-div_attributes">
                    {{ device.name }}
                  </div>
                  <h4>Kodas</h4>
                  <div class="inner-div_attributes">
                    {{ device.code }}
                  </div>
                  <h4>Serijos numeris</h4>
                  <div class="inner-div_attributes">
                    {{ device.serialNumber }}
                  </div>
                  <h4>Kiekis</h4>
                  <div class="inner-div_attributes">
                    {{ device.amount }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Tab>
        <Tab
          name="Aksesuarai"
          :selected="false"
        >
          <div>
            <div class="search">
              <button
                :disabled="!disabledButton(device)"
                class="addDevice"
                @click="$refs.addAccessoryModal.openModal()"
              >
                <font-awesome-icon icon="plus" />
              </button>
              <div class="textOnInput">
                <label
                  style="top: -10px"
                  for="inputText"
                >Paieška</label>
                <input
                  v-model="search"
                  class="searchInput"
                  placeholder="Paieška"
                  type="text"
                  name="search"
                  @change="filterAccessories"
                >
              </div>
            </div>
            <div
              v-if="!loading"
              id="scroller"
              class="tableDiv"
            >
              <table
                class="home_table"
                cellspacing="0"
                cellpadding="0"
              >
                <thead>
                  <tr>
                    <th />
                    <th>Pavadinimas</th>
                    <th>Kiekis</th>
                    <th>Data</th>
                    <th v-if="disabledButton(device)">
                      Įrankiai
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(accessory, ids) in resultQuery"
                    :key="ids"
                  >
                    <td>
                      <img
                        v-if="accessory.src == null"
                        class="home_table-img"
                        alt="device"
                        src="../images/devices.png"
                      >
                      <img
                        v-else
                        class="home_table-img"
                        alt=""
                        :src="'http://127.0.0.1:8000/storage/' + accessory.src"
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
                    <td>
                      <button
                        v-if="disabledButton(device)"
                        class="iconButton"
                        @click="
                          clicked(accessory, ids),
                          $refs.editAccessory.openModal(clickedAccessory)
                        "
                      >
                        <img
                          height="30px"
                          src="../assets/edit.svg"
                        >
                      </button>
                      <button
                        v-if="disabledButton(device)"
                        class="iconButton"
                        @click="
                          clicked(accessory, ids), $refs.deleteModal.openModal()
                        "
                      >
                        <img
                          height="30px"
                          src="../assets/delete.png"
                        >
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <h1
                v-if="device.accessories.length == 0"
                class="centered"
              >
                Prietaisas neturi aksesuarų
              </h1>
            </div>
          </div>
        </Tab>
        <Tab
          v-if="device.user_id == decoded.id || decoded.admin"
          name="Istorija"
          :selected="false"
        >
          <DeviceHistory />
        </Tab>
      </Tabs>
    </div>
    <DeleteModal
      ref="deleteModal"
      :device="clickedAccessory"
      :index="index"
      @deleteValue="deleteValue"
    />

    <AccessoryEdit
      :id="clickedAccessory.id"
      ref="editAccessory"
      :index="index"
      @updateAccessory="updateAccessory"
    />

    <AddAccessoryModal
      :id="device.id"
      ref="addAccessoryModal"
      :type="device.type"
      @addAccessory="addAccessory"
    />
  </div>
</template>

<script>
import DeleteModal from "../components/DeleteModal";
import AddAccessoryModal from "../components/AccessoryAddModal";
import AccessoryEdit from "../components/AccessoryEdit";
import DeviceHistory from "../components/DeviceHistory";
import Tabs from "../components/Tabs";
import Tab from "../components/Tab";
import axios from "axios";
import jwt_decode from "jwt-decode";
export default {
  components: {
    DeviceHistory,
    Tab,
    Tabs,
    AccessoryEdit,
    DeleteModal,
    AddAccessoryModal,
  },
  data() {
    return {
      device: {},
      history: [],
      search: "",
      clickedAccessory: {},
      index: null,
      activeItem: "prietaisas",
      loading: true,
      decoded: jwt_decode(localStorage["token"])
    };
  },
  computed: {
    resultQuery() {
      if (this.search) {
        return this.device.accessories.filter((accessory) => {
          return this.search
            .toLowerCase()
            .split(" ")
            .every((v) => accessory.name.toLowerCase().includes(v));
        });
      } else {
        return this.device.accessories;
      }
    },
  },
  created: async function () {
    await this.getDevice();
  },
  methods: {
    isActive(menuItem) {
      return this.activeItem === menuItem;
    },
    setActive(menuItem) {
      this.activeItem = menuItem;
    },

    getDevice: async function () {
      await axios
        .get(
          "devices/" + this.$route.params.type + "/" + this.$route.params.id,
          {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          }
        )
        .then((res) => {
          console.log(res.data);
          this.device = res.data.data;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err.response);
          if (err.response.status == 404) {
            this.$router.push("/notFound");
          }
        });
    },
    addAccessory(data) {
      this.device.accessories.push(data.data);
    },
    clicked(accessory, index) {
      this.clickedAccessory = accessory;
      this.index = index;
    },
    deleteValue(id) {
      this.device.accessories.splice(id, 1);
    },
    updateAccessory(id, data) {
      this.device.accessories[id].name = data.data.name;
      this.device.accessories[id].amount = data.data.amount;
      this.device.accessories[id].src = data.data.src;
    },
    disabledButton(device) {
      if (device.user_id == this.decoded.id || this.decoded.admin) {
        return true;
      }
      return false;
    },
    filterAccessories() {
      console.log("working");
      this.filteredAccessories = this.users.accessories.filter((accessory) => {
        return accessory.name.toLowerCase().includes(this.search.toLowerCase());
      });
    },
  },
};
</script>

<style scoped>
@import "../styles/device.css";
</style>