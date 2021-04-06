<template>
  <div>
    <div class="navi_wrap" :class="toggledNav ? 'open' : 'closed'">
      <div class="toggle-menu">
        <a class="unselectable" @click="toggledNav = !toggledNav">
          <img height="30px" src="../assets/menu.svg" />
        </a>
      </div>
      <div class="navi_links">
        <a
          class="unselectable navi_link"
          @click="push('Home')"
          :class="{ text_active: $route.name == 'Home' }"
        >
          <img height="30px" src="../assets/devices.svg" />
          <span class="link-text">INVENTORIUS</span>
        </a>
        
        <a
          class="unselectable navi_link"
          @click="push('Messages')"
          :class="{ text_active: $route.name == 'Messages' }"
        >
        <div style=" margin-right: 20px;">
            <notification-bell
            :size="30"
            :count="messages"
            :upperLimit="50"
            counterLocation="upperRight"
            counterStyle="round"
            counterBackgroundColor="#FF0000"
            counterTextColor="#FFFFFF"
            iconColor="#000000"
          />
        </div>
          <span class="link-text">ĮVYKIAI</span>
        </a>

            <a
          v-if="decoded.admin"
          class="unselectable navi_link"
          @click="push('Statistics')"
          :class="{ text_active: $route.name == 'Statistics' }"
        >
          <img height="30px" src="../assets/statistics.svg" />
          <span class="link-text">STATISTIKA</span>
        </a>

          <a
          v-if="decoded.admin"
          class="unselectable navi_link"
          @click="push('usersTable')"
          :class="{ text_active: $route.name == 'usersTable' }"
        >
          <img height="30px" src="../assets/workers.svg" />
          <span class="link-text">VARTOTOJAI</span>
        </a>

          <a
          class="unselectable navi_link"
          @click="push('Profile')"
          :class="{ text_active: $route.name == 'Profile' }"
        >
          <img height="30px" src="../assets/profile.svg" />
          <span class="link-text">PROFILIS</span>
        </a>

            <a
          class="unselectable navi_link"
          @click="logout()"
        >
          <img height="30px" src="../assets/logout.svg" />
          <span class="link-text">ATSIJUNGTI</span>
        </a>

      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import jwt_decode from "jwt-decode";
import NotificationBell from "vue-notification-bell";
import Pusher from "pusher-js";
export default {
  data() {
    return {
      listActive: false,
      toggledNav: true,
      messages: null,
      loading: true,
      decoded: jwt_decode(localStorage["token"]),
    };
  },
  components: {
    NotificationBell,
  },
  methods: {
    logout() {
      axios
        .post("logout", "", {
          headers: {
            Authorization: "bearer" + " " + localStorage["token"],
          },
        })
        .then((res) => {
          localStorage.removeItem("token");
          this.$router.push("/login");
          this.$vToastify.success(res.data.message);
        });
    },
    messagesCount() {
      axios
        .get("users/messages/count", {
          headers: {
            Authorization: "Bearer " + localStorage["token"],
          },
        })
        .then((res) => {
          this.messages = res.data;
        });
    },
    push(route) {
      if (this.$route.name !== route) {
        this.$router.push({ name: route });
      }
    },
    watchCount() {
      Pusher.logToConsole = true;

      var pusher = new Pusher("911c4c72971affe5f999", {
        cluster: "eu",
      });

      var channel = pusher.subscribe("notiflication-channel");
      channel.bind(
        "NotificationSend",
        (data) => {
          if (this.decoded.id == data.id) {
            console.log("hello")
            this.messages = data.count;
          }
        },
        this
      );
    },
  },

  async mounted() {
    this.loading = false;
    this.messagesCount();
    this.watchCount();
  },
};
</script>

<style scoped>
@import "../styles/navbar.css";
</style>