<template>
  <AdminLayout  :hasPadding="false"  :showDatePicker="false"  :title="__('control.statistics.header')" >
    <template #breadcrumb>
      <span class="label-xs c-soft-400 cursor-pointer" @click="router.visit(route('control.statistics.index'))">İstatistikler</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400">
         Albüm İstatistikleri
      </span>
    </template>
    <!-- {{usePage().props.platformStatistics}} -->
    <template #toolbar>
        <div class="w-48">
            <VueDatePicker @update:model-value="onDateChoosen"  v-model="choosenDate"  range  month-picker :max-date="new Date()"  class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">

                <template #input-icon>
                    <div class="p-3">
                        <CalendarIcon color="var(--sub-600)"/>
                    </div>
                </template>
                <template #left-sidebar>
                        <div class="flex flex-col flex-1">
                            <button @click="setDateRange('last30days')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 1 Ay</button>
                            <button @click="setDateRange('last3months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 3 Ay</button>
                            <button @click="setDateRange('last6months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 6 Ay</button>
                            <button @click="setDateRange('last12months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 12 Ay</button>
                            <button @click="setDateRange('allTime')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Tüm Zamanlar</button>
                        </div>
                </template>
            </VueDatePicker>
        </div>

    </template>

    <div class="bg-white-400 h-44 p-6 relative mb-24">
      <div class="">
        <div class="flex items-center gap-2">
            <h1 class="label-xl c-strong-950" v-text="product.album_name"/>
             <div class="whitespace-nowrap border border-soft-200 rounded-lg px-2 py-1 flex items-center gap-2">
                <component :is="statusData.find((e) => e.value == product.status)?.icon"
                        :color="statusData.find((e) => e.value == product.status)?.color"></component>
                <p class="subheading-xs c-sub-600">
                {{ statusData.find((e) => e.value == product.status)?.label }}
                </p>
            </div>
        </div>

        <div class="flex flex-1 items-center justify-start gap-2 flex-wrap  py-4">
            <div class="flex items-center" v-for="artist in filterMainArtists ">
                <div class="w-6 h-6 bg-blue-300 rounded-full overflow-hidden me-2">
                    <img :alt="artist.name"
                        :src="artist.image ? artist.image.thumb : defaultStore.profileImage(artist.name)"
                        class="border-2 border-white rounded-full "
                    >
                </div>
                <p class="label-sm c-sub-600 me-1">{{ artist.name }}</p>
               <span class="label-sm c-neutral-500"> •</span>
            </div>

            <button @click="showAllArtists = !showAllArtists" class="c-neutral-500 label-xs hover:underline" v-if="props.product.main_artists.length > 2">
              <template v-if="!showAllArtists">  +5 Sanatçı Daha </template>
              <template v-else>  Daha az göster </template>
            </button>
        </div>
      </div>

      <div
          class="absolute rounded-lg w-[120px] h-[120px] bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="product.album_name"
             :src="product.image ? product.image.thumb : defaultStore.profileImage(product.album_name)">
      </div>
      <div class="flex items-center justify-end gap-2 absolute top-5 right-5 w-[600px] flex-wrap">

        <div v-for="(platform,index) in product.download_platforms" class="flex items-center gap-1">
            <Icon  :icon="platform.icon" />
           <span class="paragraph-sm c-sub-600 w-max"> {{platform.name}}</span>
           <template v-if="index < product.download_platforms?.length - 1"><span class="label-sm c-soft-400 mx-2">•</span> </template>
        </div>
      </div>
    </div>
    <div class="px-6">

        <div class="flex grid grid-cols-3 gap-3 mb-5">

         <MonthlyListeningChart :monthly-stats="monthlyStats"/>

      <div class="flex flex-col gap-3">
        <AppCard>
          <template #header>
            <div class="flex items-center gap-2">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <Music2LineIcon color="var(--sub-600)"/>
              </div>
              <div class="flex flex-col items-start ">
                <p class="subheading-2xs c-soft-400">PARÇA İNDİRMELERİ</p>
                <div class="flex items-center gap-2">
                    <p class="label-medium c-strong-950">{{ downloadCounts?.songs?.toLocaleString() || 0 }}</p>


                </div>
              </div>
            </div>
          </template>
        </AppCard>
        <AppCard>
          <template #header>
            <div class="flex items-center gap-2">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <StackOverflowIcon color="var(--sub-600)"/>
              </div>
              <div class="flex flex-col items-start ">
                <p class="subheading-2xs c-soft-400">ALBÜM İNDİRMELERİ</p>
                <div class="flex items-center gap-2">

                  <p class="label-medium c-strong-950">{{ downloadCounts?.albums?.toLocaleString() || 0 }}</p>


                </div>
              </div>
            </div>
          </template>
        </AppCard>

        <AppCard>
          <template #header>
            <div class="flex items-center gap-2">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <VideoLineIcon color="var(--sub-600)"/>
              </div>
              <div class="flex flex-col items-start ">
                <p class="subheading-2xs c-soft-400">VIDEO İNDİRMELERİ</p>

               <div class="flex items-center gap-2">

                  <p class="label-medium c-strong-950">{{ downloadCounts?.videos?.toLocaleString() || 0 }}</p>


                </div>
              </div>
            </div>
          </template>
        </AppCard>

      </div>

        </div>
        <div class="flex grid grid-cols-2 gap-3 mb-5">
            <AppCard class="flex-1 w-full">
                <template #header>
                        <div class="flex items-center">
                            <PieChartIcon color="var(--sub-600)"/>
                            <div class="flex-1 ms-2"><p class="label-sm c-strong-950 !text-start">{{ __('control.statistics.cards.platforms') }}</p></div>

                        </div>
                </template>
                <template #body>
                    <hr class="my-6">

                    <div v-if="platformStatistics && (platformStatistics?.spotify > 0 || platformStatistics?.apple > 0 || platformStatistics?.other > 0)" class="flex flex-col gap-4">
                        <div>
                            <PlatformsTotalStreamChart :data="{ platforms: platformStatistics || { spotify: 0, apple: 0, other: 0 } }"/>
                        </div>
                        <hr>
                        <div class="flex items-center h-24">
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <SpotifyIcon width="32" height="32"/>
                            <p class="paragraph-xs c-sub-600">Spotify</p>
                            <p class="label-sm c-strong-950">{{ (platformStatistics?.spotify || 0).toLocaleString() }}</p>
                            <div class="w-2 h<-2 bg-blue-500 rounded-full"></div>
                        </div>
                        <div class="bg-soft-200 w-[1px] h-full"></div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <AppleMusicIcon width="32" height="32"/>
                            <p class="paragraph-xs c-sub-600">Apple Music</p>
                            <p class="label-sm c-strong-950">{{ (platformStatistics?.apple || 0).toLocaleString() }}</p>
                            <div class="w-2 h-2 bg-[#47C2FF] rounded-full"></div>
                        </div>
                        <div class="bg-soft-200 w-[1px] h-full"></div>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-8 h-8 bg-soft-200 rounded-full flex items-center justify-center">
                            <OthersIcon color="var(--sub-600)"/>
                            </div>
                            <p class="paragraph-xs c-sub-600">Diğer</p>
                            <p class="label-sm c-strong-950">{{ (platformStatistics?.other || 0).toLocaleString() }}</p>
                            <div class="w-2 h-2 bg-soft-200 rounded-full"></div>
                        </div>
                        </div>
                    </div>

                    <div v-else class="flex flex-col items-center gap-2 justify-center h-full min-h-60">
                        <img src="@/assets/images/empty_state_statistic_platforms.png">
                        <p class="paragraph-sm c-soft-400">{{ __('control.statistics.cards.empty') }}</p>
                    </div>
                    </template>
            </AppCard>

            <PlatformBasedSalesCountChart :platform-sales-count="platformSalesCount || {}" :product_id="props.product.id" :platforms="platforms" />




        </div>


        <div>
        <AppTabs
            :slug="currentTab"
            :tabs="tabs"
            class="my-5"
            @change="onTabChange"
        />
        </div>

        <div v-if="loading" class="flex justify-center items-center py-8">
            <div class="loading-spinner"></div>
        </div>
        <div v-else>
            <component :is="tabs.find((e) => e.slug == currentTab)?.component"
                    :tableData="tab" />
        </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed, watch, nextTick, onMounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {AppCard} from '@/Components/Cards';

