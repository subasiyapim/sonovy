<template>


  <div
     :class="{
        'w-[272px] md:w-[272px] transition-all duration-300': isSidebarOpen,
        'w-[72px] md:w-[72px] transition-all duration-300': !isSidebarOpen
      }"
      class="relative bg-white-500 flex flex-col  md:h-screen   sideBar  border border-white-600">
    <div class=" p-3">
      <div :class="isSidebarOpen ? 'p-3' :'px-0 py-2'" class="flex  gap-3 items-center ">
        <img class="cursor-pointer w-10 h-10 " alt="" src="@/assets/images/logo.png">
        <div v-if="isSidebarOpen" class="flex flex-col gap-1">
          <p class="label-sm c-strong-950">{{ __('control.sidebar.music_distribution') }}</p>
          <p class="paragraph-xs c-sub-600">{{ __('control.sidebar.music_distribution_management') }}</p>
        </div>
      </div>
    </div>
    <hr class="mb-3 soft-hr mx-5">
    <div :class="isSidebarOpen ? 'px-5 pt-2 pb-4' : 'px-3 py-2'" class="h-full flex flex-col gap-5 overflow-hidden">
      <AppTextInput v-if="isSidebarOpen" class="w-full" placeholder="Yayın, Artist ara...">
        <template #icon>
          <SearchIcon color="var(--soft-400)"/>
        </template>
      </AppTextInput>
      <SecondaryButton @click="isProductDialogOn = true" class="w-full">
        <template #icon>
          <AddIcon/>
        </template>

       <template v-if="isSidebarOpen" #default> {{ __('control.general.create') }}</template>
      </SecondaryButton>


      <div class="flex-1 overflow-scroll hideScroll">
        <MenuItem title="Dashboard" wrapper="dashboard" :path="route('control.dashboard')" :icon="DashboardIcon" :isSidebarOpen="isSidebarOpen">

        </MenuItem>
        <MenuItem title="Katalog" wrapper="catalog" :icon="CatalogIcon" :isSidebarOpen="isSidebarOpen">
          <template #sub>
            <sub-menu-item :path="'control.catalog.products.index'">{{ __('control.catalog.products') }}</sub-menu-item>
            <sub-menu-item :path="'control.catalog.songs.index'">{{ __('control.catalog.songs') }}</sub-menu-item>
            <sub-menu-item :path="'control.catalog.artists.index'">{{ __('control.catalog.artists') }}</sub-menu-item>
            <sub-menu-item :path="'control.catalog.labels.index'">{{ __('control.catalog.labels') }}</sub-menu-item>
            <sub-menu-item :path="'#'">{{ __('control.catalog.styles') }}</sub-menu-item>
          </template>
        </MenuItem>

        <MenuItem :path="route('control.statistics.index')" wrapper="statistics" title="İstatistikler" :icon="StatisticsIcon" :isSidebarOpen="isSidebarOpen"/>
        <MenuItem title="Finans" wrapper="finance" :icon="WalletIcon" :isSidebarOpen="isSidebarOpen">
          <template #sub>
            <sub-menu-item :path="'control.finance.payments.index'">{{ __('control.finance.payments.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.reports.index'">{{ __('control.finance.reports.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.analysis.index'">{{ __('control.finance.analysis.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.reports.report-files'">{{ __('control.finance.imports.header') }}</sub-menu-item>
            <sub-menu-item :path="'control.finance.reports.participant-reports'">{{ __('control.finance.participants.header') }}</sub-menu-item>
          </template>
        </MenuItem>
         <MenuItem title="Publishing" :icon="IcraIcon" :isSidebarOpen="isSidebarOpen"/>
         <MenuItem title="Telif Yönetimi"  :icon="TeklifManagementIcon" :isSidebarOpen="isSidebarOpen"/>


        <MenuItem title="Bildirimler" :icon="NotificationIcon" :isSidebarOpen="isSidebarOpen"/>
        <div class="mt-4 p-2">
          <p class="subheading-xs c-soft-400"> {{ __('control.other') }}</p>
        </div>
        <MenuItem title="Kullanıcı Yönetimi" :icon="PersonSettingsIcon"
                  :path="route('control.user-management.users.index')" :isSidebarOpen="isSidebarOpen"/>
        <MenuItem title="CMS" :icon="CmsIcon" :isSidebarOpen="isSidebarOpen"/>

        <MenuItem title="Yönetici Ayarları" wrapper="management" :icon="SettingsIcon" :isSidebarOpen="isSidebarOpen">
          <template #sub>
            <sub-menu-item :path="'control.management.announcements.index'">Duyurular</sub-menu-item>
            <sub-menu-item >E-posta Yönetimi</sub-menu-item>
            <sub-menu-item >Kurulum</sub-menu-item>
            <sub-menu-item >Ödeme Yönetimi</sub-menu-item>
            <sub-menu-item :path="'control.management.planItems.index'">Plan Yönetimi</sub-menu-item>
            </template>
        </MenuItem>
        <MenuItem title="Destek Merkezi" :icon="SupportCenterIcon" :isSidebarOpen="isSidebarOpen"/>
      </div>

        <button @click="isSidebarOpen =  !isSidebarOpen" class="z-[1] absolute w-6 h-6 top-7 -right-3 flex items-center justify-center rounded-lg bg-dark-green-800">
            <div  class="  flex items-center justify-center">
                <ChevronRightIcon v-if="!isSidebarOpen" color="#fff" />
                <ChevronLeftIcon v-else color="#fff" />
            </div>
        </button>
        <div :class="isSidebarOpen ? 'p-3 gap-3' : 'p-0.5 py-2' " class="flex mx-auto h-min items-center  border-t border-soft-200 ">
            <div :class="isSidebarOpen ? 'w-10 h-10' : 'w-6 h-6' " class="rounded-full  bg-blue-300">
            <img :alt="usePage().props.auth.user.name"
                :src="usePage().props.auth.user.image
                            ? usePage().props.auth.user.image
                            : defaultStore.profileImage(usePage().props.auth.user.name)">
            </div>
            <div v-if="isSidebarOpen">
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
  SupportCenterIcon,
  ChevronRightIcon,
  ChevronLeftIcon
} from '@/Components/Icons'
import MenuItem from '@/Components/Menu/MenuItem.vue'
import SubMenuItem from '@/Components/Menu/SubMenuItem.vue'
import {usePage, router} from '@inertiajs/vue3';
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();
const isProductDialogOn = ref(false);

const isSidebarOpen = ref(false);
</script>


<style>


</style>
