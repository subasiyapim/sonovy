<script setup>
import {ref,computed} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';

import {usePage} from '@inertiajs/vue3';

import {useDefaultStore} from "@/Stores/default";
import {Icon} from '@/Components/Icons';
const defaultStore = useDefaultStore();
const emits = defineEmits(['update'])
const props = defineProps({
  tableData: {},
});

const data = computed({
    get:() => props.tableData,
    set:(val) => emits('update',val)
});


</script>
<template>

  <AppTable :hasSelect="false" v-model="data" :isClient="true" :hasSearch="false" :showAddButton="false">



    <AppTableColumn label="No" width="40">
      <template #default="scope">
                  <span class="paragraph-xs c-strong-950"> #{{scope.index+1}}</span>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Platform" >
      <template #default="scope">
        <div class="flex items-center gap-3">
           <div class="w-8 h-8 rounded-full flex items-center justify-center border border-soft-200"> <Icon :icon="scope.row.platform_image" /></div>
            <p class="label-sm c-strong-950"> {{scope.row.platform_name}}</p>
        </div>

      </template>
    </AppTableColumn>

    <AppTableColumn label="Toplam Parça Sayısı">
      <template #default="scope">
         <span class="paragraph-xs c-sub-600">
         {{scope.row.song_count}} Parça
         </span>


      </template>
    </AppTableColumn>
    <AppTableColumn label="Dinlenme Sayısı">
      <template #default="scope">
        <span class="border border-soft-200 rounded px-2 py-0.5 label-xs c-sub-600">{{scope.row.quantity}}</span>


      </template>
    </AppTableColumn>
    <AppTableColumn label="% Stream">
      <template #default="scope">
        <span class="border border-soft-200 rounded px-2 py-0.5 label-xs c-sub-600">{{scope.row.quantity_percentage}}%</span>
      </template>
    </AppTableColumn>

    <template #empty>
      Veri bulunamadı
    </template>
  </AppTable>

</template>

<style scoped>

</style>