import MonthlyListeningChart from './charts/monthly_listening_charts.vue';
import PlatformsTotalStreamChart from './charts/platforms_total_stream_chart.vue';
import PlatformBasedSalesCountChart from './charts/PlatformBasedSalesCountChart.vue';

import moment from 'moment';
import 'moment/dist/locale/tr';
moment.locale('tr');




import {AppTabs} from '@/Components/Widgets'
import {PrimaryButton, IconButton, RegularButton} from '@/Components/Buttons'
import {StatusBadge} from '@/Components/Badges'
import {
  AddIcon,
  SpotifyIcon,
  AppleMusicIcon,
  CloseIcon,
  OthersIcon,
  CalendarIcon,
  StackOverflowIcon,
    VideoLineIcon,
    Music2LineIcon,
    PieChartIcon,
    LineChartIcon,
    Icon,
    EditLineIcon,
    WarningIcon,
    RetractedIcon,
    CheckFilledIcon
} from '@/Components/Icons'
import {router} from '@inertiajs/vue3';

import {NewReportModal} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";



import PlatformsTab from './product_tabs/platforms.vue';
import CountriesTab from './product_tabs/countries.vue';


const defaultStore = useDefaultStore();
const pageTable = ref();

const showAllArtists = ref(false);

const data  = ref([]);

