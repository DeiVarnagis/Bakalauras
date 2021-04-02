<template>
  <transition name="fade">
    <div class="modal" v-if="show">
      <div class="modal__backdrop" @click="closeModal()" />
      <ValidationObserver ref="form">
        <form id="form" @submit.prevent="submitPdf()" class="formLogin">
          <div class="con">
            <div class="buttonDiv">
              <button type="button" class="modal__close" @click="closeModal()">
                <font-awesome-icon icon="times" />
              </button>
            </div>
            <header class="headerLogin">
              <h2>Sugeneruokite pdf</h2>
            </header>

            <span>
              <div class="textOnInput">
                <label class="top" for="inputText">Turto tipas</label>
                <select v-model="type" class="select-css">
                  <option value="1">Ilgalaikis</option>
                  <option value="2">Trumpalaikis</option>
                </select>
              </div>
            </span>

            <button class="buttonLogin">Generuoti</button>
          </div>
        </form>
      </ValidationObserver>
    </div>
  </transition>
</template>

<script>
import axios from "axios";

export default {
  name: "PdfModal",
  props:['decoded'],
  data() {
    return {
      type: 1,
      show: false,
    };
  },

  methods: {
    closeModal() {
      this.show = false;
    },
    openModal() {
      this.show = true;
    },
    submitPdf() {
      axios
        .get("pdf/download/"+ this.decoded.id+ '?type=' + this.type, {
          headers: {
            "Content-type": "application/pdf",
            "Authorization": "Bearer".concat(localStorage["token"]),
          },
          responseType: "arraybuffer",
        })
        .then((res) => {
          console.log(res);
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "ataskaita.pdf");
          document.body.appendChild(link);
          link.click();
        })
        .catch((err) => {
          console.log(err.response);
        });
    },
  },
};
</script>


<style scoped>
@import "../styles/devicesHandling.css";
</style>