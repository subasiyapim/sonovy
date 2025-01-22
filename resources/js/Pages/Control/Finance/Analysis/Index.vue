<template>
  <AdminLayout :showDatePicker="false"  :title="__('control.finance.analysis.header')" parentTitle="Katalog">

    <template #toolbar>
    <div class="w-48">
        <VueDatePicker @update:model-value="onDateChoosen"  v-model="choosenDate"  range  month-picker :max-date="new Date()"  class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
            <template #input-icon>
                <div class="p-3">
                    <CalendarIcon color="var(--sub-600)"/>
                </div>
            </template>
        </VueDatePicker>
    </div>

      <div v-if="choosenDates"
           class="flex items-center jusitfy-center gap-2 border border-soft-200 rounded px-3 py-1 hover:bg-grey-300">
        <p class="paragraph-xs c-sub-600">
          {{ moment(choosenDates[0]).format('DD/MM/YYYY') + ' - ' + moment(choosenDates[1]).format('DD/MM/YYYY') }}</p>
        <button @click="removeDateFilter">
          <CloseIcon color="var(--sub-600)"/>
        </button>
      </div>
    </template>
    <div class="flex grid grid-cols-2 gap-3 mb-5">
      <AppCard class="flex-1 w-full">
        <template #header>
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <WalletLineIcon color="var(--sub-600)"/>
          </div>
        </template>
        <template #tool>

        </template>
        <template #body>
          <div class="flex flex-col mt-5">
            <p class="paragraph-sm c-sub-600 mb-0.5">Tüm zamanların Geliri</p>
            <p class="card-currency-header c-strong-950">{{ data?.metadata?.all_time_earning }}</p>
          </div>
        </template>
      </AppCard>
      <AppCard class="flex-1 w-full">
        <template #header>
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <ExitIcon color="var(--sub-600)"/>
          </div>

        </template>
        <template #tool>

        </template>
        <template #body>
          <div class="flex flex-col mt-5">
            <p class="paragraph-sm c-sub-600 mb-0.5">{{ data?.metadata?.current_month }} Geliri</p>
            <p class="card-currency-header c-strong-950">{{ data?.metadata?.current_month_earning }}</p>
          </div>
        </template>
      </AppCard>

    </div>


    <div>
      <AppTabs 
        :slug="currentTab"
        :tabs="tabs"
        class="my-5"
        @change="onTabChange"
        :key="currentTab"
      />
    </div>

    <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="loading-spinner"></div>
    </div>
    <div v-else>
        <component :is="currentComponent"
                  :choosenDates="choosenDates"
                  :data="data.data"
                  :formattedDate="formattedDates" />
    </div>

    <NewReportModal @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed, watch, nextTick, onMounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {AppCard} from '@/Components/Cards';
import moment from 'moment';
import  'moment/dist/locale/tr';

moment.locale('tr');


import {AppTabs} from '@/Components/Widgets'
import {PrimaryButton, IconButton, RegularButton} from '@/Components/Buttons'
import {StatusBadge} from '@/Components/Badges'
import {
  AddIcon,
  LabelsIcon,
  CloseIcon,
  DocumentIcon,
  DownloadIcon,
  BankLineIcon,
  TrashIcon,
  EditIcon,
  ExitIcon,
  WalletLineIcon,
  SpeedUpIcon,
  EditLineIcon,
  CalendarIcon
} from '@/Components/Icons'
import {router} from '@inertiajs/vue3';

import {NewReportModal} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";
import GeneralLookTab from './Tabs/GeneralLookTab.vue';
import TopListsTab from './Tabs/TopListsTab.vue';
import PlatformsTab from './Tabs/PlatformsTab.vue';
import CountriesTab from './Tabs/CountriesTab.vue';

const defaultStore = useDefaultStore();
const pageTable = ref();

const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({})
  },
  filters: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  ziggy: {
    type: Object,
    default: () => ({})
  },
  auth: {
    type: Object,
    default: () => ({})
  }
})


const choosenLabel = ref(null);
const isModalOn = ref(false);
const openPaymentModal = () => {

  isModalOn.value = !isModalOn.value;
}

