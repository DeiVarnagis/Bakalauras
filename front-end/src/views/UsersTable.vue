<template>
  <div>
    <div class="generaldata_container">
      <DataBlock class="data_text" :data="usersCount">
        VARTOTOJŲ SKAIČIUS:
      </DataBlock>
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
              class="searchInput"
              placeholder="Paieška"
              type="text"
              name="search"
              @input="(current_page = 1), getUsers()"
            />
          </div>
        </div>
        <table class="home_table" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th />
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
                  class="home_table-img"
                  v-if="user.src != null"
                  alt=""
                  :src="'http://127.0.0.1:8000/storage/' + user.src"
                />
                <img
                  class="home_table-img"
                  v-else
                  alt=""
                  src="../images/defaultProfile.jpg"
                />
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
                    <img height="30px" src="../assets/eye.svg" />
                  </button>
                </router-link>
                <button
                  class="iconButton"
                  @click="
                    (clickedIndex = index), $refs.editProfile.openModal(user)
                  "
                >
                  <img height="30px" src="../assets/edit.svg" />
                </button>
                <button
                  class="iconButton"
                  @click="(clickedUser = user), $refs.deleteModal.openModal()"
                >
                  <img height="30px" src="../assets/delete.png" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="loading" class="tableNoData">
          <div v-if="loading">
            <ClipLoader :color="'#0054A6'"></ClipLoader>
          </div>
        </div>
        <div v-if="!loading && users.length == 0" class="tableNoData">
          <h1 v-if="devices.length == 0" class="centered">Duomenų nėra</h1>
        </div>

        <div v-if="users.length !== 0" class="pagination">
          <button
            @click="
              current_page > 1 ? current_page-- : current_page, getUsers()
            "
          >
            <font-awesome-icon class="arrow" icon="chevron-left" />
          </button>
          <button
            v-for="(times, index) in last_page"
            :key="index"
            :class="[times == current_page ? 'pageSelected' : 'page']"
            @click="(current_page = times), getUsers()"
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
      <AddUserModal ref="addUser" :add-user="true" />
      <ProfileEdit ref="editProfile" @updateProfile="updateProfile" />
      <DeleteModal
        ref="deleteModal"
        :device="clickedUser"
        @deleteValue="deleteValue"
        :who="'user'"
      />
    </div>
  </div>
</template>

<script>
import ClipLoader from "vue-spinner/src/ClipLoader.vue";
import axios from "axios";
import DataBlock from "../components/DataBlock";
import AddUserModal from "../components/AddUserModal";
import ProfileEdit from "../components/ProfileEdit";
import DeleteModal from "../components/DeleteModal";
export default {
  components: {
    ClipLoader,
    DeleteModal,
    AddUserModal,
    ProfileEdit,
    DataBlock,
  },
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
      usersCount: 0,
    };
  },

  created: function () {
    this.getUsersCount();
    this.getUsers();
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
        .get("users/count", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          this.usersCount = res.data;
        })
        .catch((err) => {
          console.log(err);
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
};
</script>

<style>
</style>