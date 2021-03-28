<template>
  <div class="container">
    <div class="inner-div">
      <Tabs class="inner-div_tabs">
        <Tab name="Prietaisas" :selected="true">
          <div v-if="!loading" class="inner-div_column_layout_left">
            <div class="inner-div_column_layout_left_row">
              <div class="inner-div_column_layout_left_baseColumn">
                <div class="inner-div_img-div">
                  <img
                    v-if="device.src != null"
                    alt=""
                    :src="'http://127.0.0.1:8000/storage/' + device.src"
                  />
                  <img v-else alt="" src="../images/devices.png" />
                </div>
              </div>

              <div class="inner-div_column_layout_left_column">
                <div class="inner-div_column_layout_left_baseColumn">
                  <h4 class="">Pavadinimas</h4>
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
        <Tab name="Aksesuarai" :selected="false">
          <div class="search">
            <button
              :disabled="!disabledButton(device)"
              class="addDevice"
              @click="$refs.addAccessoryModal.openModal()"
            >
              <font-awesome-icon icon="plus" />
            </button>
            <div class="textOnInput">
              <label style="top: -10px" for="inputText">Paieška</label>
              <input
                class="searchInput"
                placeholder="Paieška"
                type="text"
                v-model="search"
                @change="filterAccessories"
                name="search"
              />
            </div>
          </div>
          <div v-if="!loading" class="tableDiv" id="scroller">
            <table class="home_table" cellspacing="0" cellpadding="0">
              <thead>
                <tr>
                  <th></th>
                  <th>Pavadinimas</th>
                  <th>Kiekis</th>
                  <th>Data</th>
                  <th v-if="disabledButton(device)">Įrankiai</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(accessory, index) in resultQuery" :key="index">
                  <td>
                    <img
                      v-if="accessory.src == null"
                      alt="device"
                      src="../images/devices.png"
                    />
                    <img
                      v-else
                      alt=""
                      :src="'http://127.0.0.1:8000/storage/' + accessory.src"
                    />
                  </td>
                  <td data-label="Pavadinimas">{{ accessory.name }}</td>
                  <td data-label="Kiekis">{{ accessory.amount }}</td>
                  <td data-label="Data">{{ accessory.created_at }}</td>
                  <td>
                    <button
                      v-if="disabledButton(device)"
                      class="iconButton"
                      @click="
                        clicked(accessory, index),
                          $refs.editAccessory.openModal(clickedAccessory)
                      "
                    >
                      <font-awesome-icon class="confirmButton" icon="edit" />
                    </button>
                    <button
                      v-if="disabledButton(device)"
                      class="iconButton"
                      @click="
                        clicked(accessory, index), $refs.deleteModal.openModal()
                      "
                    >
                      <font-awesome-icon
                        class="confirmButton"
                        icon="trash-alt"
                      />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <h1 class="centered" v-if="device.accessories.length == 0">
              Prietaisas neturi aksesuarų
            </h1>
          </div>
        </Tab>
        <Tab
          v-if="device.user_id == decoded.id"
          name="Istorija"
          :selected="false"
        >
          <DeviceHistory></DeviceHistory>
        </Tab>
      </Tabs>
    </div>
    <DeleteModal
      v-bind:device="clickedAccessory"
      @deleteValue="deleteValue"
      v-bind:index="index"
      ref="deleteModal"
    ></DeleteModal>

    <AccessoryEdit
      v-bind:id="clickedAccessory.id"
      v-bind:index="index"
      @updateAccessory="updateAccessory"
      ref="editAccessory"
    >
    </AccessoryEdit>

    <AddAccessoryModal
      :type="device.type"
      :id="device.id"
      @addAccessory="addAccessory"
      ref="addAccessoryModal"
    >
    </AddAccessoryModal>
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
  data() {
    return {
      device: {},
      history: [],
      search: "",
      clickedAccessory: {},
      index: null,
      activeItem: "prietaisas",
      loading: true,
      decoded: null,
    };
  },
  components: {
    DeviceHistory,
    Tab,
    Tabs,
    AccessoryEdit,
    DeleteModal,
    AddAccessoryModal,
  },
  methods: {
    isActive(menuItem) {
      return this.activeItem === menuItem;
    },
    setActive(menuItem) {
      this.activeItem = menuItem;
    },

    getDevice: async function () {
      this.decoded = jwt_decode(localStorage["token"]);
      console.log(this.decoded, "user");
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
          console.log(res);
          this.device = res.data.data;
          this.loading = false;
        })
        .catch((err) => {
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
      var decoded = jwt_decode(localStorage["token"]);
      if (device.user_id == decoded.id) {
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
};
</script>

<style scoped>
@import "../styles/device.css";
</style>