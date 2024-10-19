<template>

  <AdminLayout @dateChoosen="onDateChoosen" :title="__('control.artist.header')" parentTitle="Katalog">


    <AppTable
        ref="artistTable"
        v-model="usePage().props.artists"
        @addNewClicked="openAddDialog"
        :config="appTableConfig"
        :slug="route('control.catalog.artists.index')">
      <AppTableColumn :label="__('control.artist.fields.name')" align="left">
        <template #default="scope">
          <div class="flex justify-start items-center gap-2 w-full">
            <div class="w-12 h-12 rounded-full overflow-hidden">
              <img :alt="scope.row.name"
                   :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
              >
            </div>
            <div>
              <a :href="route('control.catalog.artists.show',scope.row.id)"
                 class="c-sub-600 text-sm leading-4 font-bold">{{ scope.row.name }}</a>
              <div class="flex flex-row gap-x-2 items-center" v-if="scope.row.platforms">
                <template v-for="platform in scope.row.platforms" :key="platform.id">
                  <a v-if="platform.code === 'spotify'"
                     :href="platform.pivot.url"
                     target="_blank"
                     class="flex items-center gap-x-1">
                    <SpotifyIcon/>
                    {{ platform.pivot.url }}
                  </a>
                  <a v-if="platform.code === 'apple'"
                     :href="platform.pivot.url"
                     target="_blank"
                     class="flex items-center gap-x-1">
                    <AppleMusicIcon/>
                    {{ platform.pivot.url }}
                  </a>
                </template>

              </div>
            </div>
          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Durum" sortable="status">
        <template #default="scope">

          <StatusBadge>
            {{ scope.row.status }}
          </StatusBadge>
        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.artist.fields.tracks_count')">
        <template #default="scope">
          {{ scope.row.tracks_count }}
        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.artist.fields.genres')">
        <template #default="scope">
          <BasicBadge class="mx-1" v-for="(genre, index) in scope.row.genres" :key="index">
            {{ genre }}
          </BasicBadge>
          <span v-if="scope.row.genres_count > 0">+{{ scope.row.genres_count }}</span>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.general.actions')">
        <template #default="scope">
          <div class="flex gap-3">
            <IconButton @click="deleteRow(scope.row)">
              <TrashIcon color="var(--sub-600)"/>
            </IconButton>
            <IconButton @click="editRow(scope.row)">
              <EditIcon color="var(--sub-600)"/>
            </IconButton>
          </div>
        </template>
      </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">{{ __('control.artist.notfound') }}</h2>
            <h3 class="paragraph-medium c-neutral-500">{{ __('control.artist.notfound_subtitle') }}</h3>
          </div>
          <PrimaryButton @click="openAddDialog">
            <template #icon>
              <AddIcon/>
            </template>
            {{ __('control.artist.first_create_btn') }}
          </PrimaryButton>
        </div>
      </template>
    </AppTable>

    <ArtistDialog :artist="chosenArtist" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed, nextTick} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {AddIcon, TrashIcon, EditIcon, AppleMusicIcon, SpotifyIcon} from '@/Components/Icons'
import {ArtistDialog} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";
import {Link} from "@inertiajs/vue3";
import {StatusBadge, BasicBadge} from '@/Components/Badges'
import {showNotification} from '@/Components/Notification';
import {toast} from 'vue3-toastify';

const defaultStore = useDefaultStore();
const artistTable = ref();
const props = defineProps({
  artists: Object,
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})

const chosenArtist = ref(null);
const isModalOn = ref(false);
const openAddDialog = () => {
  chosenArtist.value = null;
  isModalOn.value = !isModalOn.value;
}

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})


const deleteRow = (row) => {
  //EXAMPLE ROW SİLME İÇİN
  showNotification();
  toast.success('Sanatçı başarıyla silindi');
  artistTable.value.removeRowData(row);
}
const editRow = (artist) => {

  chosenArtist.value = artist;
  isModalOn.value = !isModalOn.value;


}
const onDateChoosen = (e) => {
  artistTable.value.search('daterange', e);


}


</script>

<style lang="scss" scoped>

</style>
