<script setup>
import {ref, computed} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton} from '@/Components/Buttons';

import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
import {Howl} from "howler";
import {
  AudioIcon,
  RingtoneIcon,
  MusicVideoIcon,
  PlayCircleFillIcon,

} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  tableData: {},
});

const songs = computed({
  get: () => props.tableData,
  set: (value) => emits('updateSong', value)
})


const getBody = computed(() => {
  return document.querySelector('body');
})

const onComplete = (e) => {
  location.reload();
  // console.log("EEE",e);

  // const findedIndex = props.product.songs.findIndex((el) => el.id == e.id );
  // if(findedIndex >= 0){
  //     props.product.songs[findedIndex] = e;
  // }
}

const currentSound = ref(null);
const currentSong = ref(null);
const playSound = (song) => {
  if (currentSound.value) {
    currentSound.value.pause();
    currentSound.value = null;
  }
  currentSong.value = song;

  currentSound.value = new Howl({
    src: [song.path],
    html5: true,
    onload: (e) => {
      currentSound.value.play();
    }
  });
};

const pauseMusic = (song) => {
  if (currentSound.value && currentSound.value.playing()) {
    currentSound.value.pause();

  }
  currentSound.value = null;
  currentSong.value = null;
};
</script>
<template>
  <AppTable v-model="songs" :isClient="true" :hasSearch="false" :showAddButton="false">

    <AppTableColumn label="tür" width="70">
      <template #default="scope">

        <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

          <tippy :interactive="true" theme="dark" :appendTo="getBody">
            <AudioIcon v-if="scope.row.song_type == 1" color="var(--sub-600)"/>
            <MusicVideoIcon v-if="scope.row.song_type == 2" color="var(--sub-600)"/>
            <MusicVideoIcon v-if="scope.row.song_type == 4" color="var(--sub-600)"/>
            <RingtoneIcon v-if="scope.row.song_type == 3" color="var(--sub-600)"/>

            <template #content>
              <p v-if="scope.row.song_type == 1">
                {{ __('control.song.audio') }}
              </p>
              <p v-if="scope.row.song_type == 2">
                {{ __('control.song.music_video') }}
              </p>
              <p v-if="scope.row.song_type == 3">
                {{ __('control.song.ringtone') }}
              </p>
              <p v-if="scope.row.song_type == 4">
                {{ __('control.song.apple_video') }}
              </p>

            </template>
          </tippy>
        </div>
      </template>
    </AppTableColumn>


    <AppTableColumn label="Parça Adı">
      <template #default="scope">
        <div class="flex items-center gap-3">
          <div>
            <p class="label-sm c-solid-950"> {{ scope.row.song_name }} ({{ scope.row.version }})</p>
            <p class="paragraph-xs c-sub-600"> ISRC:{{ scope.row.isrc_code }} </p>
          </div>
        </div>
      </template>
    </AppTableColumn>


    <AppTableColumn label="Plak Şirketi">
      <template #default="scope">
        <p class="paragraph-xs c-sub-600">{{ scope.row.label_name }}</p>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Dinlenme Sayısı">
      <template #default="scope">
        <span class="border border-soft-200 rounded px-2 py-0.5 label-xs c-sub-600">{{ scope.row.quantity }}</span>
      </template>
    </AppTableColumn>
    <AppTableColumn label="Dinlenme Oranı%">
      <template #default="scope">
        <span
            class="border border-soft-200 rounded px-2 py-0.5 label-xs c-sub-600">{{
            scope.row.quantity_percentage
          }}%</span>
      </template>
    </AppTableColumn>

    <template #empty>
      Şarkı bulunamadı
    </template>
  </AppTable>

</template>

<style scoped>

</style>
