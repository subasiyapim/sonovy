<script setup>
import {ref,reactive} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton,PrimaryButton,RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';

import {

  EyeOnIcon,
  TrashIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  user: {},
});

</script>
<template>

    <div class="flex flex gap-6 items-center mb-5">
       <FormElement :label="'Kullanıcıyı Blokla'" placeholder="Yorum Ekle" label-width="290px" class="w-[560px]">
            <template #tooltip>
                deneme
            </template>
        </FormElement>
        <button class="bg-error-500 px-3 py-2 rounded-lg text-white  label-sm">
            Kullanıcıyı Blokla
        </button>

    </div>
    <!-- {{usePage().props.user}} -->
    <AppTable  v-model="user.histories"  :isClient="true" :hasSearch="false" :showAddButton="false" >
        <AppTableColumn label="Fatura">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn label="Tarih">
            <template #default="scope">

            </template>
        </AppTableColumn>


        <AppTableColumn label="İşlem Türü">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn label="Miktar">
            <template #default="scope">

            </template>
        </AppTableColumn>
         <AppTableColumn label="Durumu">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyon">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
