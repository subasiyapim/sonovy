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


    <AppTable  :showAddButton="false" v-model="user.tab"  :isClient="true" >
        <AppTableColumn label="Giriş Tarihi">
            <template #default="scope">
                {{scope.row.created_at}}
            </template>
        </AppTableColumn>

        <AppTableColumn label="Ülke">
            <template #default="scope">
      {{scope.row.country}}
            </template>
        </AppTableColumn>


        <AppTableColumn label="IP">
            <template #default="scope">
                 {{scope.row.ip}}
            </template>
        </AppTableColumn>

        <AppTableColumn label="Agent">
            <template #default="scope">
                 {{scope.row.user_agent}}
            </template>
        </AppTableColumn>

        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
