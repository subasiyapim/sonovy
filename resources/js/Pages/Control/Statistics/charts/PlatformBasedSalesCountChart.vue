<template>
  <!-- {{Object.keys(usePage().props)}} -->

  <AppCard class="flex-1 w-full min-h-40">
    <template #header>
      <div class="flex items-center">
        <LineChartIcon color="var(--sub-600)"/>
        <div class="flex-1 ms-2"><p class="label-sm c-strong-950 !text-start">
          {{ __('control.statistics.cards.platforms_sale_counts') }}</p></div>

      </div>

    </template>
    <template #tool>
      <div class="flex gap-2">
        <select @change="onPlatformChange" id="platformOptions"
                class="block w-full ps-3 pe-7  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
          <option :selected="platform_id == platform.id" v-for="platform in platforms" :value="platform.id">{{ platform.name }}</option>

        </select>

      </div>

    </template>

    <template #body>
      <template v-if="!loading">
       <hr class="mt-4" v-if="product_id && !(platformSalesCount && platformSalesCount.length > 0)">
        <div v-if="!product_id" class="flex items-center border border-soft-200 rounded mt-6">
            <button @click="onChangeType('audio_streams')" :class="type == 'audio_streams' ? 'bg-weak-50' : 'bg-white' "
                    class="flex border-r border-soft-200 label-xs c-sub-600 flex-1 flex justify-center py-1">Ses Yayını
            </button>

            <button @click="onChangeType('ringtones')" :class="type == 'ringtones' ? 'bg-weak-50' : 'bg-white' "
                    class="flex border-r border-soft-200 label-xs c-sub-600 flex-1 flex justify-center py-1">Zil Sesleri
            </button>
            <button @click="onChangeType('videos')" :class="type == 'videos' ? 'bg-weak-50' : 'bg-white' "
                    class="flex border-r border-soft-200 label-xs c-sub-600 flex-1 flex justify-center py-1">Videolar
            </button>
            <button @click="onChangeType('apple_video')" :class="type == 'songs' ? 'bg-weak-50' : 'bg-white' "
                  class="flex border-r border-soft-200 label-xs c-sub-600 flex-1 flex justify-center py-1">Apple Video
            </button>

        </div>
         <div v-if="platformSalesCount" class="flex flex-col gap-3 p-4">
            <div>
                <div class="flex items-center mb-2 gap-2">


                <p class="label-xl c-strong-950">{{ Object.values(platformSalesCount["Music Release"] ?? {})?.reduce((curr,ar) => curr+ar,0) }}</p>
                <span v-if="percentage != 0" class="label-xs rounded-full px-2 py-0.5"
                        :class="percentage > 0 ? 'bg-[#D8E5ED] text-[#060E2F]' : 'bg-[#FFC0C5] text-[#681219]' ">
                                    <template v-if="percentage >0">
                                        +
                                    </template>
                                    {{ percentage }} %
                                </span>
                </div>


                <div v-if="platform_id" class="flex items-center gap-1">
                <Icon :icon="platforms.find((e) => e.id == platform_id)?.icon" width="18" height="18"/>
                <span
                    class="subheading-2xs c-soft-400 uppercase">{{ platforms.find((e) => e.id == platform_id)?.name }}</span>
                </div>

            </div>
            <div>
                <VueApexCharts
                    type="line"
                    height="250"
                    :options="chartOptions"
                    :series="series"
                ></VueApexCharts>

            </div>
        </div>
        <div v-else class="flex flex-col items-center gap-2 justify-center h-full min-h-60">


          <img src="@/assets/images/empty_state_statistic_platform_sales.png">
          <p class="paragraph-sm c-soft-400">{{ __('control.statistics.cards.empty') }}</p>
        </div>

      </template>
      <template v-else>

        <div class="flex flex-col gap-2 items-center justify-center min-h-32">

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

    </template>

  </AppCard>


</template>

<script setup>
import {ref, onMounted, watch} from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import {AppCard} from '@/Components/Cards';
import {SpotifyIcon, LineChartIcon, Icon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore';
import moment from 'moment';
import { usePage} from '@inertiajs/vue3';
import {router} from '@inertiajs/vue3';

const props = defineProps({
  platformSalesCount: {
    type: Object,
    default: () => ({})
  },
  platforms: {
  },
  product_id: {
    type: [String, Number],
    default: null
  }
})

const series = ref([
  {
    name: 'Sales',
    data: ref(Object.values(props.platformSalesCount["Music Release"] ?? {}))
  },
]);


const chartOptions = ref({
  chart: {
    type: 'line',
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
  xaxis: {
    categories: [],
    labels: {
      show: true,
      style: {
        fontFamily: 'Poppins',
        cssClass: 'subheading-2xs c-soft-400',
      },
      formatter: function(value) {
        return moment(value).format('MMM YY');
      }
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    axisBorder: {
      show: false,
    },
    labels: {
      show: true,
      formatter: function(value) {
        return Math.round(value).toLocaleString();
      }
    },
  },
  grid: {
    show: true,
    borderColor: '#f1f1f1',
    strokeDashArray: 4,
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function(value) {
        return value.toLocaleString();
      },
    },
    x: {
      formatter: function(value) {
        return moment(value).format('MMMM YYYY');
      }
    }
  },
});
let params = new URLSearchParams(window.location.search)

const loading = ref();
const crudStore = useCrudStore();
const period = ref('weekly');
const platform_id = ref(params.get('platform') ?? 2);
const type = ref('audio_streams');

const onPlatformChange = (e) => {
  platform_id.value = e.target.value;
  console.log("GELDİİ");

  router.visit(route(route().current()), {
    replace: true,
    preserveState: true,
    preserveScroll: true,
    data: { ...route().params, platform: platform_id.value },
  });
}

const onChangeType = (e) => {
  type.value = e;
//   getData();
}


const totalSales = ref(0);
const percentage = ref(0);

const getData = async () => {
  loading.value = true;
  try {

    let payload = {
      "period": period.value,
      "platform_id": platform_id.value,
      "type": type.value,
    }
    if (props.product_id) {
      payload['product_id'] = props.product_id;
    }
    const response = await crudStore.post(route('control.statistics.platform.sales'), payload);
    series.value[0].data = response.data;
    totalSales.value = response.total;
    percentage.value = response.percentage;
    console.log("REPSONSE", response);


  } catch (error) {
    loading.value = false;
  }
  loading.value = false;
}

</script>

<style scoped>
/* Add any custom styles here */
</style>
