<script setup>
import {ref,computed} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import moment from 'moment';
import {usePage} from '@inertiajs/vue3';

import {
  AudioIcon,
  RingtoneIcon,
  MusicVideoIcon,
  PlayCircleFillIcon,
  CalendarIcon,

} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";
import {Icon} from '@/Components/Icons';
const defaultStore = useDefaultStore();
const emits = defineEmits(['update'])
const props = defineProps({
  tableData: {},
});


const getBody = computed(() => {
    return document.querySelector('body');
})
const data = computed({
    get:() => props.tableData,
    set:(val) => emits('update',val)
});


</script>
<template>

  <AppTable :hasSelect="false" v-model="data" :isClient="true" :hasSearch="false" :showAddButton="false">


    <AppTableColumn label="No" width="40">
      <template #default="scope">
        #{{scope.index+1}}
      </template>
    </AppTableColumn>
    <AppTableColumn label="tür" >
      <template #default="scope">
        <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

            <tippy :interactive="true" theme="dark" :appendTo="getBody">
                    <AudioIcon v-if="scope.row.album_type == 1" color="var(--sub-600)"/>
                   <MusicVideoIcon v-if="scope.row.album_type == 2" color="var(--sub-600)"/>
                    <MusicVideoIcon v-if="scope.row.album_type == 4" color="var(--sub-600)"/>
                    <RingtoneIcon v-if="scope.row.album_type == 3" color="var(--sub-600)"/>

                <template #content>
                    <p v-if="scope.row.album_type == 1">

                        {{ __('control.song.audio') }}
                    </p>
                    <p v-if="scope.row.album_type == 2">
                        {{ __('control.song.music_video') }}
                    </p>
                    <p v-if="scope.row.album_type == 3">
                        {{ __('control.song.ringtone') }}
                    </p>
                    <p v-if="scope.row.album_type == 4">
                        {{ __('control.song.apple_video') }}
                    </p>

                </template>
            </tippy>
        </div>
      </template>
    </AppTableColumn>
    <AppTableColumn label="Yayın Bilgisi" >
      <template #default="scope">
        <div class="flex items-center gap-3">

           <div class="w-8 h-8 rounded flex items-center justify-center border border-soft-200">
                <img v-if="scope.row.album_image" :src="scope.row.album_image?.thumb"  class="w-full h-full object-cover">
           </div>
            <div>
                <a :href="route('control.statistics.product',scope.row.album_id)" class="paragraph-xs c-blue-500"> {{scope.row.album_name}}</a>
                <p class="paragraph-xs c-sub-600"> {{scope.row.upc_code}}</p>
            </div>
        </div>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Sanatçı">
      <template #default="scope">
          <span class="paragraph-xs c-strong-950">{{ scope.row.artist_name }}</span>


      </template>
    </AppTableColumn>
    <AppTableColumn label="Plak Şirketi">
      <template #default="scope">
       <p class="paragraph-xs c-strong-950"> {{scope.row.label_name}}</p>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Yayın Tarihi">
      <template #default="scope">
        <div class="flex items-center gap-1 whitespace-nowrap">
            <CalendarIcon color="var(--sub-600)" />
            <p class="paragraph-xs c-strong-950"> {{moment(scope.row.release_date).format('DD/MM/YYYY')}}</p>

        </div>

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
