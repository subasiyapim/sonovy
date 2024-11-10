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
                   :required="true"
                   :error="form.errors.version"
                   :config="{letter:500}"
                   v-model="form.version"
                   :label="__('control.song.fields.version')"
                   type="text"
                   :placeholder="__('control.song.fields.version_placeholder')"/>

      <FormElement label-width="190px" v-model="form.main_artists" :error="form.errors.main_artists" type="select"
                   :label="__('control.song.fields.main_artists')"
                   :placeholder="__('control.song.fields.main_artists_placeholder')" :config="artistSelectConfig">

        <template #option="scope">
          <div class="flex items-center  gap-2">
            <div class="w-3 h-3 rounded-full overflow-hidden">
              <img :src="scope.data.image"/>
            </div>
            <p>{{ scope.data.label }}</p>
          </div>
        </template>

      </FormElement>
      <FormElement label-width="190px" v-model="form.featuring_artists" :error="form.errors.featuring_artists"
                   type="multiselect" :label="__('control.song.fields.featuring_artists')"
                   :placeholder="__('control.song.fields.featuring_artists_placeholder')" :config="artistSelectConfig">
        <template #first_child>
          <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2">
            <AddIcon color="var(--sub-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #empty>
          <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
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
              <template v-for="artist in scope.data">
                {{ artist.label }}, &nbsp;
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
      <FormElement v-for="(lyric_writer,i) in form.lyrics_writers" :disabled="form.is_instrumental" label-width="190px"
                   v-model="form.lyrics_writers[i]" :label="__('control.song.fields.lyrics_writer')"
                   :placeholder="__('control.song.fields.lyrics_writer_placeholder')" type="select"
                   :config="lyricsWriterConfig">
        <template #description>
          <div class="flex justify-end items-center">
            <button :disabled="form.is_instrumental" @click="form.lyrics_writers.splice(i,1)" class="mt-1">
              <span class="c-error-500 label-xs">Temizle</span>
            </button>
          </div>
        </template>
      </FormElement>

      <div class="flex">
        <div style="width:144px;"></div>
        <div class="text-start flex-1">
          <button :disabled="form.is_instrumental" @click="form.lyrics_writers.push({})"
                  class="flex items-center gap-2">
            <AddIcon color="var(--blue-500)"/>
            <span class="c-blue-500 label-xs">Yeni Ekle</span>
          </button>
        </div>
      </div>
      <FormElement :disabled="form.is_instrumental" label-width="190px" v-model="form.lyrics"
                   :label="__('control.song.fields.lyrics')" :placeholder="__('control.song.fields.lyrics_placeholder')"
                   type="textarea">
      </FormElement>
      <FormElement label-width="190px" :config="sliderConfig" v-model="form.preview_start"
                   :label="__('control.song.fields.preview_start')" type="slider">
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
              <AppSelectInput v-model="musician.id" :config="musicansSelectConfig"
                              :placeholder="__('control.song.fields.musicians_placeholder')"></AppSelectInput>
            </div>
            <div class="flex-1">
              <AppSelectInput v-model="musician.role" :config="roleConfig"
                              :placeholder="__('control.song.fields.roles_placeholder')"></AppSelectInput>
              <button @click="form.musicians.splice(musicanIndex,1)" class="flex items-center ms-auto gap-2 mt-2">

                <span class="c-error-500 label-xs">Temizle</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="flex">
        <div style="width:150px;"></div>
        <div class="flex-1">
          <button @click="form.musicians.push({})" class="flex items-center gap-2">
            <AddIcon color="var(--blue-500)"/>
            <span class="c-blue-500 label-xs">Yeni Ekle</span>
          </button>
        </div>

      </div>
      <div>
        <FormElement label-width="190px" v-model="form.isrc" label="ISRC" placeholder="Lütfen giriniz" type="text">
        </FormElement>
      </div>
    </div>

    <SectionHeader :title="__('control.song.dialog.header_4')"/>
    <div class="p-5 flex flex-col gap-6">
      <div class="flex items-start gap-2 " v-for="(participant,i) in form.participants">
        <FormElement class="flex-1" type="select" v-model="participant.id" :config="participantSelectConfig"
                     :label="__('control.song.fields.participants')" direction="vertical" :required="true"
                     :placeholder="__('control.song.fields.participants_placeholder')"/>
        <FormElement class="flex-1" type="multiselect" v-model="participant.tasks" :config="roleConfig"
                     :label="__('control.song.fields.roles')" direction="vertical" :required="true"
                     :placeholder="__('control.song.fields.roles_placeholder')"/>
        <div class="flex flex-col item-start mb-0.5">
          <label class="label-sm c-strong-950">{{ __('control.song.fields.share') }}</label>
          <AppIncrementer v-model="participant.rate" class="h-9"></AppIncrementer>
          <button @click="form.participants.splice(i,1)" class="flex items-center ms-auto gap-2 mt-2">

            <span class="c-error-500 label-xs">Temizle</span>
          </button>
        </div>
      </div>


      <div>
        <button @click="form.participants.push({})" class="flex items-center gap-2">
          <AddIcon color="var(--blue-500)"/>
          <span class="c-blue-500 label-xs">Yeni Katılımcı Ekle</span>
        </button>
      </div>

    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4">
      <RegularButton @click="isDialogOn = false" class="flex-1">
        {{ __('control.general.cancel') }}
      </RegularButton>
      <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
        <template #icon>
          <AddIcon/>
        </template>
        {{ __('control.general.save') }}
      </PrimaryButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {AddIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onBeforeMount} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore';


