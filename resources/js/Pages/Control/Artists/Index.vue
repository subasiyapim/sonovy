<template>

  <AdminLayout :showDatePicker="false" @dateChoosen="onDateChoosen" :title="__('control.artist.header')"
               parentTitle="Katalog">


    <AppTable
    :hasSelect="true"
        buttonLabel="Sanatçı Ekle"
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
                 class="paragraph-xs c-blue-500">{{ scope.row.name }}</a>
              <div class="flex flex-col gap-y-0.5 items-start mt-1" v-if="scope.row.platforms">

                <template v-for="platform in scope.row.platforms" :key="platform.id">


                    <a
                        v-if="platform.id == 2"
                        :href="platform.url"
                        target="_blank"
                        class="flex items-center gap-0.5 paragraph-xs c-sub-600">
                        <SpotifyIcon/>
                        {{ platform?.url?.split("/").pop() }}
                    </a>

                  <a v-if="platform.id == 4"
                     :href="platform.url"
                     target="_blank"
                     class="flex items-center gap-0.5 mt-0.5 paragraph-xs c-sub-600">
                    <AppleMusicIcon/>
                    {{ platform?.url?.split("/").pop().split("?")[0] }}
                  </a>
                </template>

              </div>
            </div>
          </div>
        </template>
      </AppTableColumn>


      <AppTableColumn :label="__('control.artist.fields.tracks_count')">
        <template #default="scope">
            <p class="paragraph-xs c-strong-950">  {{ scope.row.tracks_count }}</p>
        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.artist.fields.genres')">
        <template #default="scope">

          <BasicBadge class="mx-1" v-for="(genre, index) in scope.row.genres" :key="index">
            {{ genre }}
          </BasicBadge>
        <BasicBadge>
            <span class="paragraph-xs c-strong-950" v-if="scope.row.genres_count > 0">+{{ scope.row.genres_count }}</span>
        </BasicBadge>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.general.actions')" align="right">
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
              <AddIcon  color="var(--dark-green-500)" />
            </template>
            {{ __('control.artist.first_create_btn') }}
          </PrimaryButton>
        </div>
      </template>
    </AppTable>

    <ArtistDialog @done="onDone" @update="onUpdate" :artist="chosenArtist" v-if="isModalOn" v-model="isModalOn"/>
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
import {StatusBadge, BasicBadge} from '@/Components/Badges'

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
  artistTable.value.removeRowDataFromRemote(row);
}
const editRow = (artist) => {

  chosenArtist.value = artist;
  isModalOn.value = !isModalOn.value;
}
const onDateChoosen = (e) => {
  artistTable.value.search('daterange', e);
}

const onDone = (e) => {
  artistTable.value.addRow(e);


}
const onUpdate = (e) => {
  artistTable.value.editRow(e);
}
</script>

<style lang="scss" scoped>

</style>
