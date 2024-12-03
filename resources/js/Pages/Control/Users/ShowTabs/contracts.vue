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

    <h1 class="mb-6 subheading-regular text-start" v-text="'Tanımlı Sözleşmeler'"/>

    <AppTable  v-model="user.histories"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="Sözleşme Adı">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn label="İmzalanma Durumu">
            <template #default="scope">

            </template>
        </AppTableColumn>


        <AppTableColumn label="İmzalanma Tarihi">
            <template #default="scope">
                 <p class="paragraph-xs c-sub-600">{{scope.row.publish_date}}</p>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Aksiyonlar">
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
