<template>
  <AdminLayout title="Tüm Sanatçılar" parentTitle="Katalog">

    <AppTable v-model="usePage().props.artists" @addNewClicked="openDialog">
      <AppTableColumn label="Sanatçı Adı">
        <template #default="scope">
          <div class="flex items-center gap-2 ">
            <div class="w-12 h-12 rounded-full overflow-hidden">
              <img :alt="scope.row.name"
                   :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
              >
            </div>
            <div>
              <h3 class="font-bold text-lg">{{ scope.row.name }}</h3>
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
      <AppTableColumn label="Durum">
        <template #default="scope">
          {{ scope.row.status }}
        </template>
      </AppTableColumn>

      <AppTableColumn label="Top. Parça Sayısı">
        <template #default="scope">
          {{ scope.row.tracks_count }}
        </template>
      </AppTableColumn>

      <AppTableColumn label="Tarzlar">
        <template #default="scope">
          <span v-for="(branch, index) in scope.row.artist_branches" :key="index">
            {{ branch }}
          </span>
          <span v-if="scope.row.artist_branches_count > 0">+{{ scope.row.artist_branches_count }}</span>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Aksiyonlar">
        <template #default="scope">
          <div class="flex gap-3">
            <IconButton>
              <TrashIcon color="var(--sub-600)"/>
            </IconButton>
            <IconButton>
              <EditIcon color="var(--sub-600)"/>
            </IconButton>
          </div>
        </template>
      </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">Henüz yayınız bulunmamaktadır.</h2>
            <h3 class="paragraph-medium c-neutral-500">Oluşturucağınız tüm yayınlar burada listelenecektir.</h3>
          </div>
          <PrimaryButton>
            <template #icon>
              <AddIcon/>
            </template>
            İlk Yayını Oluştur
          </PrimaryButton>
        </div>
      </template>
    </AppTable>

    <AddArtistDialog v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {AddIcon, TrashIcon, EditIcon, AppleMusicIcon, SpotifyIcon} from '@/Components/Icons'
import {AddArtistDialog} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";
import {Link} from "@inertiajs/vue3";

const defaultStore = useDefaultStore();

const props = defineProps({
  artists: Object
})

const isModalOn = ref(false);
const openDialog = () => {
  isModalOn.value = !isModalOn.value;
}
</script>

<style lang="scss" scoped>

</style>
