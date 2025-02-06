<template>
  <AdminLayout :showGoBack="false" :showBreadCrumb="false" :showDatePicker="false" :title="__('control.finance.reports.header')" parentTitle="Katalog">

    <div class="flex items-start my-6">
      <div class="flex-1">
        <h1 class="subheading-regular c-strong-950 mb-0.5">{{ __('control.finance.reports.current_reports') }}</h1>
        <p class="label-sm c-neutral-500">{{ __('control.finance.reports.current_reports_description') }}</p>
      </div>
      <PrimaryButton @click="openPaymentModal" class="w-60">
        <template #icon>
          <DocumentIcon color="var(--dark-green-500)"/>
        </template>
        <p>{{ __('control.finance.reports.create_new_report') }}</p>

      </PrimaryButton>
    </div>

    <div >
      <AppTabs :slug="currentTab" :tabs="tabs" class="my-5" @change="onTabChange"/>
    </div>

    <div class=" pb-10">
      <component :is="tabs.find(e => e.slug == currentTab)?.component"/>
    </div>

    <NewReportModal @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>


import {ref, onMounted, onUnmounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

import {AppTabs} from '@/Components/Widgets'
import {PrimaryButton} from '@/Components/Buttons'
import {
  DocumentIcon,
} from '@/Components/Icons'
import {router, usePage} from '@inertiajs/vue3';

import {NewReportModal} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";
import AutomaticReports from './Tabs/AutomaticReports.vue';
import DemandedReports from './Tabs/DemandedReports.vue';

const defaultStore = useDefaultStore();
const pageTable = ref();

const props = defineProps({
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})


const isModalOn = ref(false);
const openPaymentModal = () => {
  isModalOn.value = !isModalOn.value;
}
let params = new URLSearchParams(window.location.search)

const currentTab = ref(params.get('slug') ?? 'auto-reports')
const tabs = ref([
  {
    title: "Otomatik Raporlar",
    slug: "auto-reports",
    component: AutomaticReports,
  },
  {
    title: "Talep Edilen Raporlar",
    slug: "demanded-reports",
    component: DemandedReports,
  }
])

const editRow = (label) => {

  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}
const onDone = (e) => {
  pageTable.value.addRow(e);
}

const onTabChange = (tab) => {
  console.log("TAB", tab);

  router.visit(route(route().current()), {
    data: {
      slug: tab.slug,
      page: params.get('page') ?? 1,
    }
  });
}

const onUpdate = (e) => {
  pageTable.value.editRow(e);
}
</script>

<style lang="scss" scoped>

</style>
