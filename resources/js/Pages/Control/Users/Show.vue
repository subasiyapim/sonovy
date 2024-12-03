<template>

  <AdminLayout :showDatePicker="false" :title="__('control.user.show_header')" parentTitle="Katalog"
               subParent="Tüm Şarkılar" :hasPadding="false">
    <template #breadcrumb>
      <span class="label-xs c-soft-400">Katalog</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400 cursor-pointer" @click="router.visit(route('control.user-management.users.index'))">Tüm Kullanıcılar</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400">{{ user.type_text }}</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400">{{ user.album_name }}</span>
    </template>
      <div class="bg-white-400 h-44 p-5 relative">
      <div class="">
        <h1 class="label-xl c-strong-950" v-text="user.name"/>
        <span class="c-sub-600 paragraph-medium" v-text="user.id"/>
      </div>

      <div
          class="absolute rounded-full w-32 h-32 bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="user.name"
             :src="user.image ? user.image.thumb : defaultStore.profileImage(user.name)">
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <PrimaryButton @click="remove">
          <template #icon>
            <TrashIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
        <PrimaryButton @click="isModalOn = true">
          <template #icon>
            <EditIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
      </div>
    </div>
    <div class="mt-32 "></div>
    <div v-if="usePage().props.user.status == 4" class="my-3 px-8">
      <div class="bg-red-50 rounded px-3 py-2 my-2 flex items-center gap-2">
        <WarningIcon color="var(--error-500)"/>
        <p class="paragraph-xs c-strong-950"> {{ usePage().props.user.note }}</p>

      </div>
    </div>
    <!---APP TABS --->
    <div>
      <AppTabs :slug="currentTab" :tabs="tabs" class="my-5" @change="onTabChange"></AppTabs>
    </div>
    <div class="px-8 pb-10">
      <component :user="user" :is="tabs.find(e => e.slug == currentTab)?.component"></component>
    </div>

  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {ArtistDialog} from '@/Components/Dialog';
import {FormElement} from '@/Components/Form';
import {
  DocumentIcon,
  EditIcon,
  Icon,
  LinkIcon,
  PersonCardIcon,
  PersonIcon,
  PhoneIcon,
  PlayFilledIcon,
  SpotifyIcon,
  TrashIcon,
  WorldIcon,
  EditLineIcon,
  AddIcon,
  LabelsIcon,
  CalendarIcon,
  WarningIcon,
  RetractedIcon,
  ArtistsIcon,
  AudioIcon,
  MusicVideoIcon,
  RingtoneIcon,
  CheckFilledIcon
} from '@/Components/Icons'

const showAllArtists = ref(false);
import {PrimaryButton, RegularButton} from '@/Components/Buttons'
import {AppSelectInput} from '@/Components/Form'
import {AppTabs} from '@/Components/Widgets'
import {BasicBadge} from '@/Components/Badges'


import {useDefaultStore} from "@/Stores/default";
import {TidalIcon, YoutubeIcon} from "@/Components/Icons/index.js";
import {computed, ref} from "vue";
import {useForm, usePage, router} from '@inertiajs/vue3';
import ProfileTab from './ShowTabs/profile.vue';
import PricingTab from './ShowTabs/pricing.vue';
import ContractsTab from './ShowTabs/contracts.vue';
import BalanceTab from './ShowTabs/balance.vue';
import InvoicesTab from './ShowTabs/invoices.vue';
import ActivitiesTab from './ShowTabs/activities.vue';
import FlagsTab from './ShowTabs/flags.vue';
import RelationsTab from './ShowTabs/relations.vue';
import AuthorisationsTab from './ShowTabs/authorisations.vue';

import {useCrudStore} from '@/Stores/useCrudStore';

const changingStatus = ref(false);
const isModalOn = ref(false);
import {toast} from 'vue3-toastify';

const hasError = ref(null)
const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  statuses: {
    type: Array,
    required: true
  }
});


const crudStore = useCrudStore();
const defaultStore = useDefaultStore();
let params = new URLSearchParams(window.location.search)


const currentTab = ref(params.get('slug') ?? 'profile')

const onArtistProcessDone = () => {
  location.reload();
}
const remove = () => {

}

const productStatus = ref(props.user.status);
const productStatusConfig = computed(() => {
  return {
    data: props.statuses,
  };
})
const tabs = ref([
  {
    title: "Profil Bilgileri",
    slug: "profile",
    component: ProfileTab,
  },
  {
    title: "Fiyatlandırma",
    slug: "pricing",
    component: PricingTab,
  },
  {
    title: "Sözleşmeler",
    slug: "contracts",
    component: ContractsTab,
  },
  {
    title: "Bakiye",
    slug: "balances",
    component: BalanceTab,
  },
  {
    title: "Faturalar",
    slug: "invoices",
    component: InvoicesTab,
  },
  {
    title: "Aktiviteler",
    slug: "activities",
    component: ActivitiesTab,
  },
{

    title: "Bayraklar",
    slug: "flags",
    component: FlagsTab,
  },
  {
    title: "Bağlı Olduğu Alanlar",
    slug: "relatons",
    component: RelationsTab,
  },
  {
    title: "Yetkiler",
    slug: "authorisations",
    component: AuthorisationsTab,
  },
])

const onTabChange = (tab) => {

  router.visit(route(route().current(), props.user.id ?? 4), {
    data: {slug: tab.slug}
  });
}

const changeStatus = async () => {
  if (changingStatus.value) {
    return;
  }
  changingStatus.value = true;
  if (productStatus.value == 4) {
    if (props.user.note == null || props.user.note == '') {


      toast.error("Ret sebebi için not girmeniz gerekmektedir");
      hasError.value = 'Not Giriniz'
      changingStatus.value = false;
      return;
    }

  }
  const response = await crudStore.post(route('control.catalog.products.changeStatus', props.user.id), {
    status: productStatus.value,
    note: props.user.note,
  });
  if (response.success) {
    props.user.status = productStatus.value;

    changingStatus.value = false;
    toast.success("Durum Başarıyla Değiştirildi");

  }


}

const statusData = ref([
  {
    user: "Taslak",
    value: 1,
    icon: EditLineIcon,
    color: "#FF8447",

  },
  {
    user: "İnceleniyor",
    value: 2,
    icon: EditLineIcon,
    color: "#FF8447",
  },
  {
    user: "Yayınlandı",
    value: 3,
    icon: CheckFilledIcon,
    color: "#335CFF",
  },
  {
    user: "Reddedildi",
    value: 4,
    icon: WarningIcon,
    color: "#FB3748",
  },
  {
    user: "Geri Çekildi",
    value: 5,
    icon: RetractedIcon,
    color: "#717784",
  },
  {
    user: "Planlandı",
    value: 6,
    icon: CheckFilledIcon,
    color: "#FF8447",
  }
]);
</script>

<style lang="scss" scoped>

</style>
