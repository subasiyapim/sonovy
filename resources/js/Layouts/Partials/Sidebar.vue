<template>

  <div class="bg-white-500 flex flex-col w-full md:h-screen md:w-[272px]  sideBar overflow-y-hidden">
       <div class=" p-3">
           <div class="p-3 flex  gap-3 items-center ">
                 <img class="cursor-pointer w-10 h-10 " alt=""  src="@/assets/images/logo.png">
                <div class="flex flex-col gap-1">
                    <p class="label-sm c-strong-950">Müzik Dağıtım</p>
                    <p class="paragraph-xs c-sub-600">Müzik dağıtım yönetimi</p>
                </div>
           </div>
       </div>
       <hr class="mb-3 soft-hr mx-5">
       <div class="px-5 pt-2 pb-4 h-full flex flex-col gap-5 overflow-hidden">
        <AppTextInput class="w-full" placeholder="Yayın, Artist ara...">
              <template #icon><SearchIcon color="var(--soft-400)"/></template>
        </AppTextInput>
        <SecondaryButton @click="router.visit(route('control.products.create'))" class="w-full">
            <template #icon><AddIcon/></template>
            Yeni Yayın Oluştur
        </SecondaryButton>


       <div class="flex-1 overflow-scroll">
             <MenuItem title="Dashboard" :icon="DashboardIcon">

            </MenuItem >
             <MenuItem title="Katalog" :icon="DashboardIcon">
                <template #sub>
                    <a :href="route('control.products.index')" class="radius-8 sub-menu-item"> Yayınlar</a>
                    <a :href="route('control.songs.index')" class="radius-8 sub-menu-item"> Parçalar</a>
                    <a :href="route('control.artists.index')" class="radius-8 sub-menu-item"> Sanatçılar</a>
                    <a :href="route('control.labels.index')" class="radius-8 sub-menu-item"> Plak Şirketleri</a>
                    <a :href="'#'" class="radius-8 sub-menu-item"> Tarzlar</a>
                </template>
            </MenuItem >

            <MenuItem title="Teklif Yönetimi" :icon="TeklifManagementIcon" />
            <MenuItem title="İstatistikler" :icon="StatisticsIcon" />
            <MenuItem title="İcra" :icon="IcraIcon" />
            <MenuItem title="Kazan Yönetimi" :icon="EarningManagementIcon" />
            <MenuItem title="Bildirimler" :icon="NotificationIcon" />
             <div class="mt-4 p-2">
                <p class="subheading-xs c-soft-400">Diğer</p>
            </div>
            <MenuItem title="Kullanıcı Yönetimi" :icon="PersonSettingsIcon" />
            <MenuItem title="CMS" :icon="CmsIcon" />
            <MenuItem title="Yönetici Ayarları" :icon="SettingsIcon" />
            <MenuItem title="Destek Merkezi" :icon="SupportCenterIcon" />
       </div>





       <div class="flex h-min items-center gap-3 border-t border-soft-200 p-3">
            <div class="rounded-full w-10 h-10 bg-blue-300">

            </div>
            <div>
                   <div class="flex items-center gap-2">
                         <p class="label-sm c-strong-950">Sophia Williams</p>
                        <VerifiedFilledIcon />
                   </div>
                    <p class="paragraph-xs c-sub-600">sophia@alignui.com</p>
            </div>

       </div>

       </div>

  </div>
</template>

<script  setup>
import { AppTextInput } from '@/Components/Form';
import {SecondaryButton} from '@/Components/Buttons';
import {AddIcon,SearchIcon,DashboardIcon,VerifiedFilledIcon,TeklifManagementIcon,StatisticsIcon,IcraIcon,EarningManagementIcon,NotificationIcon,PersonSettingsIcon,CmsIcon,SettingsIcon,SupportCenterIcon} from '@/Components/Icons'
import MenuItem from '@/Components/Menu/MenuItem.vue'
import {usePage, router} from '@inertiajs/vue3';

</script>


<style>


</style>