import {FormElement, ArtistInput, AppIncrementer, AppSelectInput} from '@/Components/Form'

const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {
    default: false,
  },
  song: {
    default: null,
  },
  genres: {},
  product_id:{},
})
const isUpdating = computed(() => {
  return props.song ? true : false;
});

const choosenItunesField = ref(null);
const choosenSpotifyField = ref(null);
const adding = ref(false)
const image = ref();
const form = useForm({
    product_id:props.product_id,
    id: props.song.id,
    name: props.song.name,
    version: props.song.version,
    main_artists: null,
    featuring_artists:[],
    genre_id: props.song.genre_id,
    sub_genre_id: props.song.sub_genre_id,
    is_instrumental: props.song.is_instrumental,
    isrc: props.song.isrc,
    lyrics_writers: [null],
    lyrics: props.song.lyrics,
    musicians: [{}],
    participants: [{}],
    preview_start: props.song.preview_start ?? [0,15]
});

const artistSelectConfig = computed(() => {
  return {
    showTags: false,
    hasSearch: true,
    data: [...(props.song?.featuring_artists ?? [])?.map((element) => {
       return {
               label: element.name,
               value:element.id,
               image:element.media[0]?.original_url,
            };
    }),...(props.song?.main_artists ?? []).map((element) => {
       return {
               label: element.name,
               value:element.id,
               image:element.media[0]?.original_url,
            };
    })],
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

const musicansSelectConfig = computed(() => {
  return {

    hasSearch: true,
    data: (props.song?.musicians ?? [])?.map((element) => {
       return {
                value:element.id,
                label: element.name,
            };
    }),
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
const lyricsWriterConfig = computed(() => {
  return {

    hasSearch: true,
    data: (props.song?.lyrics_writers ?? [])?.map((element) => {
       return {
                value:element.id,
               label: element.name,
            };
    }),
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

const participantSelectConfig = computed(() => {
  return {

    hasSearch: true,
      data: (props.song?.participants ?? [])?.map((element) => {
       return {
                value:element.user_id,
                label: element.user?.name,
            };
    }),
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
const sliderConfig = computed(() => {
  return {
    range: 15,
    formatter: (v) => {
      const minutes = Math.floor(v / 60);
      const remainingSeconds = v % 60;
      return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')} sn`;

    },
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
  form.put(route('control.catalog.songs.update', props.song.id),
      {
        onFinish: () => {

        },
        onSuccess: async (e) => {
            console.log("EEE",);

          toast.success(e.props.notification.message);
          isDialogOn.value = false;
          emits('done', e.props.notification.song)

        },
        onError: (e) => {
          console.log("HATAAAA", e);
        }
      });

}

const checkIfDisabled = computed(() => {
  return false;
})


const roleConfig = computed(() => {
  return {
    data: usePage().props.artistBranches,
  }
})
onBeforeMount(() => {
    if (props.song) {
        form.featuring_artists  = (props.song.featuring_artists??[]).map((e) => e.id);


       form.main_artists  = props.song.main_artists?.length> 0 ? props.song.main_artists[0].id : null;


        form.musicians = (props.song.musicians ?? []).map((e) => {return{id:e.id,role:e.pivot?.branch_id}}) ?? [{}];
        form.participants = (props.song.participants ?? []).map((e) => {return {id:e.user_id,tasks:e.tasks,rate:e.rate}}) ?? [{}];
        form.lyrics_writers =  (props.song.lyrics_writers ?? []).map((e) => e.id) ?? [1];


        if(form.lyrics_writers.length == 0){
                form.lyrics_writers = [null];
        }
        if(form.participants.length == 0){
            form.participants = [{}]
        }
        if(form.musicians.length == 0){
            form.musicians = [{}]
        }

    }
});
</script>

<style scoped>

</style>
