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
import {useCrudStore} from "@/Stores/useCrudStore";

const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const note = ref('');
const props = defineProps({
  user: {},
});
const onBlockClicked = async () => {
    var response = await crudStore.post(route('control.user-management.users.toggle-status',props.user.id),{
        "reason" :note.value
    });
    console.log("RESPONSE",response);

};
</script>
<template>

    <div class="flex flex gap-6 items-center mb-5">
       <FormElement :label="'Kullanıcıyı Blokla'" v-model="note" placeholder="Yorum Ekle" label-width="290px" class="w-[560px]">
            <template #tooltip>
                deneme
            </template>
        </FormElement>
        <button @click="onBlockClicked" class="bg-error-500 px-3 py-2 rounded-lg text-white  label-sm">
            Kullanıcıyı Blokla
        </button>

    </div>

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
