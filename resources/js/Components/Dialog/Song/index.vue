<template>
  <BaseDialog v-model="isDialogOn" align="right" :title="song.name"
              :description="(song.size / (1024*1024)).toFixed(2)">
    <template #icon>
      <img width="24" height="24" src="@/assets/images/mp3_active.png"/>
    </template>
    <SectionHeader :title="__('control.song.dialog.header_1')"/>
    <div class="p-5 flex flex-col gap-6">
      <FormElement label-width="190px"
                   :required="true"
                   :error="form.errors.name"
                   :config="{letter:500}"
                   v-model="form.name"
                   :label="__('control.song.fields.name')"
                   type="text"
                   :placeholder="__('control.song.fields.name_placeholder')"/>
      <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.version"
                   :config="{letter:500}"
                   v-model="form.version"
                   :label="__('control.song.fields.version')"
                   type="text"
                   :placeholder="__('control.song.fields.version_placeholder')"/>

      <FormElement ref="mainArtistSelect" label-width="190px" v-model="form.main_artists"
                   :error="form.errors.main_artists" type="select"
                   :label="__('control.song.fields.main_artists')"
                   :placeholder="__('control.song.fields.main_artists_placeholder')" :config="mainArtistSelectConfig">
        <template #first_child>
          <button @click="openArtistCreateDialog('main_artists')"
                  class="flex items-center gap-2 paragraph-sm c-sub-600 p-2">
            <AddIcon color="var(--sub-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #empty>
          <button @click="openArtistCreateDialog('main_artists')"
                  class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
            <AddIcon color="var(--dark-green-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #option="scope">

          <div class="flex items-center  gap-2">
            <div class="w-3 h-3 rounded-full overflow-hidden">
              <img :src="scope.data.image"/>
            </div>
            <p>{{ scope.data.label }}</p>
          </div>
        </template>

      </FormElement>
      <FormElement ref="featuringArtistMultiselect" label-width="190px" v-model="form.featuring_artists"
                   :error="form.errors.featuring_artists"
                   type="multiselect" :label="__('control.song.fields.featuring_artists')"
                   :placeholder="__('control.song.fields.featuring_artists_placeholder')"
                   :config="featuringArtistSelectConfig">
        <template #first_child>
          <button @click="openArtistCreateDialog('featuring_artists')"
                  class="flex items-center gap-2 paragraph-sm c-sub-600 p-2">
            <AddIcon color="var(--sub-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #empty>
          <button @click="openArtistCreateDialog('featuring_artists')"
                  class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
            <AddIcon color="var(--dark-green-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #option="scope">
          <div class="flex items-center  gap-2">
            <div class="w-3 h-3 rounded-full overflow-hidden">
              <img :src="scope.data.image"/>
            </div>
            <p>{{ scope.data.label }}</p>
          </div>
        </template>
        <template #model="scope">
          <div class="flex items-center relative gap-2">
            <div class="flex items-center relative" :style="{'width' : scope.data.length * 20+'px'}">

              <div v-for="(artist,index) in scope.data" :style="{'left': 14*index+'px'}"
                   class="absolute w-5 h-5 rounded-full border border-white flex items-center justify-center bg-blue-300">
                <span class="label-xs"> {{ artist.label[0] }}</span>
              </div>
            </div>
            <p style="white-space:nowrap;">

              <template v-for="(artist,artistIndex) in scope.data">
                {{ artist.label }}
                <template v-if="artistIndex != scope.data.length-1">, &nbsp;</template>
              </template>
            </p>

          </div>
        </template>
      </FormElement>
      <FormElement label-width="190px" v-model="form.genre_id" :error="form.errors.genre_id"
                   :label="__('control.song.fields.genre')" :required="true"
                   :placeholder="__('control.song.fields.genre_placeholder')" type="select" :config="genreConfig">

      </FormElement>
      <FormElement label-width="190px" v-model="form.sub_genre_id" :error="form.errors.sub_genre_id"
                   :label="__('control.song.fields.sub_genre')" :required="true"
                   :placeholder="__('control.song.fields.genre_placeholder')" type="select" :config="genreConfig">
      </FormElement>

      <FormElement direction="vertical" v-model="form.is_instrumental" :error="form.errors.is_instrumental"
                   type="fancyCheck" :config="{title:__('control.song.fields.is_instrumental')}"
                   :placeholder="__('control.song.fields.is_instrumental_placeholder')">

      </FormElement>
    </div>
    <SectionHeader :title="__('control.song.dialog.header_2')"/>
    <div class="p-5 flex flex-col gap-6">

      <FormElement :required="!form.is_instrumental" v-for="(lyric_writer,i) in form.lyrics_writers"
                   :error="form.errors[`lyrics_writers.${i}`]" :disabled="form.is_instrumental" label-width="190px"
                   v-model="form.lyrics_writers[i]" :label="__('control.song.fields.lyrics_writer')"
                   :placeholder="__('control.song.fields.lyrics_writer_placeholder')"
      >
        <template #description>
          <div class="flex justify-between items-center">
            <button v-if="i == form.lyrics_writers.length-1" :disabled="form.is_instrumental"
                    @click="form.lyrics_writers.push('')"
                    class="flex items-center gap-2">
              <AddIcon color="var(--blue-500)"/>
              <span class="c-blue-500 label-xs">Yeni Ekle</span>
            </button>
            <button :disabled="form.is_instrumental" @click="form.lyrics_writers.splice(i,1)" class="mt-1 ms-auto">
              <span class="c-error-500 label-xs">Temizle</span>
            </button>
          </div>
        </template>
      </FormElement>


      <FormElement :required="!form.is_instrumental" v-for="(_,i) in form.composers"
                   :error="form.errors[`composers.${i}`]" :disabled="form.is_instrumental" label-width="190px"
                   v-model="form.composers[i]" :label="'Besteci'"
                   :placeholder="'Besteci giriniz'"
      >
        <template #description>
          <div class="flex justify-between items-center">
            <button v-if="i == form.composers.length-1" :disabled="form.is_instrumental"
                    @click="form.composers.push('')"
                    class="flex items-center gap-2">
              <AddIcon color="var(--blue-500)"/>
              <span class="c-blue-500 label-xs">Yeni Ekle</span>
            </button>
            <button :disabled="form.is_instrumental" @click="form.composers.splice(i,1)" class="ms-auto mt-1">
              <span class="c-error-500 label-xs">Temizle</span>
            </button>
          </div>
        </template>
      </FormElement>


      <FormElement :disabled="form.is_instrumental" label-width="190px" v-model="form.lyrics"
                   :label="__('control.song.fields.lyrics')" :placeholder="__('control.song.fields.lyrics_placeholder')"
                   type="textarea">
      </FormElement>

      <FormElement label-width="190px"
                   :label="__('control.song.fields.preview_start')" type="custom">
        <AppSliderInput @play="onPlaySong" :config="sliderConfig" v-model="form.preview_start">

        </AppSliderInput>
      </FormElement>
    </div>
    <SectionHeader :title="__('control.song.dialog.header_3')"/>

    <div class="p-5 flex flex-col gap-6">
      <div class="flex">
        <div style="width:134px;" class="label-sm">
          Müzisyen & <br> Katkı Sağlayan

        </div>

        <div class="flex flex-col w-full flex-1">
          <div class="flex items-start justify-center gap-3" v-for="(musician,musicanIndex) in form.musicians">

            <div class="flex-1">
              <FormElement direction="vertical" v-model="musician.name"
                           :placeholder="__('control.song.fields.musicians_placeholder')"
                           :error="form.errors[`musicians.${musicanIndex}.id`]">
                <template #description>
                  <button v-if="musicanIndex == form.musicians.length-1" @click="form.musicians.push({})"
                          class="flex items-center mt-2 gap-2">
                    <AddIcon color="var(--blue-500)"/>
                    <span class="c-blue-500 label-xs">Yeni Ekle</span>
                  </button>
                </template>
              </FormElement>

            </div>
            <div class="flex-1">
              <FormElement direction="vertical" :error="form.errors[`musicians.${musicanIndex}.role_id`]" type="custom">
                <AppSelectInput v-model="musician.role_id" :config="roleConfig"
                                :placeholder="__('control.song.fields.roles_placeholder')"></AppSelectInput>
              </FormElement>

              <button @click="form.musicians.splice(musicanIndex,1)" class="flex items-center ms-auto gap-2 mt-2">
                <span class="c-error-500 label-xs">Temizle</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <div class="flex-1">
          <FormElement :required="true" label-width="190px" v-model="form.isrc" label="ISRC"
                       placeholder="Lütfen giriniz" type="text">
          </FormElement>
        </div>
        <RegularButton class="w-min" @click="generateISRCCode">
          <template #icon>
            <ISRCStarsIcon color="var(--sub-600)"/>
          </template>
          Oluştur
        </RegularButton>
      </div>
    </div>
    <SectionHeader :title="__('control.song.dialog.header_4')"/>
    <div class="p-5 flex flex-col gap-6">
      <div class="flex items-start gap-2 " v-for="(participant,i) in form.participants">

        <FormElement class="flex-1" type="select" :error="form.errors[`participants.${i}.id`]" v-model="participant.id"
                     :config="participantSelectConfig"
                     :label="__('control.song.fields.participants')" direction="vertical"
                     :placeholder="__('control.song.fields.participants_placeholder')">

          <template #description>
            <button v-if=" i == form.participants.length-1" @click="form.participants.push({})"
                    class="flex items-center gap-2  mt-2">
              <AddIcon color="var(--blue-500)"/>
              <span class="c-blue-500 label-xs">Yeni Katılımcı Ekle</span>
            </button>
          </template>
        </FormElement>
        <FormElement class="flex-1" type="select" :error="form.errors[`participants.${i}.tasks`]"
                     v-model="participant.tasks" :config="roleConfig"
                     :label="__('control.song.fields.roles')" direction="vertical"
                     :placeholder="__('control.song.fields.roles_placeholder')"/>
        <div class="flex flex-col item-start mb-0.5">
          <FormElement direction="vertical" :error="form.errors[`participants.${i}.rate`]"
                       :label=" __('control.song.fields.share')" type="custom">

            <AppIncrementer v-model="participant.rate" :config="{isKeyboardOn:true}" class="h-9"></AppIncrementer>
            <template #description>
              <div>
                <button @click="form.participants.splice(i,1)" class="flex items-center ms-auto gap-2 mt-2">
                  <span class="c-error-500 label-xs">Temizle</span>
                </button>
              </div>
            </template>
          </FormElement>

        </div>
      </div>


    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
      <RegularButton @click="isDialogOn = false" class="flex-1">
        {{ __('control.general.cancel') }}
      </RegularButton>
      <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
        <template #icon>
          <AddIcon  color="var(--dark-green-500)"/>
        </template>
        {{ __('control.general.save') }}
      </PrimaryButton>
    </div>
  </BaseDialog>

  <SmallArtistCreateDialog @done="onArtistCreated" v-if="createArtistDialog"
                           v-model="createArtistDialog"></SmallArtistCreateDialog>

