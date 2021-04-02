<script>
import { Line } from "vue-chartjs";

export default {
  extends: Line,
  mounted() {
    this.renderChart(
      {
        labels: [
          "Sausis",
          "Vasaris",
          "Kovas",
          "Balandis",
          "Gegužė",
          "Birželis",
          "Liepa",
        ],
        datasets: [
          {
            label: "Vartotojų skaičius",
            data: [2, 10, 5, 9, 0, 6, 20],
            backgroundColor: "transparent",
            borderColor: "rgba(1, 116, 188, 0.50)",
            pointBackgroundColor: "rgba(171, 71, 188, 1)",
          },
        ],
      },

      {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: "Naujų vartotojų skaičiaus prieaugis",
        },
      }
    );
  },
};
</script>