// URL'den başlangıç değerlerini al
const params = new URLSearchParams(window.location.search);
const initialSlug = params.get('slug') ?? 'general';

// Reactive değişkenler
const currentTab = ref(initialSlug);
const choosenDates = ref(null);
const loading = ref(false);

// Sayfa yüklendiğinde ve URL değiştiğinde çalışacak watch
watch(() => router.currentRoute.value.query, (query) => {
    try {
        const slug = query.slug || 'general';
        if (tabs.value.some(tab => tab.slug === slug)) {
            currentTab.value = slug;
            console.log("Tab güncellendi:", slug);
        }
    } catch (error) {
        console.error("URL parsing hatası:", error);
        currentTab.value = 'general';
    }
}, {
    immediate: true,
    deep: true
});

// Tarih parametrelerini kontrol et ve ayarla
if (params.get('start_date') && params.get('end_date')) {
    choosenDates.value = [
        moment(params.get('start_date'), "M-YYYY"),
        moment(params.get('end_date'), "M-YYYY")
    ];
}

const removeDateFilter = async () => {
  loading.value = true;
  try {
    choosenDates.value = null;
    await router.visit(route(route().current()), {
      preserveScroll: true,
      only: ['data']
    });
  } finally {
    loading.value = false;
  }
}
const choosenDate = ref();
const onDateChoosen = async (e) => {
  loading.value = true;
  try {
    await router.visit(route(route().current()), {
      data: {
        end_date: `${e['1'].month+1}-${e['1'].year}`,
        start_date: `${e['0'].month+1}-${e['0'].year}`,
        slug: currentTab.value,
      },
      preserveScroll: true,
      only: ['data']
    });
  } finally {
    loading.value = false;
  }
}

const tabs = ref([
  {
    title: "Genel Bakış",
    slug: "general",
    component: GeneralLookTab,
  },
  {
    title: "Top Listeler",
    slug: "top-lists",
    component: TopListsTab,
  },
  {
    title: "Mağaza",
    slug: "platforms",
    component: PlatformsTab,
  },
  {
    title: "Ülkeler",
    slug: "countries",
    component: CountriesTab,
  }
])

const editRow = (label) => {

  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}
const onDone = (e) => {
  pageTable.value.addRow(e);
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

// Debug için onMounted hook'u
onMounted(() => {
    console.log('Index component mounted:', {
        data: props.data,
        filters: props.filters,
        currentTab: currentTab.value,
        choosenDates: choosenDates.value,
        formattedDates: formattedDates.value
    });
});

const onTabChange = async (tab) => {
    console.log('Tab değişimi başladı:', tab);
    loading.value = true;
    
    try {
        // Önce tab'ı güncelle
        currentTab.value = tab.slug;
        
        // URL parametrelerini hazırla
        const query = new URLSearchParams(window.location.search);
        query.set('slug', tab.slug);
        
        if (choosenDates.value) {
            query.set('start_date', moment(choosenDates.value[0]).format('M-YYYY'));
            query.set('end_date', moment(choosenDates.value[1]).format('M-YYYY'));
        }
        
        // URL'i güncelle ve veriyi yükle
        const url = `${route(route().current())}?${query.toString()}`;
        console.log('Ziyaret edilecek URL:', url);
        
        await router.visit(url, {
            preserveState: true,
            preserveScroll: true,
            only: ['data'],
            onSuccess: (page) => {
                console.log('Tab değişimi başarılı:', {
                    data: page.props.data,
                    currentTab: currentTab.value,
                    countries: page.props.data?.data?.countries,
                    releases: page.props.data?.data?.releases
                });
                // Tab değişiminin başarılı olduğundan emin olalım
                nextTick(() => {
                    console.log('Tab değişimi tamamlandı:', {
                        currentTab: currentTab.value,
                        component: currentComponent.value,
                        data: props.data
                    });
                });
            },
            onError: (errors) => {
                console.error('Tab değişimi hatası:', errors);
            }
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
const currentComponent = computed(() => {
    const component = tabs.value.find(tab => tab.slug === currentTab.value)?.component;
    console.log('Current component:', {
        tab: currentTab.value,
        component: component?.name
    });
    return component ?? GeneralLookTab;
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