</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {SmallArtistCreateDialog} from '@/Components/Dialog';

import {AddIcon, ISRCStarsIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore';
import {Howl} from "howler";

const currentSound = ref();
const isPlaying = ref(false);
const onPlaySong = () => {

  if (props.song.path) {

    if (isPlaying.value) {
      pauseMusic(props.song);
    } else {
      playSound(props.song);
    }
  }

}

const playSound = (song) => {
  if (currentSound.value) {
    currentSound.value.pause();
    currentSound.value = null;
  }


  currentSound.value = new Howl({
    src: [song.path],
    html5: true,
    onload: (e) => {
    console.log(form.preview_start);
      currentSound.value.seek(form.preview_start[0]);
      currentSound.value.play();
    }
  });

  isPlaying.value = true;
};

const pauseMusic = (song) => {
  if (currentSound.value && currentSound.value.playing()) {
    currentSound.value.pause();

  }
  currentSound.value = null;
  isPlaying.value = false;

};
const featuringArtistMultiselect = ref();
const mainArtistSelect = ref();


import {FormElement, ArtistInput, AppIncrementer, AppSelectInput, AppSliderInput} from '@/Components/Form'
import InputError from "@/Components/InputError.vue";

const createArtistDialog = ref(false);
const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {
    default: false,
  },
  song: {
    default: null,
  },
  genres: {},
  product_id: {},
})
const isUpdating = computed(() => {
  return props.song ? true : false;
});


