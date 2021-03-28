<template>
  <div class="container">
    <div class="inner-div">
      <div v-if="!loading" class="inner-div_column_layout_left_row">
        <div class="inner-div_column_layout_left_column_left">
          <div class="inner-div_column_layout_left_baseColumn">
            <div class="img-div">
              <img
                v-if="user.src != null"
                alt=""
                :src="'http://127.0.0.1:8000/storage/' + user.src"
              />
              <img v-else alt="" src="../images/defaultProfile.jpg" />
            </div>
          </div>
        </div>
        <div class="inner-div_column_layout_left_column">
          <div class="nameDiv">
            <p class="nameStyle">{{ user.name }} {{ user.surname }}</p>
          </div>
          <div class="aboutDiv">
            <p class="about">
              So perhaps, you've generated some fancy text, and you're content
              that you can now copy and paste your fancy text in the comments
              section of funny cat videos, but perhaps you're wondering how it's
              even
            </p>
          </div>
          <div class="generalInfo">
            <p class="genaralStyle">Bendra Informacija</p>
          </div>
          <div class="genaralTabelDiv">
            <div class="generalTable">
              <ul>
                <li class="generalTable_label_li">Elektroninis paštas</li>
                <li class="generalTable_label_li">Gimimo metai</li>
                <li class="generalTable_label_li">Adresas</li>
                <li class="generalTable_label_li">Telefono Numeris</li>
              </ul>
              <ul>
                <li class="generalTable_info_li">{{ user.email }}</li>
                <li v-if="user.birth != null" class="generalTable_info_li">
                  {{ user.birth }}
                </li>
                <li v-else class="generalTable_info_li">-------</li>
                <li v-if="user.address != null" class="generalTable_info_li">
                  {{ user.address }}
                </li>
                <li v-else class="generalTable_info_li">-------</li>
                <li
                  v-if="user.phoneNumber != null"
                  class="generalTable_info_li"
                >
                  {{ user.phoneNumber }}
                </li>
                <li v-else class="generalTable_info_li">-------</li>
              </ul>
            </div>
          </div>
          <div class="buttonsDiv">
            <button
              @click="$refs.editProfile.openModal(user)"
              class="buttonsDiv_editButton"
            >
              Redeguoti
            </button>
            <button
              @click="$refs.leaveWork.openModal()"
              class="buttonsDiv_editButton"
            >
              Perduoti visus prietaisus
            </button>
          </div>
        </div>
      </div>
    </div>
    <ProfileEdit @updateProfile="updateProfile" ref="editProfile">
    </ProfileEdit>

    <LeaveWorkModal ref="leaveWork"></LeaveWorkModal>
  </div>
</template>

<script>
import axios from "axios";
import ProfileEdit from "../components/ProfileEdit";
import LeaveWorkModal from "../components/LeaveWorkModal";
import jwt_decode from "jwt-decode";
export default {
  data() {
    return {
      user: {},
      decoded: {},
      loading: true,
    };
  },
  components: {
    ProfileEdit,
    LeaveWorkModal,
  },
  methods: {
    updateProfile(data) {
      this.user = data;
    },
    getUser() {
      this.decoded = jwt_decode(localStorage["token"]);
      var api = "user-profile";
      if (this.$route.params.id != null && this.decoded.admin) {
        api = "users/" + this.$route.params.id;
      }

      axios
        .get(api, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          this.user = res.data;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
  created: function () {
   // this.decoded = jwt_decode(localStorage["token"]);
    this.getUser();
  },
};
</script>

<style scoped >
@import "../styles/profile.css";
</style>