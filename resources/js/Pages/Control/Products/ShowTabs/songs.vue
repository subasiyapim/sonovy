<script setup>
import {ref,computed} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton} from '@/Components/Buttons';
import {SongParticipantModal, SongDetailModal, SongAcrResponseModal, SongDialog,SongMusiciansModal} from '@/Components/Dialog';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
import {Howl} from "howler";
import {
  AudioIcon,
  RingtoneIcon,
  MusicVideoIcon,
  PlayCircleFillIcon,
  ChartsIcon,
  EyeOnIcon,
  EditIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  product: {},
});
const isSongParticipantModalOn = ref(false);
const isSongDetailModalOn = ref(false);
const isAcrResponseModalOn = ref(false);
const isSongEditModalOn = ref(false);
const isMusiciansModalON = ref(false);
const choosenSong = ref(null);
const openParticipantModal = (song) => {
  isSongParticipantModalOn.value = true;
  choosenSong.value = song;
};
const openMusicansModal = (song) => {
  isMusiciansModalON.value = true;
  choosenSong.value = song;
};
const openSongDetailModal = (song) => {
  isSongDetailModalOn.value = true;
  choosenSong.value = song;
};
const openAcrResponseModal = (song) => {
  isAcrResponseModalOn.value = true;
  choosenSong.value = song;
};
const openEditDialog = (song) => {
  isSongEditModalOn.value = true;
  choosenSong.value = song;
}

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
  <AppTable :hasSelect="true" v-model="product.songs" :isClient="true" :hasSearch="false" :showAddButton="false">
    <AppTableColumn label="No" sortable="id">
      <template #default="scope">
        <p class="table-name-text">
          {{ scope.row.id }}
        </p>
      </template>
    </AppTableColumn>
    <AppTableColumn label="tür">
      <template #default="scope">
        <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

            <tippy :interactive="true" theme="dark" :appendTo="getBody">
                    <AudioIcon v-if="scope.row.type == 1" color="var(--sub-600)"/>
                   <MusicVideoIcon v-if="scope.row.type == 2" color="var(--sub-600)"/>
                    <MusicVideoIcon v-if="scope.row.type == 4" color="var(--sub-600)"/>
                    <RingtoneIcon v-if="scope.row.type == 3" color="var(--sub-600)"/>

                <template #content>
                    <p v-if="scope.row.type == 1">
                        {{ __('control.song.audio') }}
                    </p>
                    <p v-if="scope.row.type == 2">
                        {{ __('control.song.music_video') }}
                    </p>
                    <p v-if="scope.row.type == 3">
                        {{ __('control.song.ringtone') }}
                    </p>
                    <p v-if="scope.row.type == 4">
                        {{ __('control.song.apple_video') }}
                    </p>

                </template>
            </tippy>
        </div>
      </template>
    </AppTableColumn>


    <AppTableColumn label="Parça Adı" width="304">
      <template #default="scope">

        <div class="flex items-center gap-3">

          <div>
            <p class="label-sm c-solid-950"> {{ scope.row.name }} <template v-if="scope.row.version">({{scope.row.version}})</template></p>
            <p class="paragraph-xs c-sub-600"> {{ scope.row.isrc }} </p>
          </div>
        </div>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Süre">
      <template #default="scope">
        <div v-if="currentSong !== scope.row" @click="playSound(scope.row)"
             class="cursor-pointer flex items-center gap-2">
          <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
            <PlayCircleFillIcon color="var(--dark-green-500)"/>
          </div>
          <p class="label-sm c-strong-950">
            {{ scope.row.duration ?? '2.35' }}
          </p>
        </div>
        <div v-else @click="pauseMusic(scope.row)" class="cursor-pointer flex items-center gap-2">
          <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
            <PlayCircleFillIcon color="var(--dark-green-500)"/>
          </div>
          <p class="label-sm c-strong-950">
            Durdur
          </p>
        </div>
      </template>
    </AppTableColumn>
    <AppTableColumn label="Sanatçı">
      <template #default="scope">
        <div class="flex items-center gap-2">
          <div class="flex -space-x-3 rtl:space-x-reverse">
            <template v-for="artist in scope.row.artists">
              <a class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full  "
                 :href="route('control.catalog.artists.show', artist.id)">
                <img :alt="artist.name"
                     :src="artist.image ? artist.image.thumb : defaultStore.profileImage(artist.name)"
                     class="w-8 h-8 border-2 border-white rounded-full "
                >
              </a>
            </template>
          </div>
        </div>
      </template>
    </AppTableColumn>
    <AppTableColumn label="Linkler">
      <template #default="scope">

      </template>
    </AppTableColumn>
    <AppTableColumn label="Katılımcılar">
      <template #default="scope">
        <button @click="openParticipantModal(scope.row)">
          <div class="flex items-center gap-2 label-xs c-sub-600 border border-soft-200 px-2 py-1 rounded-lg">
            <p class="w-max"> {{ scope.row.participants?.length ?? 0 }} Katılımcı</p>
          </div>
        </button>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Katkı Sağlayanlar"  width="140">
      <template #default="scope">
        <button @click="openMusicansModal(scope.row)">
          <div class="flex items-center gap-2 label-xs c-sub-600 border border-soft-200 px-2 py-1 rounded-lg">
            <p class="w-max"> {{ scope.row.musicians?.length ?? 0 }} Katılımcı</p>
          </div>
        </button>

      </template>
    </AppTableColumn>
    <AppTableColumn label="Analiz">
      <template #default="scope">
        <button @click="openAcrResponseModal(scope.row)" class="flex items-center gap-2">
          <ChartsIcon color="var(--neutral-500)"/>
          <span class="c-neutral-500 label-xs"> Analiz</span>
          <span class="w-3 h-3 rounded-full bg-[#FF8447]"></span>
        </button>
      </template>
    </AppTableColumn>
    <AppTableColumn label="Aksiyonlar" align="right">
      <template #default="scope">
        <IconButton @click="openSongDetailModal(scope.row)">
          <EyeOnIcon color="var(--sub-600)"/>
        </IconButton>
        <IconButton @click="openEditDialog(scope.row)">
          <EditIcon color="var(--sub-600)"/>
        </IconButton>
      </template>
    </AppTableColumn>
    <template #empty>
      Şarkı bulunamadı
    </template>
  </AppTable>
  <SongDialog v-if="isSongEditModalOn" :product_id="product.id" @done="onComplete" v-model="isSongEditModalOn"
              :genres="usePage().props.genres" :song="choosenSong"></SongDialog>
  <SongParticipantModal v-if="isSongParticipantModalOn" v-model="isSongParticipantModalOn"
                        :song="choosenSong"></SongParticipantModal>
  <SongMusiciansModal v-if="isMusiciansModalON" v-model="isMusiciansModalON"
                        :song="choosenSong"></SongMusiciansModal>
  <SongDetailModal v-if="isSongDetailModalOn" v-model="isSongDetailModalOn" :song="choosenSong"></SongDetailModal>
  <SongAcrResponseModal v-if="isAcrResponseModalOn" v-model="isAcrResponseModalOn" :product="product"
                        :song="choosenSong"></SongAcrResponseModal>
</template>

<style scoped>

</style>
