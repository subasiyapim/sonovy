<template>

  <AdminLayout :showDatePicker="false" :title="__('control.song.show_header')"
               :parentTitle="__('control.song.title_singular')"
               :hasPadding="false">
    <template #breadcrumb>
        <span class="label-xs c-soft-400">Katalog</span>
        <span class="label-xs c-soft-400">•</span>
        <span class="label-xs c-soft-400 cursor-pointer" @click="router.visit(route('control.catalog.songs.index'))">Şarkılar</span>
        <span class="label-xs c-soft-400">•</span>
        <span class="label-xs c-soft-400">
            {{song.type == 1 ? 'Ses Yayın' :(song.type == 2 ? 'Müzik Video' : (song.type == 4 ? 'Apple Video' : 'Zil Sesi') ) }}
        </span>
        <span class="label-xs c-soft-400">•</span>
        <span class="label-xs c-soft-400">{{ song.name }}</span>
             <template v-if="song.version"><p class="label-xs c-soft-400"> ({{song.version}})</p></template>
    </template>
    <div class="bg-dark-green-800  p-5 flex  relative">
      <div
          class=" rounded-lg w-60 h-60 bg-blue-300 left-8 top-8 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="song.name"
             :src="song.image ? song.image.original_url : 'https://via.placeholder.com/150'">
      </div>
      <div class=" flex-1 ms-4 flex flex-col justify-end">
        <div class="bg-white rounded-lg flex items-center gap-2 px-2 py-1 w-min mb-6">
          <CheckFilledIcon color="var(--dark-green-800)"/>
          <p class="label-xs c-strong-950" v-text="song.product_status"/>
        </div>
        <h1 style="line-height:72px;" class="flex items-center text-5xl text-white font-semibold">
                {{song.name}}
             <template v-if="song.version"><p > ({{song.version}})</p></template>

        </h1>


        <div class="flex items-center gap-2">
          <div class="w-6 h-6 bg-blue-300 rounded-full overflow-hidden me-3">
            <img alt="" class="w-full h-full image-fluid"
                 :src="song?.main_artist?.image ? song?.main_artist.image.original_url : 'https://via.placeholder.com/150' "/>
          </div>
          <p class="label-sm text-white me-1">{{ song.main_artist?.name }}</p>
          <p class="c-sub-600 paragraph-sm hidden">@ellenrow</p>

          <span class="text-white paragraph-sm">{{ song.duration }}</span>
        </div>


      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <!-- <PrimaryButton @click="remove">
          <template #icon>
            <TrashIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
        <PrimaryButton @click="isModalOn = true">
          <template #icon>
            <EditIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton> -->
      </div>
    </div>

    <div class="mt-32 flex items-start gap-8 h-full">
      <div class="px-8 flex-1 flex flex-col gap-12">
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.song.show.song_info')"/>
          <div class="flex items-start gap-4 mb-3">
            <div class="flex flex-col gap-8 flex-1">


              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.song.show.album_name')"/>
                  <span class="label-sm c-strong-950">
                    {{song.album_name}}
                    <template v-if="song.version">({{song.version}})</template>
                  </span>

                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.song.show.artist')"/>
                  <span class="label-sm c-strong-950" v-text="song?.main_artist?.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.song.show.label')"/>
                  <span class="label-sm c-strong-950" v-text="song.label_name"/>
                </div>
              </div>


              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.song.show.isrc')"/>
                  <span class="label-sm c-strong-950" v-text="song.isrc"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.song.show.upc_code')"/>
                  <span class="label-sm c-strong-950" v-text="song.upc_code"/>
                </div>
              </div>


            </div>
            <div class="flex flex-col gap-8 flex-1">
              <!--- SONG PLATFORM --->
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <LinkIcon color="var(--sub-600)"/>
                </div>
                <div v-for="platform in song.platforms" :key="platform.id">
                  <p class="paragraph-xs c-sub-600" v-text="platform.name"/>
                  <span class="label-sm c-strong-950" v-text="platform.pivot.url"/>
                </div>
              </div>
            </div>
          </div>
          <h1 class="my-9 subheading-regular text-start" v-text="__('control.song.show.participants')"/>
           <div class="mb-10 grid grid-cols-2 gap-12">
            <div v-for="participant in song.participants" class="flex items-start gap-2 w-64">
                <div class="w-12 h-12 rounded-full bg-blue-300 flex items-center justify-center">
                    <img class="image-fluid" :src="participant.user?.image">
                </div>
                <div class="flex flex-col items-start">
                   <p class="c-sub-600 label-xs mb-2">{{participant.user?.name}}</p>
                    <p class="c-strong-950 label-sm">{{participant.user?.name}}</p>
                </div>
                <div class="label-xs c-sub-600 ms-auto">
                   %  {{participant.rate}}
                </div>
            </div>
           </div>
        </div>

      </div>
      <div class="h-full bg-soft-200" style="width:1px;">

      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular">
          {{ __('control.song.show.song_albums') }} (12)
        </h1>

        <template v-if="song.other_songs">
          <div v-for="song in song.other_songs" class="flex p-4">
            <div class="flex-1 flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg overflow-hidden">
                <img v-if="song.image" :src="song.image ? song.image.original_url : 'https://via.placeholder.com/150'"/>
              </div>
              <div>
                <p class="text-sm c-strong-950">{{ song.album_name }}</p>
                <span class="paragraph-xs c-blue-500">{{ song.name }}</span>
              </div>
            </div>
            <div class="flex items-end gap-2">
              <div class="h-3.5">
                <PlayFilledIcon color="var(--strong-950)"/>
              </div>
              <span class="paragraph-xs c-neutral-500">{{ song.duration }}</span>
            </div>
          </div>
        </template>
        <template v-else>
          <p v-text="__('control.song.album_notfound')"/>
        </template>
      </div>
    </div>
     <SongDialog v-if="isModalOn" :product_id="song.product_id" @done="onComplete" v-model="isModalOn"
              :genres="genres" :song="song"></SongDialog>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {router} from '@inertiajs/vue3';
import {
  PersonCardIcon,
  PercantageIcon,
  MessageIcon,
  PhoneIcon,
  DocumentIcon,
  TrashIcon,
  EditIcon,
  SpotifyIcon,
  CheckFilledIcon,
  GenreIcon,
  PlayFilledIcon,
  LinkIcon
} from '@/Components/Icons'

const isModalOn = ref(false);
import {PrimaryButton} from '@/Components/Buttons'
import {AppIncrementer} from '@/Components/Form'
import {ref} from 'vue';
import {useDefaultStore} from "@/Stores/default";
import {SongDialog} from '@/Components/Dialog';

const props = defineProps({
  song: {
    type: Object,
    required: true
  },
  genres:{

  },
})

const onDone = () => {
  isModalOn.value = false;
}

const defaultStore = useDefaultStore();

const appIncrementerConfig = {
  formatter: (value) => {
    return '%' + value;
  }
};

const remove = () => {
  router.delete(route('control.catalog.labels.destroy', props.song.id), {});
}
</script>

<style lang="scss" scoped>

</style>
