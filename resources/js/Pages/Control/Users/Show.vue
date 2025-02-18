<template>

  <AdminLayout :showDatePicker="false" :title="__('control.user.show_header')" parentTitle="Katalog"
               subParent="Tüm Şarkılar" :hasPadding="false">
    <template #breadcrumb>
      <span class="label-xs c-soft-400">{{ __('control.user.title_management') }}</span>
        <span class="label-xs c-soft-400">•</span>
        <Link :href="route('control.user-management.users.show',user.parent.id)" v-if="user.parent" class="label-xs c-soft-400">{{ user.parent?.name }}</Link>
        <span v-if="user.parent" class="label-xs c-soft-400">•</span>
        <span class="label-xs c-soft-400">{{ user.name }}</span>
    </template>
    <template #toolbar>
        <RegularButton v-if="!isInAdminViewMode" @click="switchUsers">
          <template #icon>
            <EyeOnIcon color="var(--sub-600)"/>
          </template>
             Kullanıcının Gözünden Gör
        </RegularButton>
    </template>
      <div class="bg-white-400 h-44 p-5 relative">
      <div class="flex flex-col items-start">
        <div class="flex items-center gap-2">
            <h1 class="label-xl c-strong-950" v-text="user.name"/>
             <template v-for="role in user.roles">
                    <div class="px-3 py-1 rounded-full" :class="role.code == 'super_admin' ? 'bg-[#CAC0FF]' : (role.code == 'admin' ? 'bg-[#D8E5ED]' : 'bg-[#C0D5FF]')">
                        <p class="label-xs" :class="role.code == 'super_admin' ? 'text-[#351A75]' : (role.code == 'admin' ? 'text-[#060E2F]' : 'text-[#122368]')">  {{role.name}}</p>
                    </div>
                </template>

             <div v-if="user.status == 1" class="border border-soft-200 px-2 py-1 rounded-full flex items-center gap-2">
                <CheckFilledIcon color="var(--sub-600)" />
                <p class="label-xs text-[#122368]">         {{ __('control.user.active') }}</p>
            </div>
             <div v-else class=" px-2 py-1 rounded-full flex items-center gap-2 bg-error-500">
                <WarningIcon style="width:12px;" color="#fff" />
                <p class="label-xs text-white">  {{ __('control.user.status_enum.status_passive') }}</p>
            </div>
        </div>
        <span class="c-neutral-400 paragraph-xs" v-text="'#'+user.id"/>
        <span class="c-sub-600 paragraph-medium" v-text="user.email"/>

      </div>

      <div
          class="absolute rounded-full w-32 h-32 bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="user.name"
             :src="user.image ? user.image.thumb : defaultStore.profileImage(user.name)">
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">

        <PrimaryButton @click="remove" class="bg-error-500">
          <template #icon>
            <TrashIcon color="#fff"/>
          </template>
        </PrimaryButton>
        <PrimaryButton @click="openUserModal">
          <template #icon>
            <EditIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
      </div>

        <div class=" absolute bottom-2 right-5">
        <span class="label-xs !font-semibold c-sub-600">  {{ __('control.user.last_login') }}: </span>
        <span class="label-xs !font-regular c-sub-600">02.09.2024 - 09:30</span>
        </div>
    </div>
    <div class="mt-32 "></div>

    <!---APP TABS --->
    <div class="px-6">
      <AppTabs :slug="currentTab" :tabs="tabs" class="my-5" @change="onTabChange"></AppTabs>
    </div>
    <div class="px-6 pb-10">

      <component :user="user" :is="tabs.find(e => e.slug == currentTab)?.component"></component>
    </div>

  </AdminLayout>
    <UserModal @update="onUpdated" v-model="isUserModalOn" v-if="isUserModalOn" :user="user"></UserModal>

</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {ArtistDialog} from '@/Components/Dialog';
import {FormElement} from '@/Components/Form';
import {UserModal} from '@/Components/Dialog';
import {useUiStore} from '@/Stores/useUiStore';
import { Link } from '@inertiajs/vue3'
import {
  EditIcon,
  Icon,
  LinkIcon,
  PersonCardIcon,
  PersonIcon,
  PhoneIcon,
  PlayFilledIcon,
  SpotifyIcon,
  TrashIcon,
  EyeOnIcon,
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
const uiStore = useUiStore();
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
});

const isUserModalOn = ref(false);
const crudStore = useCrudStore();
const defaultStore = useDefaultStore();
let params = new URLSearchParams(window.location.search)


const currentTab = ref(params.get('slug') ?? 'profile')

const onArtistProcessDone = () => {
  location.reload();
}
const remove = async () => {
    try {
        const response = await crudStore.del(route('control.user-management.users.destroy',props.user.id));
        toast.success(response.message);
        router.visit(route('control.user-management.users.index'));
    } catch (error) {
          toast.error(error.response?.data?.message ?? "işlem hatalı");

    }
}

const productStatus = ref(props.user.status);

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
        slug: "relations",
        component: RelationsTab,
    },
    {
        title: "Yetkiler",
        slug: "authorisations",
        component: AuthorisationsTab,
    },
    ])

const openUserModal = () => {
    isUserModalOn.value = true;
}

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

const onUpdated = (e) => {
    console.log("EEE",e);

}

const isInAdminViewMode = computed(() => {
    return uiStore.isAdminViewOn;
})

const switchUsers = () => {
    uiStore.isAdminViewOn = true;
    localStorage.setItem("account-to-switch-back",props.user.id );
    router.visit(route('control.user-management.users.switch-to-user'), { method: 'post',data:{
        user_id : props.user.id
    }});
    toast.success("Kullanıcı görünümüne geçildi");

};
</script>

<style lang="scss" scoped>

</style>
