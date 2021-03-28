<template>
  <section class="app">
    <div class="sidebar">
      <header>
        <a href="/"
          ><img alt="teltonika" src="../images/Teltonika-logo.png"
        /></a>
      </header>
      <nav class="sidebar-nav">
        <ul>
          <li>
            <a href="/">Prietaisai</a>
          </li>
          <li>
            <div>
              <a href="/messages"
                >Užklausos
                <notification-bell
                  :size="25"
                  :count="messages"
                  :upperLimit="50"
                  counterLocation="upperRight"
                  counterStyle="round"
                  counterBackgroundColor="#FF0000"
                  counterTextColor="#FFFFFF"
                  iconColor="#000000"
              /></a>
            </div>
          </li>
          <li v-if="!loading && decoded.admin">
             <a href="/usersTable">Vartotojai</a>
          </li>
          <li>
            <a href="/profile">Profilis</a>
          </li>
          <li>
            <a href="/login" @click.prevent="logout()">Atsijungti</a>
          </li>
        </ul>
      </nav>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import jwt_decode from "jwt-decode";
import NotificationBell from "vue-notification-bell";
export default {
  data() {
    return {
      messages: null,
      decoded:null,
      loading:true,
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
  },
  async mounted() {
    this.decoded = jwt_decode(localStorage["token"]);
    this.loading=false;
    this.messagesCount();
  },
};
</script>

<style scoped>
@import "../styles/navbar.css";
</style>