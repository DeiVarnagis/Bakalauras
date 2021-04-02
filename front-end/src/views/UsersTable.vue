<template>
  <div>
    <div class="generaldata_container">
      <DataBlock class="data_text" :data="usersCount">VARTOTOJŲ SKAIČIUS:</DataBlock>
    </div>
    <div class="container">
      <div class="innerDiv">
        <div class="search">
          <div>
            <button class="addDevice" @click="$refs.addUser.openModal()">
              <font-awesome-icon icon="plus" />
            </button>
          </div>
          <div class="textOnInput">
            <label for="inputText">Paieška</label>
            <input
              v-model="searchQuery"
              @input="(current_page = 1), getUsers()"
              class="searchInput"
              placeholder="Paieška"
              type="text"
              name="search"
            />
          </div>
        </div>
        <table class="home_table" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th></th>
              <th>Vardas</th>
              <th>Pavardė</th>
              <th>Adresas</th>
              <th>Gimimo metai</th>
              <th>Telefonas</th>
              <th>Elektroninis Paštas</th>
              <th>Rolė</th>
              <th>Įrankiai</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="index">
              <td style="--minWidth: 50px">
                <img
                  v-if="user.src != null"
                  alt=""
                  :src="'http://127.0.0.1:8000/storage/' + user.src"
                />
                <img v-else alt="" src="../images/defaultProfile.jpg" />
              </td>
              <td data-label="Vardas">
                {{ user.name }}
              </td>
              <td data-label="Pavardė">
                {{ user.surname }}
              </td>
              <td
                v-if="user.address"
                data-label="Adresas"
                style="--minWidth: 200px"
              >
                {{ user.address }}
              </td>

              <td v-else data-label="Adresas" style="--minWidth: 200px">-</td>
              <td v-if="user.birth" data-label="Gimimo metai">
                {{ user.birth }}
              </td>
              <td v-else data-label="Telefonas" style="--minWidth: 200px">-</td>
              <td v-if="user.phoneNumber" data-label="Gimimo metai">
                {{ user.phoneNumber }}
              </td>
              <td v-else data-label="Gimimo metai">-</td>
              <td data-label="Elektroninis Paštas">
                {{ user.email }}
              </td>
              <td
                v-if="user.admin"
                data-label="Adminas"
                style="--minWidth: 75px"
              >
                Adminas
              </td>
              <td v-else data-label="Adminas" style="--minWidth: 75px">
                Vartotojas
              </td>
              <td>
                <router-link :to="'/profile/' + user.id">
                  <button class="iconButton">
                    <font-awesome-icon class="confirmButton" icon="eye" />
                  </button>
                </router-link>
                <button
                  @click="
                    (clickedIndex = index), $refs.editProfile.openModal(user)
                  "
                  class="iconButton"
                >
                  <font-awesome-icon class="confirmButton" icon="edit" />
                </button>
                <button
                  @click="(clickedUser = user), $refs.deleteModal.openModal()"
                  class="iconButton"
                >
                  <font-awesome-icon class="confirmButton" icon="trash-alt" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!loading && users.length == 0">
          <h1 class="centered" v-if="devices.length == 0">Duomenų nėra</h1>
        </div>
        <div v-if="!loading" class="pagination">
          <button
            @click="
              current_page > 1 ? current_page-- : current_page, getUsers()
            "
          >
            <font-awesome-icon class="arrow" icon="chevron-left" />
          </button>
          <button
            v-for="(times, index) in this.last_page"
            @click="(current_page = times), getUsers()"
            :key="index"
            v-bind:class="[times == current_page ? 'pageSelected' : 'page']"
          >
            {{ times }}
          </button>
          <button
            @click="
              current_page < last_page ? current_page++ : current_page,
                getUsers()
            "
          >
            <font-awesome-icon class="arrow" icon="chevron-right" />
          </button>
        </div>
      </div>
      <AddUserModal v-bind:addUser="true" ref="addUser"></AddUserModal>
      <ProfileEdit
        ref="editProfile"
        @updateProfile="updateProfile"
      ></ProfileEdit>
      <DeleteModal
        v-bind:device="clickedUser"
        @deleteValue="deleteValue"
        ref="deleteModal"
      ></DeleteModal>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import jwt_decode from "jwt-decode";
import DataBlock from "../components/DataBlock";
import AddUserModal from "../components/AddUserModal";
import ProfileEdit from "../components/ProfileEdit";
import DeleteModal from "../components/DeleteModal";
export default {
  data() {
    return {
      clickedIndex: null,
      clickedUser: {},
      users: [],
      decoded: null,
      last_page: null,
      searchQuery: "",
      current_page: 1,
      loading: true,
      usersCount:0
    };
  },
  components: {
    DeleteModal,
    AddUserModal,
    ProfileEdit,
    DataBlock
  },
  methods: {
    logout() {
      axios
        .post("logout", "", {
          headers: {
            Authorization: "bearer" + " " + localStorage["token"],
          },
        })
        .then(localStorage.removeItem("token"), this.$router.push("/login"));
    },
    getUsers() {
      axios
        .get(
          "users?search=" + this.searchQuery + "&page=" + this.current_page,
          {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          }
        )
        .then((res) => {
          console.log(res.data);
          this.last_page = res.data.data.last_page;
          this.users = res.data.data.data;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err.response);
        });
    },
      getUsersCount() {
      axios
        .get("users/count",
          {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          }
        )
        .then((res) => {
          this.usersCount = res.data;
        })
        .catch((err) => {
          console.log(err)
        });
    },
    updateProfile(data) {
      this.users[this.clickedIndex].name = data.name;
      this.users[this.clickedIndex].surname = data.surname;
      this.users[this.clickedIndex].phoneNumber = data.phoneNumber;
      this.users[this.clickedIndex].address = data.address;
      this.users[this.clickedIndex].email = data.email;
      this.users[this.clickedIndex].birth = data.birth;
      this.users[this.clickedIndex].src = data.src;
    },
    deleteValue() {
      this.users.splice(this.clickedIndex + 1, 1);
    },
  },

  created: function () {
    this.decoded = jwt_decode(localStorage["token"]);
    if (!this.decoded.admin) {
      this.logout();
      this.$vToastify.error("Jūs neautorizuotas šiam veiksmui");
    }
    this.getUsersCount();
    this.getUsers();
  },
};
</script>

<style>
</style>