const statusData = ref([
  {
    label: "Taslak",
    value: 1,
    icon: EditLineIcon,
    color: "#FF8447",

  },
  {
    label: "İnceleniyor",
    value: 2,
    icon: EditLineIcon,
    color: "#FF8447",
  },
  {
    label: "Yayınlandı",
    value: 3,
    icon: CheckFilledIcon,
    color: "#335CFF",
  },
  {
    label: "Reddedildi",
    value: 4,
    icon: WarningIcon,
    color: "#FB3748",
  },
  {
    label: "Geri Çekildi",
    value: 5,
    icon: RetractedIcon,
    color: "#717784",
  },
  {
    label: "Planlandı",
    value: 6,
    icon: CheckFilledIcon,
    color: "#FF8447",
  }
]);

const setDateRange = (type) => {
  const now = new Date();
  let range = [];

  switch (type) {
    case 'last30days':

        range = [
            { month: moment().subtract(1, 'months').month(), year: moment().subtract(1, 'months').year() },
            { month: moment().month(), year: moment().year() },
        ];
        break;
    case 'last3months':
      range = [
        { month: moment().subtract(3, 'months').month(), year: moment().subtract(3, 'months').year() },
          { month: moment().month(), year: moment().year() },
      ];
      break;
    case 'last6months':
      range = [
        { month: moment().subtract(6, 'months').month(), year: moment().subtract(6, 'months').year() },
          { month: moment().month(), year: moment().year() },
      ];
      break;
    case 'last12months':


      range = [
        { month: moment().subtract(1, 'year').month(), year: moment().subtract(1, 'year').year() },
        { month: moment().month(), year: moment().year() },
      ];
      break;

    case 'allTime':
      range = [
        {
          month: 0,
          year: 1970,
        },
         { month: moment().month(), year: moment().year() },
      ];
      break;
    default:
      range = [];
  }
  console.log("RANGEE",range);

  onDateChoosen(range)
};


const filterMainArtists = computed(() => {
    if(props.product.main_artists.length <= 2){
          return props.product.main_artists;
    }else {
        if(showAllArtists.value){
            return props.product.main_artists;
        }else {
            return props.product.main_artists.slice(0, 2);
        }
    }
})

const choosenDate =ref();
const props = defineProps({

      monthlyStats: {
        type: Object,
        default: () => ({
        labels: [],
        series: [],
        total: 0,
        percentage: 0,
        average: 0
        })
    },
      platformSalesCount: {
    default: () => ({})
  },
    platformStatistics:{},
    downloadCounts:{},
    platforms:{},
    product:{},
    tab:{},
})


