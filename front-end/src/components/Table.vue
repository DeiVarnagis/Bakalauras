<template>
  <div>
    <table
      class="home_table"
      cellspacing="0"
      cellpadding="0"
    >
      <thead>
        <tr>
          <th />
          <th v-column-sortable:code>
            Kodas
          </th>
          <th v-column-sortable:name>
            Pavadinimas
          </th>
          <th v-column-sortable:serialNumber>
            Serijos Numeris
          </th>
          <th v-column-sortable:amount>
            Kiekis
          </th>
          <th v-column-sortable:type>
            Tipas
          </th>
          <th v-column-sortable:state>
            Statusas
          </th>
          <th
            v-if="decoded.admin && type == 'All'"
            v-column-sortable:user.name
          >
            Vardas
          </th>
          <th
            v-if="decoded.admin && type == 'All'"
            v-column-sortable:user.surname
          >
            Pavardė
          </th>
          <th>Įrankiai</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(device, ids) in devices"
          :key="ids"
        >
          <td style="--minWidth: 50px">
            <img
              v-if="device.src != null"
              class="home_table-img"
              alt=""
              :src="'http://127.0.0.1:8000/storage/' + device.src"
            >
            <img
              v-else
              class="home_table-img"
              alt=""
              src="../images/devices.png"
            >
          </td>
          <td
            data-label="Kodas"
            style="--minWidth: 100px"
          >
            {{ device.code }}
          </td>
          <td
            data-label="Pavadinimas"
            style="--minWidth: 200px"
          >
            {{ device.name }}
          </td>
          <td
            data-label="Serijos Numeris"
            style="--minWidth: 200px"
          >
            {{ device.serialNumber }}
          </td>
          <td
            data-label="Kiekis"
            style="--minWidth: 75px"
          >
            {{ device.amount }}
          </td>
          <td
            v-if="device.type == 'LongTerm'"
            data-label="Tipas"
            style="--minWidth: 75px"
          >
            Ilgalaikis
          </td>
          <td
            v-if="device.type == 'ShortTerm'"
            data-label="Tipas"
          >
            Trumpalaikis
          </td>
          <td v-if="device.state == 0">
            Laisvas
          </td>
          <td v-if="device.state == 1">
            Laukia užklausoje
          </td>
          <td v-if="device.state == 2">
            Paskolintas
          </td>
          <td v-if="decoded.admin && type == 'All' && device.user != null">
            {{ device.user.name }}
          </td>
          <td v-if="decoded.admin && type == 'All' && device.user != null">
            {{ device.user.surname }}
          </td>
          <td>
            <button
              :disabled="disabledDolly(device)"
              :class="disabledDolly(device) ? 'disabledButton' : 'iconButton'"
              @click="clicked(device, ids), $refs.handleDevice.openModal()"
            >
              <img
                height="30px"
                src="../assets/move.svg"
              >
            </button>

            <router-link :to="'/device/' + device.type + '/' + device.id">
              <button
                class="iconButton"
                @click="clicked(device, ids)"
              >
                <img
                  height="30px"
                  src="../assets/eye.svg"
                >
              </button>
            </router-link>

            <button
              :disabled="disabledButton(device)"
              :class="disabledButton(device) ? 'disabledButton' : 'iconButton'"
              @click="
                clicked(device, ids, device.type),
                $refs.updateDevice.openModal(clickedDevice)
              "
            >
              <img
                height="30px"
                src="../assets/edit.svg"
              >
            </button>

            <button
              :disabled="disabledButton(device)"
              :class="disabledButton(device) ? 'disabledButton' : 'iconButton'"
              @click="
                clicked(device, ids, device.type), $refs.deleteModal.openModal()
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
    <DeviceUpdateModal
      :id="clickedDevice.id"
      ref="updateDevice"
      :index="index"
      @updateDevice="updateDevice"
    />

    <DeviceHandlingModal
      ref="handleDevice"
      :device="clickedDevice"
      :index="index"
      :type="type"
      :decoded="decoded"
      @updateValue="updateValue"
    />

    <DeleteModal
      ref="deleteModal"
      :device="clickedDevice"
      :index="index"
      @deleteValue="deleteValue"
    />
  </div>
</template>

<script>
import columnSortable from "vue-column-sortable";
import DeviceHandlingModal from "../components/DeviceHandlingModal";
import DeleteModal from "../components/DeleteModal";
import DeviceUpdateModal from "../components/DeviceUpdateModal";
import Vue from "vue";

new Vue({
  directives: { columnSortable },
});

export default {
  components: {
    DeviceHandlingModal,
    DeleteModal,
    DeviceUpdateModal,
  },
  directives: {
    columnSortable,
  },
  props: ["devices", "type", "decoded"],
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
    updateValue(id, action) {
      this.devices[id].state = 1;
      this.devices[id].action = action;
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
      if ((device.user_id == this.decoded.id && device.state == 0) ||(this.decoded.admin && device.state == 0)  || (this.type == "Borrowed" && device.action == 0)) 
      {
        return false;
      }
      return true;
    },

    disabledButton(device) {
      if ((device.user_id == this.decoded.id && device.state == 0) ||(this.decoded.admin && device.state == 0)) 
      {
        return false;
      }
      return true;
    },
    orderBy(sortFn) {
      this.devices.sort(sortFn);
    },
  },
};
</script>

<style>
</style>