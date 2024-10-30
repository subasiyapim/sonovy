<template>

  <AdminLayout :showDatePicker="false" :title="__('control.artist.show_header')" parentTitle="Katalog"
               subParent="Tüm Şarkılar" :hasPadding="false">

    <div class="bg-white-400 h-44 p-5 relative">
      <div class="">
        <h1 class="label-xl c-strong-950" v-text="artist.name"/>
        <span class="c-sub-600 paragraph-medium hidden">@marvin-d'amore</span>
      </div>

      <div
          class="absolute rounded-full w-32 h-32 bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="artist.name"
             :src="artist.image ? artist.image.small : defaultStore.profileImage(artist.name)">
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <PrimaryButton @click="remove">
          <template #icon>
            <TrashIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
        <PrimaryButton @click="isModalOn = true">
          <template #icon>
            <EditIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
      </div>
      <div class="flex items-center gap-4 absolute bottom-5 right-5">
        <a v-for="(platform, index) in filteredPlatforms"
           class="flex items-center gap-2"
           :href="platform.pivot.url"
           :key="platform.id"
           target="_blank"
        >
          <Icon :icon="platform.icon"/>
          <span class="c-strong-950 label-sm">{{ platform.name }}</span>
          <span class="label-sm c-soft-300" v-if="index < filteredPlatforms.length - 1">•</span>
        </a>


      </div>
    </div>

    <div class="mt-32 flex items-start gap-8 h-full">
      <div class="px-8 flex-1 flex flex-col gap-12">
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.artist.artist_info')"/>
          <div class="flex items-start gap-4">
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PersonIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.name')"/>
                  <span class="label-sm c-strong-950" v-text="artist.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PersonCardIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.id')"/>
                  <span class="label-sm c-strong-950" v-text="artist.id"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.ipi_code')"/>
                  <span class="label-sm c-strong-950" v-text="artist.ipi_code"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.isni_code')"/>
                  <span class="label-sm c-strong-950" v-text="artist.isni_code"/>
                </div>
              </div>

            </div>
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <WorldIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.country')"/>
                  <span class="label-sm c-strong-950" v-text="artist.country.native"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PhoneIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.phone')"/>
                  <span class="label-sm c-strong-950" v-text="artist.phone"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <LinkIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.artist.fields.website')"/>
                  <span class="label-sm c-strong-950" v-text="artist.website"/>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="hidden">
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.artist.fields.artist_branches')"/>
          <div class="flex flex-wrap gap-3">
            <div v-for="(branch, index) in artist.artist_branches"
                 class="border border-soft-200 bg-white px-3 flex items-center py-1 rounded">
              <span class="label-xs c-sub-600">
                {{ index + 1 + ' ' + branch.name }}
              </span>
            </div>
          </div>
        </div>
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.artist.platforms')"/>
          <div class="flex flex-col flex-wrap gap-2 max-h-56 w-min">
            <div v-for="platform in artist.platforms" class="flex gap-2 items-center w-48" :key="platform.id">
              <Icon :icon="platform.icon"></Icon>
              <span class="c-strong-950 label-sm" v-text="platform.name"/>
              <a :href="platform.pivot.url" target="_blank">
                <LinkIcon color="var(--soft-400)"/>
              </a>
            </div>
          </div>
        </div>
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.artist.about')"/>
          <p class="c-sub-600 text-sm mb-5" v-html="artist.about"/>

        </div>
      </div>
      <div class="h-full bg-soft-200" style="width:1px;">
      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular" v-text="__('control.artist.artist_albums')"/>
        <template v-if="artist.products.length > 0">
          <div v-for="product in artist.products" class="flex p-4">
            <div class="flex-1 flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg overflow-hidden">
                <img src="https://placehold.co/400x400"/>
              </div>
              <div>
                <p class="text-sm c-strong-950">Parça Adı</p>
                <span class="paragraph-xs c-blue-500">Albüm Adı</span>
              </div>
            </div>
            <div class="flex items-end gap-2">
              <div class="h-3.5">
                <PlayFilledIcon color="var(--strong-950)"/>
              </div>
              <span class="paragraph-xs c-neutral-500">02:35</span>
            </div>
          </div>
        </template>
        <template v-else>
          <p v-text="__('control.artist.album_notfound')"/>
        </template>
      </div>

    </div>
    <ArtistDialog @done="onArtistProcessDone" :artist="artist" v-if="isModalOn" v-model="isModalOn"/>

  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {ArtistDialog} from '@/Components/Dialog';
import {
  DocumentIcon,
  EditIcon,
  Icon,
  LinkIcon,
  PersonCardIcon,
  PersonIcon,
  PhoneIcon,
  PlayFilledIcon,
  SpotifyIcon,
  TrashIcon,
  WorldIcon
} from '@/Components/Icons'
import {PrimaryButton} from '@/Components/Buttons'
import {useDefaultStore} from "@/Stores/default";
import {TidalIcon, YoutubeIcon} from "@/Components/Icons/index.js";
import {computed, ref} from "vue";
import {router} from '@inertiajs/vue3';

const isModalOn = ref(false);

const props = defineProps({
  artist: {
    type: Object,
    required: true
  }
});


const defaultStore = useDefaultStore();

const filteredPlatforms = computed(() => {
  return props.artist.platforms.filter(platform =>
      ['spotify', 'apple', 'youtube'].includes(platform.code)
  );
});
const onArtistProcessDone = () => {
  location.reload();
}
const remove = () => {
  router.delete(route('control.catalog.artists.destroy', props.artist.id), {});
}
</script>

<style lang="scss" scoped>

</style>