const choosenLabel = ref(null);
const isModalOn = ref(false);
const openPaymentModal = () => {

  isModalOn.value = !isModalOn.value;
}

// URL'den başlangıç değerlerini al
const params = new URLSearchParams(window.location.search);


// Reactive değişkenler
const currentTab = ref(params.get('slug') ?? 'platforms');
const choosenDates = ref(null);
const loading = ref(false);

// Sayfa yüklendiğinde çalışacak setup
onMounted(() => {
    // URL'den slug'ı al ve geçerli bir slug mu kontrol et
    const urlParams = new URLSearchParams(window.location.search);
    const urlSlug = urlParams.get('slug');

    if (urlSlug && tabs.value.some(tab => tab.slug === urlSlug)) {
        currentTab.value = urlSlug;
    } else {
        currentTab.value = 'platforms';
    }

    // Tarih parametrelerini kontrol et
    const startDate = urlParams.get('start_date');
    const endDate = urlParams.get('end_date');

    if (startDate && endDate) {
        choosenDates.value = [
            moment(startDate, 'M-YYYY'),
            moment(endDate, 'M-YYYY')
        ];
    }


});




// const choosenDate = ref();

// Tarih işlemleri için yardımcı fonksiyon
const formatMonthYear = (date) => {
    if (!date) return null;
    return moment(date).format('M-YYYY');
};

const onDateChoosen = async (e) => {
    console.log("EEEE",e);

    if (!e || !e['0'] || !e['1']) {
        await router.visit(route(route().current(),props.product.id), {
            data: {
                slug: currentTab.value,
            },
            preserveScroll: true,
            only: ['data']
        });
        return;
    }

    loading.value = true;
    try {
        // Tarihleri oluştur
        const dates = [
            moment().set({ month: e['0'].month, year: e['0'].year }),
            moment().set({ month: e['1'].month, year: e['1'].year })
        ];

        choosenDates.value = dates;

        await router.visit(route(route().current(),props.product.id), {
            data: {
                start_date: formatMonthYear(dates[0]),
                end_date: formatMonthYear(dates[1]),
                slug: currentTab.value,
            },
            preserveScroll: true,
            only: ['data']
        });
    } catch (error) {
        console.error('Tarih güncelleme hatası:', error);
    } finally {
        loading.value = false;
    }
};

const tabs = ref([
  {
    title: "En İyi Platformlar",
    slug: "platforms",
    component: PlatformsTab,
  },
  {
    title: "En İyi Ülkeler",
    slug: "countries",
    component: CountriesTab,
  },

])

const editRow = (label) => {

  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}


const formattedDates = computed(() => {
    if (!choosenDates.value) {
        const startDate = moment().format('MMMM YYYY');
        const endDate = moment().subtract(1, 'year').format('MMMM YYYY');
        return `${startDate} - ${endDate}`;
    } else if (choosenDates.value.length === 2) {
        const startDate = moment(choosenDates.value[0]).format('MMMM YYYY');
        const endDate = moment(choosenDates.value[1]).format('MMMM YYYY');
        return `${startDate} - ${endDate}`;
    }
    return '';
});


const onTabChange = async (tab) => {
    loading.value = true;
    console.log("TAB",tab);

    try {


        router.visit(route(route().current(),props.product.id), {
            // preserveState: true,
            preserveScroll: true,
            data:{
                slug: tab.slug
            },
            // only: ['data']

        });
    } catch (error) {
        console.error('Tab değişimi hatası:', error);
    } finally {
        loading.value = false;
    }
};

const onUpdate = (e) => {
  pageTable.value.editRow(e);
}

// Debug için computed property


onMounted(() => {
    if(choosenDates.value?.length > 0){
         choosenDate.value = choosenDates.value.map((date) => {
            const momentDate = moment(date);
            return {
                month: momentDate.month(),
                year: momentDate.year(),
            };
        });
    }

});

</script>

<style lang="scss" scoped>
.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
