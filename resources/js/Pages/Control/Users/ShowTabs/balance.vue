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

    <div class="border border-soft-200 w-full p-3 rounded-lg my-5">
        <div class="flex items-center justify-between w-1/2 mb-2">
            <p class="c-strong-950 label-medium w-36 !text-start">Current Balance</p>
            <p class="c-strong-950 label-medium w-6 text-center">+</p>
            <p class="c-strong-950 label-medium w-24">{{usePage().props.user.tab.current_balance}}</p>
        </div>
         <div class="flex items-center justify-between w-1/2 mb-2">
            <p class="c-strong-950 label-medium w-36 !text-start">Pending Outpayments</p>
            <p class="c-strong-950 label-medium w-6 text-center">-</p>
            <p class="c-strong-950 label-medium w-24">{{usePage().props.user.tab.pending_out_payments}}</p>
        </div>
         <div class="flex items-center justify-between w-1/2 mb-2">
            <p class="c-strong-950 label-medium w-36 !text-start">Pending Invoices</p>
            <p class="c-strong-950 label-medium w-6 text-center">-</p>
            <p class="c-strong-950 label-medium w-24">{{usePage().props.user.tab.pending_invoices}}</p>
        </div>
        <hr>
         <div class="flex items-center justify-between w-1/2 mt-2">
            <p class="c-strong-950 label-medium w-36 !text-start">Confirmed Total</p>
            <p class="c-strong-950 label-medium w-6">=</p>
            <p class="c-strong-950 label-medium w-24">{{usePage().props.user.tab.confirmed_total}}</p>
        </div>
    </div>

    <AppTable  v-model="user.histories"  :isClient="true" :hasSearch="false" :showAddButton="false">
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

        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