const generateISRCCode = async () => {
  console.log();

  const response = await crudStore.get(route('control.catalog.products.get-isrc'), {
    type: props.song.type,
    index: props.song.id,
  })
  console.log("RESPONSEEE", response);

  if (response) {
    form.isrc = response
  }

}

const whichArtistConfigToAdd = ref(null);
const choosenItunesField = ref(null);
const choosenSpotifyField = ref(null);
const adding = ref(false)
const image = ref();
const form = useForm({
  product_id: props.product_id,
  id: props.song.id,
  name: props.song.name,
  version: props.song.version,
  main_artists: null,
  featuring_artists: [],
  genre_id: props.song.genre_id,
  sub_genre_id: props.song.sub_genre_id,
  is_instrumental: props.song.is_instrumental,
  isrc: props.song.isrc,
  lyrics_writers: [],
  composers: [],
  lyrics: props.song.lyrics,
  musicians: [{}],
  participants: [{}],
  preview_start: props.song.preview_start ?? (JSON.parse(usePage().props.site_settings.song_preview_start_end_time ?? '[0,15]'))
});

const mainArtistSelectConfig = computed(() => {
  return {
    showTags: false,
    hasSearch: true,
    data: usePage().props.artists,
    remote: async (query) => {

      const response = await crudStore.get(route('control.search.artists', {
        search: query
      }))
      const formattedData = response.map(item => ({
        value: item.id,
        label: item.name,
        image: item.image ? item.image.thumb || item.image.url : null  // Use `thumb` if available, fallback to `url`
      }));


      return formattedData;

    }
  }
})

