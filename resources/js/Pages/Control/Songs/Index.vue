<template>
  <AdminLayout :showDatePicker="false" :title="__('control.song.header')" parentTitle="Katalog">

    <AppTable :showAddButton="false" ref="pageTable" :config="appTableConfig"
              v-model="usePage().props.songs" @addNewClicked="openDialog">
      <AppTableColumn :label="'Tür'" align="left" sortable="name" width="64">
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
      <!-- <AppTableColumn :label="'Durum'" sortable="name" >
        <template #default="scope">
          <StatusBadge v-text="props.statuses[scope.row.status]"
                       :type="scope.status === 1 ? 'success': 'pending'">
          </StatusBadge>

        </template>
      </AppTableColumn> -->
        <AppTableColumn :label="'Parça Adı'" sortable="name">
            <template #default="scope">
            <div class="flex flex-col items-start">
                <a :href="route('control.catalog.songs.show',scope.row.id)" class="paragraph-xs c-blue-500">
                {{ scope.row.name }} <template v-if="scope.row.version">({{scope.row.version}})</template>
                </a>
                <p class="paragraph-xs c-sub-600">ISRC: {{ scope.row.isrc }}</p>
            </div>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Yayın Bilgisi" width="400">
            <template #default="scope">

                <div v-if="scope.row.products.length > 0" class="flex gap-x-2 items-start">
                    <div class="w-8 h-8 rounded overflow-hidden">
                    <img class="w-10 h-10" alt=""
                        :src="scope.row.products[0].image ? scope.row.products[0].image.thumb : 'https://loremflickr.com/400/400'">

                    <img :alt="scope.row.products[0].album_name"
                        :src="scope.row.products[0].image ? scope.row.products[0].image.thumb : defaultStore.profileImage(scope.row.products[0].album_name)"
                    >

                    </div>
                    <div class="flex flex-col flex-1 items-start justisy-start">
                    <a :href="route('control.catalog.products.show',scope.row.products[0].id)" class="paragraph-xs c-blue-500">
                        {{ scope.row.products[0].album_name }} <template v-if="scope.row.products[0].version">({{scope.row.products[0].version}})</template>

                    </a>


                    <div class=" paragraph-xs c-strong-950 ">
                        <p>
                        <template v-for="(artist,artistIndex) in scope.row.products[0].artists">
                            {{ artist.name }}
                            <template v-if="artistIndex != scope.row.products[0].artists.length-1">,&nbsp;</template>
                        </template>
                        </p>

                    </div>
                    </div>
                </div>
            </template>
        </AppTableColumn>

        <AppTableColumn :label="'Süre'" sortable="name">
            <template #default="scope">
            <div v-if="currentSong !== scope.row" @click="playSound(scope.row)"
                class="flex items-center gap-2 cursor-pointer">
                <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                <PlayCircleFillIcon color="var(--dark-green-500)"/>
                </div>
                <p class="paragraph-xs c-strong-950">
                {{ scope.row.duration ?? '2.35' }}
                </p>
            </div>
            <div v-else @click="pauseMusic(scope.row)" class="flex items-center gap-2 cursor-pointer">
                <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                <PlayCircleFillIcon color="var(--dark-green-500)"/>
                </div>
                <p class="paragraph-xs c-strong-950">
                    {{ __('control.song.pause') }}
                </p>
            </div>
            </template>
        </AppTableColumn>
        <AppTableColumn :label="'Sanatçı'" sortable="name">
            <template #default="scope">
                <div class="flex gap-3 items-center" v-for="artist in scope.row.main_artists">
                    <img :alt="scope.row.name"
                        class="w-10 h-10 rounded-full overflow-hidden"
                        :src="artist.image ? artist.image.thumb : defaultStore.profileImage(artist.name)">
                    <span class="paragraph-xs c-strong-950">{{ artist.name }} </span>
                </div>
                <p class="paragraph-xs c-strong-950" v-if="scope.row.main_artists.length <= 0">
                    Sanatçı bulunmuyor
                </p>
            </template>
        </AppTableColumn>
        <AppTableColumn :label="'Katılımcı'" sortable="name">
            <template #default="scope">
                <button @click="openParticipantModal(scope.row)">
                    <div class="flex items-center gap-2 label-xs c-sub-600 border border-soft-200 px-2 py-1 rounded-lg">
                        <p class="w-max"> {{ scope.row.participants?.length ?? 0 }} Katılımcı</p>
                    </div>
                </button>
            </template>
        </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">{{ __('control.song.table.empty_header') }}</h2>
            <h3 class="paragraph-medium c-neutral-500">{{ __('control.song.table.empty_description') }}</h3>
          </div>
        </div>
      </template>
    </AppTable>

    <LabelDialog :label="choosenLabel" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
    <SongParticipantModal v-if="isSongParticipantModalOn" v-model="isSongParticipantModalOn"
                        :song="choosenSong"></SongParticipantModal>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {StatusBadge} from '@/Components/Badges'
import {Howl} from "howler";

import {
  PlayCircleFillIcon,
  MusicVideoIcon,
  RingtoneIcon,
  AudioIcon
} from '@/Components/Icons'
import {LabelDialog,SongParticipantModal} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";

const pageTable = ref();
const defaultStore = useDefaultStore();
const props = defineProps({
  filters: {
    type: Array,
    default: () => [],
    required: false
  },
  product_statuses: {
    type: Object,
    default: () => [],
    required: false
  },
  statuses: {
    type: Object,
    default: () => [],
    required: false
  }
})
const isSongParticipantModalOn = ref(false);
const choosenSong = ref(null);

const openParticipantModal = (song) => {
    console.log("SONG",song);

  isSongParticipantModalOn.value = true;
  choosenSong.value = song;
};

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
const getBody = computed(() => {
    return document.querySelector('body');
})
const pauseMusic = (song) => {
  if (currentSound.value && currentSound.value.playing()) {
    currentSound.value.pause();

  }
  currentSound.value = null;
  currentSong.value = null;
};
const data = ref([])
const choosenLabel = ref(null);
const isModalOn = ref(false);
const openDialog = () => {
  isModalOn.value = !isModalOn.value;
}

const deleteRow = (row) => {
  pageTable.value.removeRowDataFromRemote(row);
}
const editRow = (label) => {
  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}
const onDone = (e) => {
  pageTable.value.addRow(e);
}

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})
</script>

<style lang="scss" scoped>

</style>
