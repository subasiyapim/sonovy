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
           :href="platform.url"
           :key="platform.id"
           target="_blank"
        >
          <Icon :icon="platform.icon"/>
          <span class="c-strong-950 label-sm">{{ platform.name }}</span>
          <span class="label-sm c-soft-300" v-if="index < filteredPlatforms.length - 1">•</span>
        </a>


      </div>
    </div>
    <div class="flex items-center gap-2 mt-32 mb-5 ms-6">
        <button @click="changeActiveTab('artist_info')" class="py-1.5 px-3 rounded-full label-xs" :class="activeTab == 'artist_info' ? 'bg-dark-green-800 text-white' : 'bg-weak-50 c-sub-600' ">Sanatçı Bilgileri</button>
        <button @click="changeActiveTab('products')" class="py-1.5 px-3 rounded-full label-xs" :class="activeTab == 'products' ? 'bg-dark-green-800 text-white' : 'bg-weak-50 c-sub-600' ">Yayınlar</button>
        <button @click="changeActiveTab('songs')" class="py-1.5 px-3 rounded-full label-xs" :class="activeTab == 'songs' ? 'bg-dark-green-800 text-white' : 'bg-weak-50 c-sub-600' "> Parçalar</button>
    </div>
    <div v-if="activeTab == 'artist_info'" class="flex items-start gap-8 h-full">
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
                  <span class="label-sm c-strong-950" v-text="artist?.country?.name"/>
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
              <a :href="platform.url" target="_blank">
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
                <p class="text-sm c-strong-950">{{ __('control.artist.song_name') }}</p>
                <span class="paragraph-xs c-blue-500">{{ __('control.artist.album_name') }}</span>
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
    <div v-else-if="activeTab == 'products'" class="px-6">
        <AppTable v-model="tab.products"  :isClient="true" :hasSearch="false" :showAddButton="false">
            <AppTableColumn label="Tür" sortable="type" width="80">
                <template #default="scope">
                <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

                    <tippy :interactive="true" theme="dark" :appendTo="getBody">
                            <AudioIcon v-if="scope.row.type == 1" color="var(--sub-600)"/>
                        <MusicVideoIcon v-if="scope.row.type == 2" color="var(--sub-600)"/>
                            <MusicVideoIcon v-if="scope.row.type == 4" color="var(--sub-600)"/>
                            <RingtoneIcon v-if="scope.row.type == 3" color="var(--sub-600)"/>

                        <template #content>
                            <p v-if="scope.row.type == 1">
                                Ses Yayını
                            </p>
                            <p v-if="scope.row.type == 2">
                                Müzik Video
                            </p>
                            <p v-if="scope.row.type == 3">
                                Zil Sesi
                            </p>
                            <p v-if="scope.row.type == 4">
                                Apple Video
                            </p>

                        </template>
                    </tippy>

                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Yayın Bilgisi" >
                <template #default="scope">
                <div class="flex gap-x-2 items-start">
                    <div class="w-8 h-8 rounded overflow-hidden">
                    <img class="w-10 h-10" alt=""
                        :src="scope.row.image ? scope.row.image.thumb : 'https://loremflickr.com/400/400'">

                    <img :alt="scope.row.album_name"
                        :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.album_name)"
                    >

                    </div>
                    <div class="flex flex-col flex-1 items-start justisy-start">
                    <a :href="route('control.catalog.products.show',scope.row.id)" class="paragraph-xs c-blue-500">
                        {{ scope.row.album_name }}
                    </a>

                    <div class=" paragraph-xs c-strong-950 ">
                        <p>
                        <template v-for="(artist,artistIndex) in scope.row.main_artists">
                            {{ artist.name }}
                            <template v-if="artistIndex != scope.row.main_artists.length-1">,&nbsp;</template>
                        </template>
                        </p>

                    </div>
                    </div>
                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Yayın Tarih">
                <template #default="scope">
                <div v-if="scope.row.physical_release_date" class="flex items-center gap-3">
                    <p class="paragraph-xs c-sub-600 whitespace-nowrap">
                    {{ scope.row.physical_release_date }}
                    </p>
                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="UPC/Katalog" width="240">
                <template #default="scope">
                    <div class="flex flex-col justify-start ">
                        <span class="paragraph-xs c-sub-600">UPC:{{ scope.row.upc_code ?? 'Boş' }}</span>
                        <span class="paragraph-xs c-sub-600">Katalog Numarası: {{ scope.row.catalog_number ?? 'Boş' }}</span>
                    </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Rol">
                <template #default="scope">
                  <p class="paragraph-xs c-sub-600">  {{scope.row.artist_role}}</p>
                </template>
            </AppTableColumn>
        </AppTable>
    </div>
    <div v-else-if="activeTab == 'songs'" class="px-6">
        <AppTable v-model="tab.songs"  :isClient="true" :hasSearch="false" :showAddButton="false">
       <AppTableColumn label="Tür" sortable="type" width="80">
                <template #default="scope">
                <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

                    <tippy :interactive="true" theme="dark" :appendTo="getBody">
                            <AudioIcon v-if="scope.row.type == 1" color="var(--sub-600)"/>
                        <MusicVideoIcon v-if="scope.row.type == 2" color="var(--sub-600)"/>
                            <MusicVideoIcon v-if="scope.row.type == 4" color="var(--sub-600)"/>
                            <RingtoneIcon v-if="scope.row.type == 3" color="var(--sub-600)"/>

                        <template #content>
                            <p v-if="scope.row.type == 1">
                                Ses Yayını
                            </p>
                            <p v-if="scope.row.type == 2">
                                Müzik Video
                            </p>
                            <p v-if="scope.row.type == 3">
                                Zil Sesi
                            </p>
                            <p v-if="scope.row.type == 4">
                                Apple Video
                            </p>

                        </template>
                    </tippy>

                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Şarkı Bilgisi">
                <template #default="scope">
                   <a :href="route('control.catalog.songs.show',scope.row.id)" class="paragraph-xs c-blue-500"> {{scope.row.name}}</a>
                </template>
            </AppTableColumn>
            <AppTableColumn :label="'Sanatçı'" sortable="name">
                <template #default="scope">
                <div class="flex gap-3 items-center" v-for="artist in scope.row.main_artists">

                    <span class="paragraph-xs c-strong-950">{{ artist.name }} </span>
                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn :label="'Süre'" sortable="name" width="150">
                <template #default="scope">
                <div v-if="currentSong !== scope.row" @click="playSound(scope.row)"
                    class="flex items-center gap-2 cursor-pointer">
                    <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                    <PlayCircleFillIcon color="var(--dark-green-500)"/>
                    </div>
                    <p class="label-sm c-strong-950">
                    {{ scope.row.duration ?? '2.35' }}
                    </p>
                </div>
                <div v-else @click="pauseMusic(scope.row)" class="flex items-center gap-2 cursor-pointer">
                    <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                    <PlayCircleFillIcon color="var(--dark-green-500)"/>
                    </div>
                    <p class="label-sm c-strong-950">
                        {{ __('control.song.pause') }}
                    </p>
                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Yayın Tarih" >
                <template #default="scope">
                    <div class="flex items-center gap-1">

                        <CalendarIcon  color="var(--sub-600)" />
                        <p class="paragraph-xs c-sub-600">{{ moment(scope.row.created_at).format('DD/MM/YYYY') }}</p>
                    </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="ISRC Kodu" >
                <template #default="scope">
                    <p class="paragraph-xs c-sub-600">{{ scope.row.isrc }}</p>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Rol" >
                <template #default="scope">
                    <p class="paragraph-xs c-sub-600" v-for="role in scope.row.artist_role">{{role}}</p>
                </template>
            </AppTableColumn>

        </AppTable>
    </div>
    <ArtistDialog @done="onArtistProcessDone" :artist="artist" v-if="isModalOn" v-model="isModalOn"/>

  </AdminLayout>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';

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
    PlayCircleFillIcon,
    PlayFilledIcon,
    SpotifyIcon,
    TrashIcon,
    WorldIcon,
    CalendarIcon,
    AudioIcon,
    MusicVideoIcon,
    RingtoneIcon
} from '@/Components/Icons'
import {Howl} from "howler";
import {PrimaryButton} from '@/Components/Buttons'
import {useDefaultStore} from "@/Stores/default";
import {TidalIcon, YoutubeIcon} from "@/Components/Icons/index.js";
import {computed, ref} from "vue";
import {router} from '@inertiajs/vue3';
import moment from 'moment';
const isModalOn = ref(false);

const props = defineProps({
  artist: {
    type: Object,
    required: true
  },
  tab:{

  }
});
const products = ref([])
const songs = ref([])
let params = new URLSearchParams(window.location.search)

const activeTab = ref(params.get('slug') ?? 'artist_info')
const changeActiveTab = (e) => {
      router.visit(route(route().current(),props.artist.id), {
        data: {
            slug:e,
        },
        preserveScroll: true,
    });
}

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

    const getBody = computed(() => {
        return document.querySelector('body');
    });

</script>

<style lang="scss" scoped>

</style>