const featuringArtistSelectConfig = computed(() => {
  return {
    showTags: false,
    hasSearch: true,
    data: usePage().props.artists,
    remote: async (query) => {

      const response = await crudStore.get(route('control.search.artists', {
        search: query
      }))
      const formattedData = response.map(item => ({
        value: item.id,
        label: item.name,
        image: item.image ? item.image.thumb || item.image.url : null  // Use `thumb` if available, fallback to `url`
      }));


      return formattedData;

    }
  }
})

const participantSelectConfig = computed(() => {
  return {

    hasSearch: true,
    data: usePage().props.users,
    remote: async (query) => {
      const response = await crudStore.get(route('control.search.users', {
        search: query
      }))
      const formattedData = response.map(item => ({
        value: item.id,
        label: item.name,
        image: item.image ? item.image.thumb || item.image.url : null  // Use `thumb` if available, fallback to `url`
      }));


      return formattedData;

    }
  }
})
const totalSongDuration = ref(200)
const genreConfig = computed(() => {
  return {
    hasSearch: true,
    data: props.genres,
  }
})
const lyricsConfig = computed(() => {
  return {
    hasSearch: true,
    data: [],
  }
})


const calcMax = computed(() => {
    if(props.song.duration){
       const splitted = props.song.duration?.split(":");
       if(splitted.length > 2){
        return parseInt(splitted[0]) * 60 + parseInt(splitted[1]);
       }
    }
    return 0;
})
const sliderConfig = computed(() => {
  return {
    range: 15,
    formatter: (v) => {
      const minutes = Math.floor(v / 60);
      const remainingSeconds = v % 60;
      return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;

    },
    max:calcMax.value,
    processStyle: {
      'background': 'var(--dark-green-800)'
    }
  }
})


const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})


const countryConfig = computed(() => {
  return {
    data: usePage().props.countries,
  };
})
const onSubmit = async (e) => {


  if (form.musicians.length == 1) {
    if (form.musicians[0].name == '') form.musicians = null;
  }
  if (form.lyrics_writers.length == 1) if (form.lyrics_writers[0] == '') form.lyrics_writers = null;
  if (form.composers.length == 1) {
    if (form.composers[0] == '') form.composers = null
  }
  ;

  if (form.participants?.length == 1) {
    if (Object.values(form.participants[0]).length == 0) form.participants = null;
  }


  form.put(route('control.catalog.songs.update', props.song.id),
      {
        onFinish: () => {
          if (!form.composers) form.composers = [''];
          if (!form.musicians) form.musicians = [{name: ''}];
          if (!form.lyrics_writers) form.lyrics_writers = [''];
          if (!form.participants) form.participants = [{}];
        },
        onSuccess: async (e) => {

          toast.success(e.props.notification.message);
          isDialogOn.value = false;

          emits('done', e.props.notification.song)

        },
        onError: (e) => {
          toast.error(Object.values(e)[0])
        }
      });

}

const checkIfDisabled = computed(() => {
  return false;
})

const onArtistCreated = (e) => {
  let row = {
    label: e.name,
    value: e.id,

  };


  if (whichArtistConfigToAdd.value == 'featuring_artists') {
    featuringArtistMultiselect.value.appMultiSelect.insertData(row);
    featuringArtistSelectConfig.value.data.push(row);
  } else {
    mainArtistSelect.value.appSelect.insertData(row);
    mainArtistSelectConfig.value.data.push(row);
  }

}
const roleConfig = computed(() => {
  return {
    data: usePage().props.artistBranches,
  }
})
onMounted(() => {
  if (props.song) {

    form.featuring_artists = (props.song.featuring_artists ?? []).map((e) => e.id);


    form.main_artists = props.song.main_artists?.length > 0 ? props.song.main_artists[0].id : null;


    form.musicians = (props.song.musicians ?? []).map((e) => {
      return {name: e.name, role_id: e.role_id}
    }) ?? [{}];

    form.participants = (props.song.participants ?? []).map((e) => {
      return {id: e.user_id, tasks: e.tasks, rate: e.rate}
    }) ?? [{}];


    props.song?.writers?.forEach(element => {
      if (element.name) {
        form.lyrics_writers.push(element.name)
      }
    });
    props.song?.composers?.forEach(element => {
      if (element.name) {
        form.composers.push(element.name)
      }
    });
    if (form.composers.length == 0)
      form.composers = [''];
    if (form.lyrics_writers.length == 0)
      form.lyrics_writers = [''];
    if (form.musicians.length == 0)
      form.musicians = [{name: ''}];
    if (form.participants.length == 0)
      form.participants = [{}]
  }
});
const openArtistCreateDialog = (whichArtistConfigToAdString) => {
  whichArtistConfigToAdd.value = whichArtistConfigToAdString;
  createArtistDialog.value = true;
}

</script>

<style scoped>

</style>
