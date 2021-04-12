<template>
  <div>
    <div class="container">
      <div
        class="innerDiv"
        style="overflow: auto"
      >
        <AreaChart />
        <LineChart />
        <PieChart />
      </div>
    </div>
  </div>
</template>

<script>
import AreaChart from "../components/AreaChart.vue";
import LineChart from "../components/LineChart.vue";
import PieChart from "../components/PieChart.vue";

export default {
  name: "App",
  components: {
    AreaChart,
    LineChart,
    PieChart
  },
  data() {
    return {
      chartData: {
        Books: 24,
        Magazine: 30,
        Newspapers: 10,
      },
    };
  },
};
</script>

<style >
  #line-chart{
    margin-bottom: 20px;
  }
    #area-chart{
    margin-bottom: 20px;
  }
    #pie-chart{
    margin-bottom: 20px;
  }
</style>
