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
import 'moment/dist/locale/tr';
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

// Sayfa yüklendiğinde çalışacak setup
onMounted(() => {
    // URL'den slug'ı al ve geçerli bir slug mu kontrol et
    const urlParams = new URLSearchParams(window.location.search);
    const urlSlug = urlParams.get('slug');
    
    if (urlSlug && tabs.value.some(tab => tab.slug === urlSlug)) {
        currentTab.value = urlSlug;
    } else {
        currentTab.value = 'general';
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

    console.log('Sayfa yüklendi:', {
        currentTab: currentTab.value,
        choosenDates: choosenDates.value
    });
});

// URL değişikliklerini izle
watch(() => window.location.search, (search) => {
    const params = new URLSearchParams(search);
    const slug = params.get('slug');
    
    if (slug && tabs.value.some(tab => tab.slug === slug)) {
        currentTab.value = slug;
    }
}, { deep: true });

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

// Tarih işlemleri için yardımcı fonksiyon
const formatMonthYear = (date) => {
    if (!date) return null;
    return moment(date).format('M-YYYY');
};

const onDateChoosen = async (e) => {
    if (!e || !e['0'] || !e['1']) {
        console.error('Geçersiz tarih seçimi:', e);
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

        await router.visit(route(route().current()), {
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
        const query = new URLSearchParams();
        query.set('slug', tab.slug);
        
        // Tarih parametrelerini ekle
        if (choosenDates.value && choosenDates.value.length === 2) {
            const startDate = moment(choosenDates.value[0]).format('M-YYYY');
            const endDate = moment(choosenDates.value[1]).format('M-YYYY');
            query.set('start_date', startDate);
            query.set('end_date', endDate);
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
                    currentTab: currentTab.value
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
