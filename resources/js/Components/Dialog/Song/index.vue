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
                   :error="form.errors.about"
                   :config="{letter:500}"
                   v-model="form.about"
                   :label="__('control.song.fields.name')"
                   type="text"
                   :placeholder="__('control.song.fields.name_placeholder')"/>
        <FormElement label-width="190px"
                :required="true"
                :error="form.errors.about"
                :config="{letter:500}"
                v-model="form.about"
                :label="__('control.song.fields.version')"
                type="text"
                :placeholder="__('control.song.fields.version_placeholder')"/>

        <FormElement label-width="190px" v-model="form.main_artists" type="multiselect"  :label="__('control.song.fields.main_artists')" :placeholder="__('control.song.fields.main_artists_placeholder')" :config="artistSelectConfig">
                <template #first_child>
                    <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                </template>
                <template #empty>
                    <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                </template>
                    <template #description>
                    <label class="flex items-center gap-2 mt-2 px-1" for="isProductDerleme">
                    <input type="checkbox" id="isProductDerleme" class="focus:ring-0 rounded"></input>
                    <span class="paragraph-xs c-strong-950">Derleme Albüm</span>
                    <span class="paragraph-xs c-sub-600">(birden fazla sanatçı varsa seçin)</span>
                    </label>
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
        <FormElement label-width="190px" v-model="form.featuring_artists" type="multiselect" :label="__('control.song.fields.featuring_artists')" :placeholder="__('control.song.fields.featuring_artists_placeholder')"  :config="artistSelectConfig">
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
        <FormElement label-width="190px" v-model="form.genre_id" :label="__('control.song.fields.genre')"   :placeholder="__('control.song.fields.genre_placeholder')"  :config="genreConfig">

        </FormElement>
        <FormElement label-width="190px" v-model="form.sub_genre_id" :label="__('control.song.fields.sub_genre')" :placeholder="__('control.song.fields.genre_placeholder')"   :config="genreConfig">
        </FormElement>

        <FormElement direction="vertical" type="fancyCheck" :config="{title:__('control.song.fields.is_instrumental')}" :placeholder="__('control.song.fields.is_instrumental_placeholder')">

        </FormElement>
    </div>
    <SectionHeader :title="__('control.song.dialog.header_2')"/>
        <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px" v-model="form.lyrics_writer" :label="__('control.song.fields.lyrics_writer')" :placeholder="__('control.song.fields.lyrics_writer_placeholder')"  type="select" :config="lyricsConfig">

            </FormElement>

             <div class="flex">
                <div style="width:144px;"></div>
                <div class="text-start flex-1">
                    <button class="flex items-center gap-2">
                        <AddIcon color="var(--blue-500)" />
                        <span class="c-blue-500 label-xs">Yeni Ekle</span>
                    </button>
                </div>
             </div>
            <FormElement label-width="190px" v-model="form.lyrics" :label="__('control.song.fields.lyrics')" :placeholder="__('control.song.fields.lyrics_placeholder')"  type="textarea">
            </FormElement>

        </div>
    <SectionHeader :title="__('control.song.dialog.header_3')"/>
        <div class="p-5 flex flex-col gap-6">
            <div class="flex items-center justify-center gap-3">
                <div style="width:134px;" class="label-sm">
                    Müzisyen & <br> Katkı Sağlayan

                </div>
                <div class="flex-1">
                    <AppSelectInput :placeholder="__('control.song.fields.musicans_placeholder')"></AppSelectInput>
                </div>
                <div class="flex-1">
                    <AppSelectInput :placeholder="__('control.song.fields.roles_placeholder')"></AppSelectInput>
                </div>
            </div>
            <div class="flex">
                <div style="width:150px;"></div>
                <div class="flex-1">
                    <button class="flex items-center gap-2">
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
                <div class="flex items-center gap-2 ">
                    <FormElement class="flex-1" :label="__('control.song.fields.participants')"  direction="vertical" :required="true" :placeholder="__('control.song.fields.participants_placeholder')" />
                    <FormElement class="flex-1" :label="__('control.song.fields.roles')"  direction="vertical" :required="true" :placeholder="__('control.song.fields.roles_placeholder')" />
                    <div class="flex flex-col item-start mb-0.5">
                        <label class="label-sm c-strong-950">{{__('control.song.fields.share')}}</label>
                        <AppIncrementer class="h-9"></AppIncrementer>
                    </div>
                </div>

                <div>
                    <button class="flex items-center gap-2">
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

import {FormElement, ArtistInput,AppIncrementer,AppSelectInput} from '@/Components/Form'

const props = defineProps({
  modelValue: {
    default: false,
  },
  song: {
    default: null,
  }
})
const isUpdating = computed(() => {
  return props.song ? true : false;
});

const choosenItunesField = ref(null);
const choosenSpotifyField = ref(null);
const adding = ref(false)
const image = ref();
const form = useForm({
  id: "",
  name: '',
  country_id: "",
  about: "",
  artist_branches: [],
  image: "",
  phone: "",
  website: "",
  ipi_code: "",
  isni_code: "",
  platforms: []
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
const onSubmit = (e) => {
  adding.value = true;
  if (image.value) {
    form.image = image.value?.file;
  }
  if (isUpdating.value) {
    form
        .transform((data) => ({
          ...data,
          _method: 'PUT',

        }))
        .post(route('control.catalog.artists.update', form.id), {
          preserveScroll: true,
          onSuccess: (e) => {
            toast.success(e.props.notification.message);
            emits('done', e.props.notification.data)
            isDialogOn.value = false;
          },
          onError: (e) => {
            console.log("HATAAA", e);
          }
        });
  } else {

    form.post(route('control.catalog.artists.store'), {
      onFinish: () => {
        adding.value = false;
      },
      onSuccess: async (e) => {
        toast.success(e.props.notification.message);
        emits('done', e.props.notification.data)
        isDialogOn.value = false;
      },
      onError: (e) => {
        console.log("HATAAAA", e);
      },
    });
  }


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
