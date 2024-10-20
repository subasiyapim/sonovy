<template>
  <BaseDialog v-model="isDialogOn" align="right" :title="artist ? 'Sanatçı Düzenle' : 'Sanatçı Ekle'"
              description="Temel bilgileri girerek sanatçı oluşturabilirsiniz. ">
    <template #icon>
      <AddIcon color="var(--dark-green-950)"/>
    </template>

    <SectionHeader title="SANATÇI HAKKINDA"/>

    <div class="p-5 flex flex-col gap-6">
      <FormElement label-width="190px" :required="true" :error="form.errors.image" v-model="image" label="Fotoğraf"
                   type="upload" :config="{label:'Fotoğraf Yükle',note:'Min 400x400px, PNG or JPEG'}"></FormElement>

      <FormElement label-width="190px" :required="true" :error="form.errors.name" label="Ad Soyad" type="custom">
        <ArtistInput @onPlatformsChoosen="onPlatformsChoosen" v-model="form.name"
                     placeholder="Lütfen giriniz"></ArtistInput>
      </FormElement>
      <FormElement label-width="190px" :required="true" :error="form.errors.about" :config="{letter:500}"
                   v-model="form.about" label="Sanatçı Hakkında" type="textarea"
                   placeholder="Sanatçı Hakkında"></FormElement>
      <FormElement label-width="190px" :required="true" :error="form.errors.artist_branches"
                   v-model="form.artist_branches" :config="artistBranchesMultiSelect" label="Sanat Dalları"
                   type="multiselect" placeholder="Lütfen giriniz"></FormElement>
      <FormElement label-width="190px" :required="true" :error="form.errors.country_id" v-model="form.country_id"
                   label="Ülke" :config="countryConfig" placeholder="Seçiniz" type="select">
        <template #option="scope">
          <span>{{ scope.data.iconKey }}</span>
          <span class="paragraph-sm c-strong-950">
                         {{ scope.data.label }}
                    </span>
        </template>
        <template #model="scope">

          <div v-if="scope.data" class="flex items-center gap-2">

            <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.iconKey }}</span>
            <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.label }}</span>
          </div>
        </template>
      </FormElement>
      <FormElement label-width="190px" :error="form.errors.ipi_code" v-model="form.ipi_code" label="IPI"
                   placeholder="Lütfen giriniz"></FormElement>
      <FormElement label-width="190px" :error="form.errors.isni_code" v-model="form.isni_code" label="ISNI"
                   placeholder="Lütfen giriniz"></FormElement>
    </div>
    <SectionHeader title="İLETİŞİM BİLGİLERİ"/>
    <div class="p-5 flex flex-col gap-6">
      <FormElement label-width="190px" v-model="form.phone" label="Telefon Numarası" type="phone"
                   placeholder="(555) 000-0000"></FormElement>
      <FormElement label-width="190px" v-model="form.website" label="Websitesi" placeholder="www.example.com"
                   type="web"></FormElement>
    </div>
    <SectionHeader title="PLATFORMLAR"/>
    <div class="p-5 flex flex-col">
      <div v-for="platform in form.platforms" class="flex gap-4">
        <FormElement class="flex-1" direction="vertical" v-model="platform.value" label-width="190px" label="Platform"
                     type="select" :config="{data:usePage().props.platforms}" placeholder="Platform Seç">
          <template #option="scope">
            <!-- <span>{{scope.data.iconKey}}</span> -->
            <span class="paragraph-sm c-strong-950">
                            {{ scope.data.label }}
                        </span>
          </template>
          <template #model="scope">
            <div v-if="scope.data" class="flex items-center gap-2">
              <!-- <span>{{countryConfig.data.find((el) => el.value == scope.data)?.iconKey}}</span> -->
              <span>{{ usePage().props.platforms.find((el) => el.value == scope.data)?.label }}</span>
            </div>
          </template>
        </FormElement>
        <FormElement class="flex-1" direction="vertical" v-model="platform.url" label-width="190px"
                     label="Platform Link" placeholder="lütfen giriniz"></FormElement>
      </div>
      <button @click="form.platforms.push({})" class="flex items-center gap-2">
        <AddIcon color="var(--blue-500)"/>
        <p class="label-xs c-blue-500">Platform Ekle</p>
      </button>
    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4">
      <RegularButton @click="isDialogOn = false" class="flex-1">İptal</RegularButton>
      <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
        <template #icon>
          <AddIcon/>
        </template>
        Kaydet
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

import {FormElement, ArtistInput} from '@/Components/Form'

const props = defineProps({
  modelValue: {
    default: false,
  },
  artist: {
    default: null,
  }
})
const isUpdating = ref(props.artist ? true : false);
const adding = ref(false)
const image = ref();
const form = useForm({
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

const onPlatformsChoosen = (e) => {
  console.log(usePage().props.platforms);

  const finded = usePage().props.platforms.find((el) => el.label == e.platform)
  finded.url = e.url;
  const findedIndex = form.platforms.findIndex((el) => el.value == finded.value);
  if (findedIndex < 0)
    form.platforms.push(finded);
  else form.platforms[findedIndex] = finded;

}
const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const platforms = ref([{}]);

const artistBranchesMultiSelect = computed(() => {
  return {
    data: usePage().props.artistBranches,
  };
})

const countryConfig = computed(() => {
  return {
    data: usePage().props.countries,
  };
})
const onSubmit = (e) => {
  adding.value = true;
  if (isUpdating.value) {
    form
        .transform((data) => ({
          ...data,
          _method: 'PUT'
        }))
        .post(route('control.catalog.artists.update', props.artist.id), {
          preserveScroll: true,
          onSuccess: (e) => {

          },
          onError: (e) => {
            console.log("HATAAA", e);
          }
        });
    return;
  }

  if (image.value) {
    form.image = image.value?.file;
  }
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

const checkIfDisabled = computed(() => {

  return form['image'] && form['name'] && form['about'] && form['contry_id'] && (form['artist_branches'].length > 0)

})
onMounted(() => {
  if (props.artist) {

    form['name'] = props.artist['name']

    form['about'] = props.artist['about']
    form['phone'] = props.artist['phone']
    form['website'] = props.artist['website']
    form['ipi_code'] = props.artist['ipi_code']
    form['isni_code'] = props.artist['isni_code']
    form['artist_branches'] = props.artist['artist_branches'].map((e) => e.id);
    form['platforms'] = props.artist['platforms']
    form['platforms'].map((e) => {
      e.value = e.id;
    })
    form['country_id'] = props.artist?.country?.id;

  }
});
</script>

<style lang="scss" scoped>

</style>
