<template>
  <transition name="fade">
    <div class="modal" v-if="show">
      <div class="modal__backdrop" @click="closeModal()" />
      <ValidationObserver ref="form">
        <form
          v-if="!display"
          @submit.prevent="sendRequest(request.action, [])"
          class="formLogin"
        >
          <div class="autoSelect_backdrop" @click="modal = false" />
          <div class="con">
            <div class="buttonDiv">
              <button type="button" class="modal__close" @click="closeModal()">
                <font-awesome-icon icon="times" />
              </button>
            </div>
            <header class="headerLogin">
              <h2>Prietaisų Veiksmai</h2>
            </header>

            <span>
              <div class="textOnInput">
                <label for="inputText">Pavadinimas</label>
                <div class="inputLogin">
                  {{ device.name }}
                </div>
              </div>
            </span>

            <span>
              <div class="textOnInput">
                <label for="inputText">Kodas</label>
                <div class="inputLogin">
                  {{ device.code }}
                </div>
              </div>
            </span>

            <span>
              <div class="textOnInput">
                <label for="inputText">Serijos numeris</label>
                <div class="inputLogin">
                  {{ device.serialNumber }}
                </div>
              </div>
            </span>

            <span>
              <div class="textOnInput">
                <label class="top" for="inputText">Veiksmas</label>
                <select
                  @change="backEndErrors.clear('action'), getOwner()"
                  class="select-css"
                  v-model="request.action"
                >
                  <option value="0">Pasirinkite veiksmą</option>
                  <option v-if="type != 'Borrowed'" value="1">Perduoti</option>
                  <option value="2">Paskolinti</option>
                  <option v-if="type == 'Borrowed'" value="3">Grąžinti</option>
                </select>
              </div>
              <p v-if="backEndErrors.has('action')">Parisinkite veiksmą</p>
            </span>

            <span>
              <div class="autoSelect">
                <div class="textOnInput">
                  <label for="inputText">Gavėjo-Vardas-Pavardė</label>
                  <ValidationProvider
                    rules="required"
                    mode="eager"
                    v-slot="{ errors }"
                  >
                    <input
                      :disabled="disabled"
                      @change="backEndErrors.clear('user_id')"
                      @input="filterStates"
                      @onBlur="modal = false"
                      @focus="modal = true"
                      :class="
                        filteredUsers.length !== 0 && modal
                          ? 'inputLoginNoBottom'
                          : 'inputLogin'
                      "
                      autocomplete="off"
                      v-model="user"
                    />
                    <p>{{ errors[0] }}</p>
                    <p v-if="backEndErrors.has('user_id')" class="textSize">
                      Vartotojas nerastas
                    </p>
                  </ValidationProvider>
                  <div v-if="filteredUsers.length !== 0 && modal">
                    <ul>
                      <li
                        v-for="(user, index) in filteredUsers"
                        :key="index"
                        @click="setState(user)"
                      >
                        {{ user.name }} - {{ user.surname }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </span>
            <button type="submit" class="buttonLogin">Tęsti</button>
          </div>
        </form>
      </ValidationObserver>
      <AccessoryHandlingModal
        v-if="display"
        v-bind:device="device"
        @closeModal="closeModal"
        @updateValue="updateTable"
        @sendRequest="sendRequest"
      ></AccessoryHandlingModal>
    </div>
  </transition>
</template>

<script>
import { Errors } from "../Errors";
import axios from "axios";
import AccessoryHandlingModal from "../components/AccessoryHandlingModal";
export default {
  name: "DeviceHandleModal",
  components: {
    AccessoryHandlingModal,
  },
  props: ["device", "updateValue", "index", "type", "decoded"],
  data() {
    return {
      request: {
        owner_id: null,
        user_id: null,
        longTerm_id: null,
        shortTerm_id: null,
        action: 0,
        accessories: [],
      },
      display: false,
      modal: false,
      show: false,
      disabled: false,
      user: "",
      users: [],
      filteredUsers: [],
      backEndErrors: new Errors(),
    };
  },
  methods: {
    sendRequest(action, accessories) {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        if (action == 2 && this.decoded.id == this.device.user_id) {
          this.display = true;
        } else {
          this.show = false;
          this.request.accessories = accessories;
          if (action == 3) {
            this.request.owner_id = this.decoded.id;
          }
          axios
            .post("devices/transfer", this.request, {
              headers: {
                Authorization: "Bearer".concat(localStorage["token"]),
              },
            })
            .then((res) => {
              console.log(res);
              this.request.user_id = null;
              this.request.longTerm_id = null;
              this.request.shortTerm_id = null;
              this.request.action = 0;
              this.$emit("updateValue", this.index);
              this.closeModal();
              this.$vToastify.success("Užklausa sėkmingai buvo įvygdyta");
            })
            .catch((err) => {
              console.log(err.response)
              this.backEndErrors.record(err.response.data);
              if (this.backEndErrors.has("longTerm_id")) {
                this.$vToastify.error(this.backEndErrors.get("longTerm_id"));
              } else if (this.backEndErrors.has("shortTerm_id")) {
                this.$vToastify.error(this.backEndErrors.get("shortTerm_id"));
              }
            });
        }
      });
    },
    updateTable() {
      this.$emit("updateValue", this.index);
    },
    closeModal() {
      this.request.action = 0;
      this.show = false;
      this.user = "";
      this.modal = false;
      this.display = false;
    },
    openModal() {
      this.show = true;
      this.getUsers();
    },
    getUsers() {
      axios
        .get("allUsers", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          this.users = res.data;
        })
        .catch((err) => {
          console.log(err.response.data);
        });
    },
    setState(state) {
      this.user = state.name + " " + state.surname;
      this.request.owner_id = this.device.user_id;
      this.request.user_id = state.id;
      if (this.device.type == "LongTerm") {
        this.request.longTerm_id = this.device.id;
      } else {
        this.request.shortTerm_id = this.device.id;
      }
      this.modal = false;
    },
    filterStates() {
      this.filteredUsers = this.users.filter((user) => {
        return (
          user.name.toLowerCase().includes(this.user.toLowerCase()) ||
          user.surname.toLowerCase().includes(this.user.toLowerCase())
        );
      });
    },

    getOwner() {
      if (this.request.action == 3) {
        this.users.forEach((user) => {
          if (user.id == this.device.user_id) {
            this.setState(user);
            this.disabled = true;
            return;
          }
        });
        return;
      }
      this.user = null;
      this.request.user_id = null;
      this.disabled = false;
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>