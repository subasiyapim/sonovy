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




      <AppTableColumn :label="__('control.artist.fields.name')" align="left">
        <template #default="scope">
          <div class="flex justify-start items-center gap-2 w-full">
            <div class="w-12 h-12 rounded-full overflow-hidden">
              <img :alt="scope.row.artist_name"
                   :src="scope.row.artist_image ? scope.row.artist_image.thumb : defaultStore.profileImage(scope.row.artist_name)"
              >
            </div>
            <div>

              <a :href="route('control.statistics.artist',scope.row.artist_id)"
                 class="font-poppins table-name-text c-sub-600">{{ scope.row.artist_name }}</a>

              <div class="flex flex-row gap-x-2 items-center" v-if="scope.row.platforms">

                <template v-for="platform in scope.row.platforms" :key="platform.id">

                  <div class="flex items-center" v-if="platform.id == 2">
                    <a
                        :href="platform.url"
                        target="_blank"
                        class="flex items-center gap-2 paragraph-xs c-sub-600">
                        <SpotifyIcon/>
                        {{ platform?.url?.split("/").pop() }}
                    </a>
                    <span class="paragraph-xs c-sub-600 ms-3">·</span>
                  </div>
                  <a v-if="platform.id == 4"
                     :href="platform.url"
                     target="_blank"
                     class="flex items-center ms-3 gap-2 paragraph-xs c-sub-600">
                    <AppleMusicIcon/>
                    {{ platform?.url?.split("/").pop().split("?")[0] }}
                  </a>
                </template>

              </div>
            </div>
          </div>
        </template>
      </AppTableColumn>
    <AppTableColumn label="Toplam Parça Sayısı">
      <template #default="scope">
        <span class="paragraph-xs c-sub-600">{{scope.row.song_count}} Parça</span>


      </template>
    </AppTableColumn>
    <AppTableColumn label="Dinlenme Sayısı">
      <template #default="scope">
        <span class="border border-soft-200 rounded px-2 py-0.5 label-xs c-sub-600">{{scope.row.quantity}}</span>
      </template>
    </AppTableColumn>
    <AppTableColumn label="Dinlenme Oranı%">
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
