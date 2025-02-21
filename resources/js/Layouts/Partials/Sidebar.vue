<template>


  <div
      class="bg-white-500 flex flex-col w-full md:h-screen md:w-[272px]  sideBar overflow-y-hidden border border-white-600">
    <div class=" p-3">
      <div class="p-3 flex  gap-3 items-center ">
        <img class="cursor-pointer w-10 h-10 " alt="" src="@/assets/images/logo.png">
        <div class="flex flex-col gap-1">
          <p class="label-sm c-strong-950">{{ __('control.sidebar.music_distribution') }}</p>
          <p class="paragraph-xs c-sub-600">{{ __('control.sidebar.music_distribution_management') }}</p>
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
        {{ __('control.general.create') }}
      </SecondaryButton>


      <div class="flex-1 overflow-scroll hideScroll">
        <MenuItem title="Dashboard" wrapper="dashboard" :path="route('control.dashboard')" :icon="DashboardIcon">

        </MenuItem>
        <MenuItem title="Katalog" wrapper="catalog" :icon="CatalogIcon">
          <template #sub>
            <sub-menu-item :path="'control.catalog.products.index'">{{ __('control.catalog.products') }}</sub-menu-item>
            <sub-menu-item :path="'control.catalog.songs.index'">{{ __('control.catalog.songs') }}</sub-menu-item>
            <sub-menu-item :path="'control.catalog.artists.index'">{{ __('control.catalog.artists') }}</sub-menu-item>
            <sub-menu-item :path="'control.catalog.labels.index'">{{ __('control.catalog.labels') }}</sub-menu-item>
            <sub-menu-item :path="'#'">{{ __('control.catalog.styles') }}</sub-menu-item>
          </template>
        </MenuItem>

        <MenuItem :path="route('control.statistics.index')" wrapper="statistics" title="İstatistikler" :icon="StatisticsIcon"/>
        <MenuItem title="Finans" wrapper="finance" :icon="WalletIcon">
          <template #sub>
            <sub-menu-item :path="'control.finance.payments.index'">{{ __('control.finance.payments.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.reports.index'">{{ __('control.finance.reports.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.analysis.index'">{{ __('control.finance.analysis.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.reports.report-files'">{{ __('control.finance.imports.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.imports.list'">{{ __('control.finance.participants.header') }}</sub-menu-item>
          </template>
        </MenuItem>
         <MenuItem title="Publishing" :icon="IcraIcon"/>
         <MenuItem title="Telif Yönetimi"  :icon="TeklifManagementIcon"/>


        <MenuItem title="Bildirimler" :icon="NotificationIcon"/>
        <div class="mt-4 p-2">
          <p class="subheading-xs c-soft-400"> {{ __('control.other') }}</p>
        </div>
        <MenuItem title="Kullanıcı Yönetimi" :icon="PersonSettingsIcon"
                  :path="route('control.user-management.users.index')"/>
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
  WalletIcon,
  DashboardIcon,
  VerifiedFilledIcon,
  TeklifManagementIcon,
  StatisticsIcon,
  IcraIcon,
  EarningManagementIcon,
  NotificationIcon,
  CatalogIcon,
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
