<template>

  <div class="h-48 relative ">
    <VueApexCharts height="350"  type="donut" :options="chartOptions" :series="series"  />
    <div class="flex flex-col items-center absolute bottom-8 left-0 right-0 mx-auto w-full ">
        <p class="subheading-xs c-sub-600">TOPLAM STREAM</p>
        <span class="label-xl c-strong-950">{{Object.values(props.data.platforms).reduce((acc,curr) => curr+acc,0)?.toLocaleString()}}</span>
    </div>
  </div>
</template>

<script setup>
import { defineComponent, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
const props = defineProps({
    data:{},

})
const series = ref(Object.values(props.data.platforms)); // Adjust the percentages as needed
    const chartOptions = ref({
      chart: {
        type: 'donut',
      },
  plotOptions: {
    pie: {
      startAngle: -90, // Rotate the donut chart
      endAngle: 90, // Make it a half donut (half of a full circle)
      offsetY: 0, // Adjust the vertical position of the chart if needed
      donut: {
        size: '70%', // Make the donut thicker
      },
    },
  },
      labels: ['Spotify', 'Apple Music','Diğerleri'],
      stroke: {
        width: 0, // Remove border around slices
      },
      dataLabels: {
        enabled: false, // Disable data labels (optional)
      },
      legend: {
        show:false,
        position: 'top', // Position of the legend
        offsetY: 0, // Offset the legend vertically
      },
      colors: ['#335CFF', '#47C2FF','#E1E4EA'], // Customize colors
    });
</script>

<style scoped>
/* You can add additional custom styles here */
</style>
