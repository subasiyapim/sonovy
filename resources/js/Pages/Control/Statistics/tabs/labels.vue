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



    <AppTableColumn label="No" :shrink-to-width="true">
      <template #default="scope">
           <span class="paragraph-xs c-strong-950"> #{{scope.index+1}}</span>
      </template>
    </AppTableColumn>
      <AppTableColumn :label="__('control.label.title_singular')" align="left" sortable="name" width="400">
        <template #default="scope">

          <div class="flex items-center gap-2 ">
            <div class="w-12 h-12 rounded-full overflow-hidden">
              <img :alt="scope.row.label_name"
                   :src="scope.row.label_image ? scope.row.label_image.thumb : defaultStore.profileImage(scope.row.label_name)"
              >
            </div>
            <a :href="route('control.statistics.label',scope.row.label_id)"
               class="paragraph-xs c-blue-500">{{ scope.row.label_name }}</a>


          </div>
        </template>
      </AppTableColumn>
    <AppTableColumn label="Toplam Parça Sayısı">
      <template #default="scope">
               <span class="paragraph-xs c-strong-950">{{scope.row.song_count ?? 0}} Parça</span>



      </template>
    </AppTableColumn>
    <AppTableColumn label="Dinlenme Sayısı">
      <template #default="scope">
        <span class="border border-soft-200 rounded px-2 py-0.5 label-xs c-strong-950">{{scope.row.quantity}}</span>
      </template>
    </AppTableColumn>
    <AppTableColumn label="% Stream">
      <template #default="scope">
        <span class="border border-soft-200 rounded px-2 py-0.5 label-xs c-strong-950">{{scope.row.quantity_percentage}}%</span>
      </template>
    </AppTableColumn>

    <template #empty>
      Veri bulunamadı
    </template>
  </AppTable>

</template>

<style scoped>

</style>
