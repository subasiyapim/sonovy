<template>


  <div
      class="bg-white-500 flex flex-col w-full md:h-screen md:w-[272px]  sideBar overflow-y-hidden border border-white-600">
    <div class=" p-3">
      <div class="p-3 flex  gap-3 items-center ">
        <img class="cursor-pointer w-10 h-10 " alt="" src="@/assets/images/logo.png">
        <div class="flex flex-col gap-1">
          <p class="label-sm c-strong-950">Müzik Dağıtım</p>
          <p class="paragraph-xs c-sub-600">Müzik dağıtım yönetimi</p>
        </div>
      </div>
    </div>
    <hr class="mb-3 soft-hr mx-5">
    <div class="px-5 pt-2 pb-4 h-full flex flex-col gap-5 overflow-hidden">
      <AppTextInput class="w-full" placeholder="Yayın, Artist ara...">
        <template #icon>
          <SearchIcon color="var(--soft-400)"/>
        </template>
      </AppTextInput>
      <SecondaryButton @click="isProductDialogOn = true" class="w-full">
        <template #icon>
          <AddIcon/>
        </template>
        Yeni Yayın Oluştur
      </SecondaryButton>


      <div class="flex-1 overflow-scroll hideScroll">
        <MenuItem title="Dashboard" wrapper="dashboard" :path="route('control.dashboard')" :icon="DashboardIcon">

        </MenuItem>
        <MenuItem title="Katalog" wrapper="catalog" :icon="DashboardIcon">
          <template #sub>
            <sub-menu-item :path="'control.catalog.products.index'">Yayınlar</sub-menu-item>
            <sub-menu-item :path="'control.catalog.songs.index'">Parçalar</sub-menu-item>
            <sub-menu-item :path="'control.catalog.artists.index'">Sanatçılar</sub-menu-item>
            <sub-menu-item :path="'control.catalog.labels.index'">Plak Şirketleri</sub-menu-item>
            <sub-menu-item :path="'#'">Tarzlar</sub-menu-item>
          </template>
        </MenuItem>

        <MenuItem title="Teklif Yönetimi" :icon="TeklifManagementIcon"/>
        <MenuItem title="İstatistikler" :icon="StatisticsIcon"/>
        <MenuItem title="İcra" :icon="IcraIcon"/>
        <MenuItem title="Kazan Yönetimi" :icon="EarningManagementIcon"/>
        <MenuItem title="Bildirimler" :icon="NotificationIcon"/>
        <div class="mt-4 p-2">
          <p class="subheading-xs c-soft-400">Diğer</p>
        </div>
        <MenuItem title="Kullanıcı Yönetimi" :icon="PersonSettingsIcon"/>
        <MenuItem title="CMS" :icon="CmsIcon"/>
        <MenuItem title="Yönetici Ayarları" :icon="SettingsIcon"/>
        <MenuItem title="Destek Merkezi" :icon="SupportCenterIcon"/>
      </div>


      <div class="flex h-min items-center gap-3 border-t border-soft-200 p-3">
        <div class="rounded-full w-10 h-10 bg-blue-300">
          <img :alt="usePage().props.auth.user.name"
               :src="usePage().props.auth.user.image
                         ? usePage().props.auth.user.image
                         : defaultStore.profileImage(usePage().props.auth.user.name)">
        </div>
        <div>
          <div class="flex items-center gap-2">
            <p class="label-sm c-strong-950" v-text="usePage().props.auth.user.name"/>
            <VerifiedFilledIcon/>
          </div>
          <p class="paragraph-xs c-sub-600" v-text="usePage().props.auth.user.email"/>
        </div>

      </div>

    </div>


  </div>
  <ProductDialog v-model="isProductDialogOn" v-if="isProductDialogOn"></ProductDialog>
</template>

<script setup>
import {ref} from 'vue';
import {AppTextInput} from '@/Components/Form';
import {SecondaryButton} from '@/Components/Buttons';
import {ProductDialog} from '@/Components/Dialog';
import {
  AddIcon,
  SearchIcon,
  DashboardIcon,
  VerifiedFilledIcon,
  TeklifManagementIcon,
  StatisticsIcon,
  IcraIcon,
  EarningManagementIcon,
  NotificationIcon,
  PersonSettingsIcon,
  CmsIcon,
  SettingsIcon,
  SupportCenterIcon
} from '@/Components/Icons'
import MenuItem from '@/Components/Menu/MenuItem.vue'
import SubMenuItem from '@/Components/Menu/SubMenuItem.vue'
import {usePage, router} from '@inertiajs/vue3';
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();
const isProductDialogOn = ref(false);
</script>


<style>


</style>
