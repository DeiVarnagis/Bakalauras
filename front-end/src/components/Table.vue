<template>
  <div>
    <table class="home_table" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th></th>
          <th>Kodas</th>
          <th>Pavadinimas</th>
          <th>Serijos Numeris</th>
          <th>Kiekis</th>
          <th>Tipas</th>
          <th>Statusas</th>
          <th v-if="decoded.admin && type == 'All'">Vardas</th>
          <th v-if="decoded.admin && type == 'All'">Pavardė</th>
          <th>Įrankiai</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(device, index) in devices" :key="index">
          <td style="--minWidth: 50px">
            <img
              v-if="device.src != null"
              alt=""
              :src="'http://127.0.0.1:8000/storage/' + device.src"
            />
            <img v-else alt="" src="../images/devices.png" />
          </td>
          <td data-label="Kodas" style="--minWidth: 100px">
            {{ device.code }}
          </td>
          <td data-label="Pavadinimas" style="--minWidth: 200px">
            {{ device.name }}
          </td>
          <td data-label="Serijos Numeris" style="--minWidth: 200px">
            {{ device.serialNumber }}
          </td>
          <td data-label="Kiekis" style="--minWidth: 75px">
            {{ device.amount }}
          </td>
          <td
            data-label="Tipas"
            style="--minWidth: 75px"
            v-if="device.type == 'LongTerm'"
          >
            Ilgalaikis
          </td>
          <td data-label="Tipas" v-if="device.type == 'ShortTerm'">
            Trumpalaikis
          </td>
          <td v-if="device.state == 0">Laisvas</td>
          <td v-if="device.state == 1">Laukia užklausoje</td>
          <td v-if="device.state == 2">Paskolintas</td>
          <td v-if="decoded.admin && type == 'All'">{{device.user.name}}</td>
          <td v-if="decoded.admin && type == 'All'">{{device.user.surname}}</td>
          <td>
            <button
              :disabled="disabledDolly(device)"
              class="iconButton"
              @click="clicked(device, index), $refs.handleDevice.openModal()"
            >
              <font-awesome-icon
                :class="
                  disabledDolly(device) ? 'disabledButton' : 'confirmButton'
                "
                icon="dolly"
              />
            </button>

            <router-link :to="'/device/' + device.type + '/' + device.id">
              <button class="iconButton" @click="clicked(device, index)">
                <font-awesome-icon class="confirmButton" icon="eye" /></button
            ></router-link>

            <button
              :disabled="disabledButton(device)"
              class="iconButton"
              @click="
                clicked(device, index, device.type),
                  $refs.updateDevice.openModal(clickedDevice)
              "
            >
              <font-awesome-icon
                :class="
                  disabledButton(device) ? 'disabledButton' : 'confirmButton'
                "
                icon="edit"
              />
            </button>
            <button
              :disabled="disabledButton(device)"
              class="iconButton"
              @click="
                clicked(device, index, device.type),
                  $refs.deleteModal.openModal()
              "
            >
              <font-awesome-icon
                :class="
                  disabledButton(device) ? 'disabledButton' : 'confirmButton'
                "
                icon="trash-alt"
              />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <DeviceUpdateModal
      v-bind:id="clickedDevice.id"
      v-bind:index="index"
      @updateDevice="updateDevice"
      ref="updateDevice"
    >
    </DeviceUpdateModal>

    <DeviceHandlingModal
      v-bind:device="clickedDevice"
      @updateValue="updateValue"
      v-bind:index="index"
      v-bind:type="type"
      v-bind:decoded="decoded"
      ref="handleDevice"
    ></DeviceHandlingModal>

    <DeleteModal
      v-bind:device="clickedDevice"
      @deleteValue="deleteValue"
      v-bind:index="index"
      ref="deleteModal"
    ></DeleteModal>
  </div>
</template>

<script>
import DeviceHandlingModal from "../components/DeviceHandlingModal";
import DeleteModal from "../components/DeleteModal";
import DeviceUpdateModal from "../components/DeviceUpdateModal";
export default {
  data() {
    return {
      disabled: false,
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
  props: ["devices", "type", "decoded"],
  components: {
    DeviceHandlingModal,
    DeleteModal,
    DeviceUpdateModal,
  },
  methods: {
    clicked(device, index, typ) {
      this.clickedDevice = device;
      this.index = index - (this.current_page - 1) * 8;
      if (this.deviceType == null) {
        this.clickedType = typ;
        return;
      }
      this.clickedType = this.deviceType;
    },
    updateValue(id) {
      this.devices[id].state = 1;
    },
    deleteValue(id) {
      this.devices.splice(id, 1);
    },
    updateDevice(id, data) {
      this.devices[id].name = data.name;
      this.devices[id].code = data.code;
      this.devices[id].serialNumber = data.serialNumber;
      this.devices[id].amount = data.amount;
      this.devices[id].type = data.deviceType;
      this.devices[id].src = data.src;
    },
    disabledDolly(device) {
      if (device.state == 0 || (this.type == "Borrowed" && device.state == 2)) {
        return false;
      }
      return true;
    },
    disabledButton(device) {
      if (device.user_id == this.decoded.id || this.decoded.admin) {
        return false;
      }
      return true;
    },
  },
};
</script>

<style>
</style>