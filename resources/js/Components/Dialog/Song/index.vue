<template>
  <BaseDialog v-model="isDialogOn" align="right" :title="song.name"
              :description="(song.size / (1024*1024)).toFixed(2)">
    <template #icon>
      <img width="24" height="24" src="@/assets/images/mp3_active.png" />
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

        <FormElement label-width="190px" v-model="form.main_artists"  :error="form.errors.main_artists" type="select"  :label="__('control.song.fields.main_artists')" :placeholder="__('control.song.fields.main_artists_placeholder')" :config="artistSelectConfig">

                <template #option="scope">
                    <div class="flex items-center  gap-2">
                            <div class="w-3 h-3 rounded-full overflow-hidden">
                                <img :src="scope.data.image" />
                            </div>
                            <p>{{scope.data.label}}</p>
                    </div>
                </template>

        </FormElement>
        <FormElement label-width="190px" v-model="form.featuring_artists"  :error="form.errors.featuring_artists" type="multiselect" :label="__('control.song.fields.featuring_artists')" :placeholder="__('control.song.fields.featuring_artists_placeholder')"  :config="artistSelectConfig">
                <template #first_child>
                    <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                </template>
                <template #empty>
                    <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                </template>
                <template #option="scope">
                <div class="flex items-center  gap-2">
                        <div class="w-3 h-3 rounded-full overflow-hidden">
                            <img :src="scope.data.image" />
                        </div>
                        <p>{{scope.data.label}}</p>
                </div>
                </template>
                <template #model="scope">
                    <div class="flex items-center relative gap-2">
                            <div  class="flex items-center relative" :style="{'width' : scope.data.length * 20+'px'}">
                                <div v-for="(artist,index) in scope.data" :style="{'left': 14*index+'px'}" class="absolute w-5 h-5 rounded-full border border-white flex items-center justify-center bg-blue-300">
                                <span class="label-xs"> {{artist.label[0]}}</span>
                                </div>
                            </div>
                        <p style="white-space:nowrap;">
                                <template v-for="artist in scope.data">
                                    {{artist.label}}, &nbsp;
                                </template>
                        </p>

                    </div>
                </template>
        </FormElement>
        <FormElement label-width="190px" v-model="form.genre_id"  :error="form.errors.genre_id" :label="__('control.song.fields.genre')" :required="true"  :placeholder="__('control.song.fields.genre_placeholder')" type="select" :config="genreConfig">

        </FormElement>
        <FormElement label-width="190px" v-model="form.sub_genre_id"  :error="form.errors.sub_genre_id" :label="__('control.song.fields.sub_genre')" :required="true" :placeholder="__('control.song.fields.genre_placeholder')"  type="select" :config="genreConfig">
        </FormElement>

        <FormElement direction="vertical" v-model="form.is_instrumental" :error="form.errors.is_instrumental" type="fancyCheck" :config="{title:__('control.song.fields.is_instrumental')}" :placeholder="__('control.song.fields.is_instrumental_placeholder')">

        </FormElement>
    </div>
    <SectionHeader :title="__('control.song.dialog.header_2')"/>
        <div class="p-5 flex flex-col gap-6">
            <FormElement v-for="(lyric_writer,i) in form.lyrics_writer" :disabled="form.is_instrumental" label-width="190px" v-model="form.lyrics_writer[i]" :label="__('control.song.fields.lyrics_writer')" :placeholder="__('control.song.fields.lyrics_writer_placeholder')"  type="select" :config="lyricsConfig">
                <template #description>
                    <div class="flex justify-end items-center">
                        <button :disabled="form.is_instrumental" @click="form.lyrics_writer.splice(i,1)" class="mt-1">
                            <span class="c-error-500 label-xs">Temizle</span>
                        </button>
                    </div>
                </template>
            </FormElement>

             <div class="flex">
                <div style="width:144px;"></div>
                <div class="text-start flex-1">
                    <button :disabled="form.is_instrumental" @click="form.lyrics_writer.push({})" class="flex items-center gap-2">
                        <AddIcon color="var(--blue-500)" />
                        <span class="c-blue-500 label-xs">Yeni Ekle</span>
                    </button>
                </div>
             </div>
            <FormElement :disabled="form.is_instrumental" label-width="190px" v-model="form.lyrics" :label="__('control.song.fields.lyrics')" :placeholder="__('control.song.fields.lyrics_placeholder')"  type="textarea">
            </FormElement>
            <FormElement label-width="190px" :config="sliderConfig" v-model="form.preview_time" :label="__('control.song.fields.preview_time')"   type="slider">
            </FormElement>
        </div>
    <SectionHeader :title="__('control.song.dialog.header_3')"/>
        <div class="p-5 flex flex-col gap-6">
            <div class="flex items-center justify-center gap-3" v-for="musican in form.musicans">
                <div style="width:134px;" class="label-sm">
                    Müzisyen & <br> Katkı Sağlayan

                </div>
                <div class="flex-1">
                    <AppSelectInput v-model="musican.id" :config="musicansSelectConfig" :placeholder="__('control.song.fields.musicans_placeholder')"></AppSelectInput>
                </div>
                <div class="flex-1">
                    <AppSelectInput v-model="musican.role" :placeholder="__('control.song.fields.roles_placeholder')"></AppSelectInput>
                </div>
            </div>
            <div class="flex">
                <div style="width:150px;"></div>
                <div class="flex-1">
                    <button @click="form.musicans.push({})" class="flex items-center gap-2">
                        <AddIcon color="var(--blue-500)" />
                        <span class="c-blue-500 label-xs">Yeni Ekle</span>
                    </button>
                </div>

            </div>
            <div>
                <FormElement label-width="190px" v-model="form.isrc" label="ISRC" placeholder="Lütfen giriniz"  type="text" >
                </FormElement>
            </div>
        </div>

    <SectionHeader :title="__('control.song.dialog.header_4')"/>
        <div class="p-5 flex flex-col gap-6">
                <div class="flex items-center gap-2 " v-for="(participant,i) in form.participants">
                    <FormElement class="flex-1" type="select" v-model="participant.id" :config="participantSelectConfig" :label="__('control.song.fields.participants')"  direction="vertical" :required="true" :placeholder="__('control.song.fields.participants_placeholder')" />
                    <FormElement class="flex-1" v-model="participant.role" :label="__('control.song.fields.roles')"  direction="vertical" :required="true" :placeholder="__('control.song.fields.roles_placeholder')" />
                    <div class="flex flex-col item-start mb-0.5">
                        <label class="label-sm c-strong-950">{{__('control.song.fields.share')}}</label>
                        <AppIncrementer v-model="participant.share" class="h-9"></AppIncrementer>
                    </div>
                </div>

                <div>
                    <button @click="form.participants.push({})" class="flex items-center gap-2">
                        <AddIcon color="var(--blue-500)" />
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
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore';


