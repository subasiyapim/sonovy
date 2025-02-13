<template>


  <AppCard class="col-span-2 flex-1 w-full">
    <template #header>
      <div class=" flex flex-col items-start ">
        <p class="paragraph-sm c-sub-600">Aylık Dinlenme (Ort)</p>
        <div class="flex items-center gap-2">
          <p class="label-xl c-strong-950">{{ monthlyStats?.average || 0 }}</p>
          <span v-if="percentage != null && percentage != 0" class="label-xs rounded-full px-2 py-0.5"
                :class="percentage > 0 ? 'bg-[#D8E5ED] text-[#060E2F]' : 'bg-[#FFC0C5] text-[#681219]' ">
            <template v-if="percentage > 0">
              +
            </template>
            {{ percentage || 0 }} %
          </span>
        </div>
      </div>
    </template>
    <template #tool>
      <div class="flex items-start gap-2 ">

        <div v-if="platforms" class="flex items-center gap-0.5">

            <div v-html="p.icon" class="small-svg" v-for="p in platforms.slice(0,8)">
            </div>


          <div class="bg-weak-50 ms-0.5 border border-soft-200 rounded-full w-4 h-4 flex items-center justify-center">
            <p class="subheading-2xs !text-[8px] c-sub-600">+{{platforms.length-8}}</p>
          </div>
        </div>
      </div>

    </template>
    <template #body>
      <VueApexCharts
          v-if="!loading && series?.[0]?.data?.length > 0"
          type="area"
          height="200"
          :options="chartOptions"
          :series="series"
      ></VueApexCharts>
      <div v-else class="flex flex-col gap-2 items-center justify-center min-h-32">

        <svg aria-hidden="true" class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
             viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
              fill="#fff"/>
          <path
              d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
              fill="currentFill"/>
        </svg>
        <p class="label-sm c-strong-950">Yükleniyor</p>
      </div>
    </template>

  </AppCard>

</template>

<script setup>
import {ref, onMounted, watch} from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import {useCrudStore} from '@/Stores/useCrudStore';
import {AppCard} from '@/Components/Cards';
import {SpotifyIcon,Icon} from '@/Components/Icons';

const props = defineProps({
  product_id: {
    type: [String, Number],
    default: null
  },
  platforms:{},
  monthlyStats: {
    type: Object,
    default: () => ({
      labels: [],
      series: [],
      total: 0,
      percentage: 0,
      average: 0
    })
  }
})

const loading = ref(true);
const crudStore = useCrudStore();
const series = ref([
  {
    name: 'Dinlenme',
    data: props.monthlyStats?.series || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
  },
]);

const chartOptions = ref({
  chart: {
    type: 'area',
    toolbar: {
      show: false,
    },

  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    colors: ['#335CFF'],
    width: 2,
  },
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      gradientToColors: ['#417BB5', '#E4E5E700'], // O
      opacityFrom: 0.4,
      opacityTo: 0,
      stops: [0, 90]
    }

  },
  xaxis: {
    categories: props.monthlyStats?.labels || ['OCA', 'SUB', 'MAR', 'NIS', 'MAY', 'HAZ', 'TEM', 'AGU', 'EYL', 'EKI', 'KAS', 'ARA'],
    labels: {
      show: true,
      style: {
        fontFamily: 'Poppins',
        cssClass: 'subheading-2xs c-soft-400',
      },
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: true,
    },
  },
  yaxis: {
    axisBorder: {
      show: false,
    },
    labels: {
      style: {
        fontFamily: 'Poppins',
        cssClass: 'subheading-2xs c-soft-400',
      },
      show: true, // Ensure labels are visible
      formatter: function (value) {
        return (value || 0) / 1000 + 'K';
      },
    },
  },
  grid: {
    show: true, // Show grid lines
    borderColor: '#e0e0e0',
    xaxis: {
      lines: {
        show: true, // Show vertical lines (X-axis grid)
      },
    },
    yaxis: {
      lines: {
        show: false, // Hide horizontal lines (Y-axis grid)
      },
    },
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function (value) {
        return (value || 0) / 1000 + 'K';
      },
    },
  },
});
const period = ref('weekly')
const percentage = ref(props.monthlyStats?.percentage || 0)

watch(() => props.monthlyStats, (newStats) => {
  if (newStats) {
    series.value[0].data = newStats.series || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    chartOptions.value.xaxis.categories = newStats.labels || ['OCA', 'SUB', 'MAR', 'NIS', 'MAY', 'HAZ', 'TEM', 'AGU', 'EYL', 'EKI', 'KAS', 'ARA'];
    percentage.value = newStats.percentage || 0;
  }
}, { deep: true, immediate: true });

watch(() => props.monthlyStats, () => {
  loading.value = false;
}, { immediate: true });
</script>

<style >
.small-svg svg{
    width:18px !important;
    height:18px !important;
}
</style>
