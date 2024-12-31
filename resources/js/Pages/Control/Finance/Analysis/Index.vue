<template>
  <AdminLayout  :title="__('control.finance.analysis.header')" parentTitle="Katalog">

    <div class="flex grid grid-cols-2 gap-3 mb-5">
        <AppCard class="flex-1 w-full">
            <template #header>
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center"><WalletLineIcon color="var(--sub-600)" /></div>
            </template>
            <template #tool>

            </template>
            <template #body>
                <div class="flex flex-col mt-5">
                    <p class="paragraph-sm c-sub-600 mb-0.5">Tüm zamanların Geliri</p>
                    <p class="card-currency-header c-strong-950">$0.00</p>
                </div>
            </template>
        </AppCard>
        <AppCard class="flex-1 w-full">
                <template #header>
                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center"><ExitIcon color="var(--sub-600)" /></div>

                </template>
                <template #tool>

                </template>
              <template #body>
                <div class="flex flex-col mt-5">
                    <p class="paragraph-sm c-sub-600 mb-0.5">Ocak 2024 - Haziran 2024 Geliri</p>
                    <p class="card-currency-header c-strong-950">$0.00</p>
                </div>
            </template>
        </AppCard>

    </div>


    <div>
      <AppTabs :slug="currentTab" :tabs="tabs" class="my-5" @change="onTabChange"></AppTabs>
    </div>

    <div>
      <component  :is="tabs.find(e => e.slug == currentTab)?.component"></component>
    </div>

    <NewReportModal  @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {AppCard} from '@/Components/Cards';

import {AppTabs} from '@/Components/Widgets'
import {PrimaryButton, IconButton,RegularButton} from '@/Components/Buttons'
import {StatusBadge} from '@/Components/Badges'
import {AddIcon, LabelsIcon,DocumentIcon,DownloadIcon, BankLineIcon, TrashIcon, EditIcon, ExitIcon, WalletLineIcon,SpeedUpIcon,EditLineIcon} from '@/Components/Icons'
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
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})


const data = ref([])
const choosenLabel = ref(null);
const isModalOn = ref(false);
const openPaymentModal = () => {

    isModalOn.value = !isModalOn.value;
}
let params = new URLSearchParams(window.location.search)

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

const onTabChange = (tab) => {
    console.log("TAB",tab);

  router.visit(route(route().current()), {
    data: {slug: tab.slug}
  });
}

const onUpdate = (e) => {
  pageTable.value.editRow(e);
}
</script>

<style lang="scss" scoped>

</style>
