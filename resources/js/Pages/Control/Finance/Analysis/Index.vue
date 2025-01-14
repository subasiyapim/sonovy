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
      <AppTabs :slug="currentTab" :tabs="tabs" class="my-5" @change="onTabChange"></AppTabs>
    </div>

    <div>
      <component :choosenDates="choosenDates" :data="data.data" :formattedDate="formattedDates"
                 :is="tabs.find(e => e.slug == currentTab)?.component"></component>
    </div>

    <NewReportModal @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed,nextTick} from 'vue';
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
  data: {},
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})


const choosenLabel = ref(null);
const isModalOn = ref(false);
const openPaymentModal = () => {

  isModalOn.value = !isModalOn.value;
}
let params = new URLSearchParams(window.location.search)

const choosenDates = ref(null);
if (params.get('start_date') && params.get('end_date')) {
     choosenDates.value = [moment(params.get('start_date'), "M-YYYY"),moment(params.get('end_date'), "M-YYYY")]

}
const removeDateFilter = () => {
  choosenDates.value = null;
  router.visit(route(route().current()), {

    preserveScroll: true,
  });
}
const choosenDate = ref();
const onDateChoosen = (e) => {


  router.visit(route(route().current()), {
    data: {
      end_date: `${e['1'].month+1}-${e['1'].year}`,
      start_date: `${e['0'].month+1}-${e['0'].year}`,
      slug: currentTab.value,
    },
    preserveScroll: true,
  });

}
const currentTab = ref(params.get('slug') ?? 'general')
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
    title: "Country",
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
            // return moment().locale('tr').format('MMMM YYYY');
             const startDate = moment().format('MMMM YYYY');
            const endDate = moment().subtract(1, 'year').format('MMMM YYYY');
            return `${startDate} - ${endDate}`;
    } else if (choosenDates.value.length === 2) {


            const startDate = moment(choosenDates.value[0]).format('MMMM YYYY');
            const endDate = moment(choosenDates.value[1]).format('MMMM YYYY');

            console.log("SDASD",choosenDates.value[0]);
            console.log("SDASD",choosenDates.value[1]);

            return `${startDate} - ${endDate}`;
    }
    return '';
});


const onTabChange = (tab) => {
  let query = {
    slug: tab.slug,
  }
  if (choosenDates.value) {
    query['start_date'] = choosenDates.value[0];
    query['end_date'] = choosenDates.value[1];
  }
  router.visit(route(route().current()), {
    data: query
  });
}

const onUpdate = (e) => {
  pageTable.value.editRow(e);
}
</script>

<style lang="scss" scoped>

</style>
