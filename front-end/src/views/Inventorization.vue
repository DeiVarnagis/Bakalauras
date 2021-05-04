<template>
  <div class="container">
    <div class="wrapperInventorization">
      <div
        v-if="!loading"
        class="layout_div"
      >
        <FunctionalCalendar
          ref="Calendar"
          v-model="calendarData"
          :configs="calendarConfigs"
          @choseDay="checkIfContains"
        />
        <div class="button_div">
          <button
            :disabled="!isMarkedAdd"
            class="buttonLogin"
            @click="addDate"
          >
            Pridėti
          </button>
          <button
            :disabled="!isMarkedDelete"
            class="buttonLogin"
            @click="deleteDate"
          >
            Pašalinti
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
//import { FunctionalCalendar } from 'vue-functional-calendar';
import Vue from "vue";
import FunctionalCalendar from "vue-functional-calendar";
import axios from "axios";

Vue.use(FunctionalCalendar, {
  dayNames: ["Pr", "An", "Tr", "Kt", "Pn", "Št", "Sk"],
  monthNames: [
    "SAUSIS",
    "VASARIS",
    "KOVAS",
    "BALANDIS",
    "GEGUŽĖ",
    "BIRŽELIS",
    "LIEPA",
    "RUGPJŪTIS",
    "RUGSĖJIS",
    "SPALIS",
    "LAPKRITIS",
    "GRUODIS",
  ],
});

export default {
  data() {
    return {
      onBoot: false,
      isMarkedAdd: false,
      isMarkedDelete: false,
      selectedDateID: null,
      calendarData: {},
      dates: [],
      loading: true,
      calendarConfigs: {
        markedDates: [],
        disabledDates: ["beforeToday"],
        disabledDayNames: "['Št', 'Sk']",
        sundayStart: false,
        dateFormat: "yyyy/mm/dd",
        isDatePicker: true,
        isDateRange: false,
      },
    };
  },
  mounted() {
    this.fetchInventorization();
  },
  methods: {
    convertToJavaScriptDate() {
      this.dates.forEach((element) => {
        this.pushToMarkedDates(element.inventorization_time, element.id)
      });
    },

    pushToMarkedDates(date, id){
      var dateJava = new Date(Date.parse(date));
      this.calendarConfigs.markedDates.push({
          date:
            dateJava.getFullYear() +
            "/" +
            (dateJava.getMonth() + 1) +
            "/" +
            dateJava.getDate(),
          class: "markedDates",
          id: id,
        });
    },

    convertToStandarDate() {
      var date = this.calendarData.selectedDate.split("/");
      if (date[1].length == 1) {
        date[1] = "0" + date[1];
      }
      if (date[2].length == 1) {
        date[2] = "0" + date[2];
      }
      return date[0] + "-" + date[1] + "-" + date[2];
    },

    checkIfContains() {
      var exist = false;
      this.calendarConfigs.markedDates.forEach((element) => {
        if (element.date == this.calendarData.selectedDate) {
          this.selectedDateID = element.id;
          exist = true;
          return;
        }
      });

      if (!exist) {
        this.selectedDateID = null;
        this.isMarkedAdd = true;
        this.isMarkedDelete = false;
      } else {
        this.isMarkedDelete = true;
        this.isMarkedAdd = false;
      }
    },

    fetchInventorization: async function () {
      await axios
        .get("inventorization", {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then((res) => {
          this.dates = res.data;
          this.convertToJavaScriptDate();
          this.loading = false;
        })
    },

    deleteDate: async function () {
      var pos = this.calendarConfigs.markedDates.findIndex(el => el.id == this.selectedDateID );
      await axios
        .delete("inventorization/" + this.selectedDateID, {
          headers: {
            Authorization: "Bearer".concat(localStorage["token"]),
          },
        })
        .then(
          this.$vToastify.success(
            "Inventorizacijos laikas sėkmingai ištrintas"
          ),
          this.calendarConfigs.markedDates.splice(pos,1),
          this.loading = false
        )
    },

    addDate: async function () {
      await axios
        .post(
          "inventorization",
          { inventorization_time: this.convertToStandarDate() },
          {
            headers: {
              Authorization: "Bearer".concat(localStorage["token"]),
            },
          }
        )
        .then((res) => {
          this.$vToastify.success("Inventorizacijos laikas sėkmingai pridėtas");
          this.pushToMarkedDates(res.data.inventorization_time, res.data.id)
        })
    },
  },
};
</script>

<style>
@import "../styles/inventorization.css";
</style>