import {FormElement, ArtistInput,AppIncrementer,AppSelectInput} from '@/Components/Form'

const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {
    default: false,
  },
  song: {
    default: null,
  },
  genres:{},
})
const isUpdating = computed(() => {
  return props.song ? true : false;
});

const choosenItunesField = ref(null);
const choosenSpotifyField = ref(null);
const adding = ref(false)
const image = ref();
const form = useForm({
    id: props.song.id,
    name: props.song.name,
    version:props.song.version,
    main_artists:props.song.main_artists,
    featuring_artists:props.song.main_artists,
    genre_id:props.song.genre_id,
    sub_genre_id:props.song.sub_genre_id,
    is_instrumental:props.song.is_instrumental,
    isrc:props.song.isrc,
    lyrics_writer:[null],
    musicans:[{}],
    participants:[{}],
    preview_time:[0,20]
});

const artistSelectConfig = computed(() => {
    return {
        showTags:false,
        hasSearch:true,
        data: [],
        remote:async (query) => {

            const  response = await crudStore.get(route('control.search.artists',{
                search:query
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

        hasSearch:true,
        data: [],
        remote:async (query) => {
            const  response = await crudStore.get(route('control.search.users',{
                search:query
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

        hasSearch:true,
        data: [],
        remote:async (query) => {
            const  response = await crudStore.get(route('control.search.users',{
                search:query
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
        hasSearch:true,
        data: props.genres,
    }
})
const lyricsConfig = computed(() => {
    return {
        hasSearch:true,
        data: [],
    }
})
const sliderConfig = computed(() => {
    return {
        formatter:(v) => {
            const minutes = Math.floor(v / 60);
            const remainingSeconds = v % 60;
            return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')} sn`;

        },
        processStyle:{
            'background' : 'var(--dark-green-800)'
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
    form.put(route('control.catalog.songs.update',props.song.id),
    {
        onFinish: () => {

      },
      onSuccess: async (e) => {


        toast.success(e.props.notification.message);
        isDialogOn.value = false;
        emits('done',form)

      },
      onError: (e) => {
        console.log("HATAAAA", e);
      }
    });

}

const checkIfDisabled = computed(() => {
 return false;
})
onMounted(() => {
  if (props.song) {
    // form['id'] = props.song['id']
    // form['name'] = props.song['name']
    // form['about'] = props.song['about']
    // form['phone'] = props.song['phone']
    // form['website'] = props.song['website']
    // form['ipi_code'] = props.song['ipi_code']
    // form['isni_code'] = props.song['isni_code']
    // form['artist_branches'] = props.song['artist_branches'].map((e) => e.id);
    // form['platforms'] = props.song['platforms']
    // form['platforms'].map((e) => {
    //   e.value = e.id;
    //   if (e.id == 4) {
    //     choosenItunesField.value = e.url;
    //   }
    //   if (e.id == 2) {
    //     choosenSpotifyField.value = e.url;
    //   }


    // })
    // form['country_id'] = props.song?.country?.id;

  }
});
</script>

<style scoped>

</style>
