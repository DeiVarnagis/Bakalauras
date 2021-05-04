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
      <ValidationObserver ref="form">
        <form
          class="formLogin"
          @submit.prevent="sendRequest"
        >
          <div
            class="autoSelect_backdrop_leaveWork"
            @click="modal = false"
          />
          <div class="con">
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
              <h2>Perduokite visus savo prietaisus</h2>
            </header>

            <span>
              <div class="autoSelect">
                <div class="textOnInput">
                  <label for="inputText">Gavėjo-Vardas-Pavardė</label>
                  <ValidationProvider
                    v-slot="{ errors }"
                    rules="required"
                    mode="eager"
                  >
                    <input
                      v-model="user"
                      :class="
                        filteredUsers.length !== 0 && modal
                          ? 'inputLoginNoBottom'
                          : 'inputLogin'
                      "
                      autocomplete="off"
                      @change="backEndErrors.clear('user_id')"
                      @input="filterStates"
                      @onBlur="modal = false"
                      @focus="modal = true"
                    >
                    <p>{{ errors[0] }}</p>
                    <p
                      v-if="backEndErrors.has('user_id')"
                      class="textSize"
                    >
                      Vartotojas nerastas
                    </p>
                  </ValidationProvider>
                  <div v-if="filteredUsers.length !== 0 && modal">
                    <ul>
                      <li
                        v-for="(customer, index) in filteredUsers"
                        :key="index"
                        @click="setState(customer)"
                      >
                        {{ customer.name }} - {{ customer.surname }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </span>

            <button
              type="submit"
              class="buttonLogin"
            >
              Patvirtinti
            </button>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </transition>
</template>

<script>
import { Errors } from "../Errors";
import axios from "axios";
export default {
  data() {
    return {
      user_id: null,
      modal: false,
      show: false,
      user: "",
      users: [],
      filteredUsers: [],
      backEndErrors: new Errors(),
    };
  },
  methods: {
    sendRequest() {
      this.$refs.form.validate().then((success) => {
        if (!success) {
          return;
        }
        axios
          .post(
            "leaveWork",
            { user_id: this.user_id },
            {
              headers: {
                Authorization: "Bearer".concat(localStorage["token"]),
              },
            }
          )
          .then(
            this.user_id = null,
            this.user = "",
            this.closeModal(),
            this.$vToastify.success("Užklausa sėkmingai buvo išsiųsta")
          )
          .catch((err) => {
            if (err.response.data.errors) {
              this.backEndErrors.record(err.response.data);
            } else {
              this.$vToastify.error(err.response.data.error);
            }
          });
      });
    },
    closeModal() {
      this.show = false;
      this.user = "";
      this.modal = false;
      this.user_id = null;
    },
    openModal() {
      this.show = true;
       axios
        .get("allUsers", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          this.users = res.data;
        })
    },
    filterStates() {
      this.filteredUsers = this.users.filter((user) => {
        return (
          user.name.toLowerCase().includes(this.user.toLowerCase()) ||
          user.surname.toLowerCase().includes(this.user.toLowerCase())
        );
      });
    },
    setState(state) {
      this.user = state.name + " " + state.surname;
      this.user_id = state.id;
      this.modal = false;
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>