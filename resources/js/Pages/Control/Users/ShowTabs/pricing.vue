<script setup>
import {ref,reactive} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton,PrimaryButton,RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';

import {AppProgressIndicator} from '@/Components/Widgets';

import {
  StackIcon,
More2LineIcon,
  CreditCardIcon,
  RingtoneIcon,
  MusicVideoIcon,
  PlayCircleFillIcon,
  ChartsIcon,
  EyeOnIcon,
  EditIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  user: {},
});
const usages = reactive([
    {"title" : "deneme"},
    {"title" : "deneme"},
    {"title" : "deneme"},
    {"title" : "deneme"},
]);
</script>
<template>
    <div class="flex flex-col gap-6 items-start">
        <h1 class="subheading-regular text-start" v-text="'Fiyatlandırma'"/>
        <p class="paragraph-sm c-sub-600">Et semper orci donec varius sed faucibus hendrerit. Vel nunc mauris gravida nullam nulla ut nisl nibh. </p>
        <FormElement :label="'Hakediş Oranı'" placeholder="Lütfen Giriniz" label-width="290px" class="w-[560px]">
            <template #tooltip>
                deneme
            </template>
        </FormElement>
        <FormElement :label="'Satış Ödemesi Eşiği'" placeholder="Lütfen Giriniz" label-width="290px" class="w-[560px]">
            <template #tooltip>
                deneme
            </template>
        </FormElement>
    </div>
    <hr class="my-6">
    <h1 class="mb-6 subheading-regular text-start mb-3" v-text="'Mevcut Paket'"/>
    <p class="paragraph-sm c-sub-600">Et semper orci donec varius sed faucibus hendrerit. Vel nunc mauris gravida nullam nulla ut nisl nibh. </p>
    <div class="flex flex-1 gap-3 w-full mt-12 ">
        <div class="border border-soft-200 rounded-lg px-4 py-6 w-96">
            <div class="flex items-center justify-between mb-4">
                <StackIcon color="var(--dark-green-500)" />
                <IconButton class="bg-indigo-100 rounded-lg">
                    <More2LineIcon color="var(--sub-600)" />
                </IconButton>
            </div>
           <div>
                <p class="label-xl c-strong-950">Gelişmiş Paket</p>
                <p class="label-sm c-sub-600">Enterprise Plan</p>
           </div>
           <div class="my-12">
             <p class="paragraph-sm c-soft-400">Mevcut paket fiyatı</p>
               <div class="flex items-end label-lg">
                 <span class="c-strong-950">$30.</span><span class="c-soft-400">99/</span> <span class="c-soft-400">aylık</span>
               </div>
           </div>
           <hr class=" mb-8">
            <div class="flex gap-2">
                <img src="@/assets/images/Mastercard.png" class="image-fluid h-min">
                <div class="flex flex-col">
                    <div class="flex items-center gap-3">
                        <p class="subheading-2xs c-sub-600">KAYITLI ÖDEME YÖNTEMİ</p>
                        <span class="subheading-2xs text-[#122368] bg-[#C0D5FF] rounded-full px-2 py-1">KREDİ KARTI</span>
                    </div>
                    <div>
                        <p class="label-sm c-strong-950">5269 **** **** 1234 - Garanti BBVA</p>
                    </div>
                </div>
            </div>
            <div class="my-12">
                <div class="flex items-center justify-between">
                    <p class="label-sm c-strong-950">Yıllık paket</p>
                    <p class="paragraph-xs c-sub-600">Kalan 40 gün</p>
                </div>
               <AppProgressIndicator :height="6" :modelValue="10" color="#FF8447" class="my-2"/>

                 <p class="paragraph-xs c-sub-600">30.12.2024 - Üyelik Bitiş</p>
            </div>

            <div class="flex gap-2 items-center">
                <PrimaryButton class="flex-1">
                    <template #icon>
                        <CreditCardIcon color="var(--dark-green-500)" />
                    </template>
                    Ödeme Yap
                </PrimaryButton>

                <RegularButton class="flex-1">
                    Paketi Yükselt
                </RegularButton>
            </div>
        </div>
         <div class="border border-soft-200 rounded-lg px-4 py-6 flex-1">
            <p class="subheading-regular c-strong-950">Kalan Kullanımlar</p>
            <p class="label-sm c-strong-950 mb-5">Enterprise Plan</p>

            <div v-for="usage in usages">
                <div class="flex items-center gap-2">
                    <p class="flex-1 label-sm c-strong-950">{{usage.title}}</p>
                    <div class="flex items-center gap-1  w-32">
                        <AppProgressIndicator :height="3" class="w-12" :modelValue="50" color="#FF8447"  />
                        <p class="paragraph-xs c-sub-600  whitespace-nowrap">Kalan: 5/10</p>
                    </div>
                </div>
                <hr class="mb-3 mt-2">
            </div>
        </div>
    </div>
    <hr class="my-6">
    <h1 class="mb-6 subheading-regular text-start" v-text="'Tarihçe'"/>

    <AppTable  v-model="user.histories"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="Tarih">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn label="Değişiklik">
            <template #default="scope">
                <p class="paragraph-xs c-sub-600">{{scope.row.pre_order_date}}</p>
            </template>
        </AppTableColumn>


        <AppTableColumn label="Aksiyon Sahibi">
            <template #default="scope">
                 <p class="paragraph-xs c-sub-600">{{scope.row.publish_date}}</p>
            </template>
        </AppTableColumn>



        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
