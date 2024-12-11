<template>
  <BaseDialog v-model="isDialogOn" height="min-content" align="center"
              :title="artist ? __('control.artist.edit') : __('control.artist.add')"
              :description="__('control.artist.dialog.description')">
    <template #icon>
      <AddIcon color="var(--dark-green-950)"/>
    </template>

    <SectionHeader :title="__('control.artist.dialog.header_1')"/>

    <div class="p-5 flex flex-col gap-6">

      <FormElement label-width="190px"
                   :required="true"
                   :error="form.errors.name"

                   :label="__('control.artist.fields.name')"
                   type="custom">
        <ArtistInput @onPlatformsChoosen="onPlatformsChoosen"
                     :choosenItunesField="choosenItunesField"
                     :choosenSpotifyField="choosenSpotifyField"
                     v-model="form.name"
                     :placeholder="__('control.artist.fields.name_placeholder')"/>
      </FormElement>


      <FormElement label-width="190px"
                   :error="form.errors.country_id"
                   v-model="form.country_id"
                   :label="__('control.artist.fields.country')"
                   :config="countryConfig"
                   :placeholder="__('control.artist.fields.country_placeholder')"
                   type="select">

        <template #option="scope">
          <span>{{ scope.data.iconKey }}</span>
          <span class="paragraph-sm c-strong-950">{{ scope.data.label }}</span>
        </template>
        <template #model="scope">
          <div v-if="scope.data" class="flex items-center gap-2">
            <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.iconKey }}</span>
            <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.label }}</span>
          </div>
        </template>
      </FormElement>

      <FormElement label-width="190px"
                   :error="form.errors.artist_branches"
                   v-model="form.artist_branches"
                   :config="artistBranchesMultiSelect"
                   :label="__('control.artist.fields.artist_branches')"
                   type="multiselect"
                   :placeholder="__('control.artist.fields.artist_branches_placeholder')"/>

      <FormElement label-width="190px"
                   :error="form.errors.ipi_code"
                   v-model="form.ipi_code"
                   :label="__('control.artist.fields.ipi_code')"
                   :placeholder="__('control.artist.fields.ipi_code_placeholder')"/>

      <FormElement label-width="190px" :error="form.errors.isni_code"
                   v-model="form.isni_code"
                   :label="__('control.artist.fields.isni_code')"
                   :placeholder="__('control.artist.fields.isni_code_placeholder')"/>
    </div>

    <SectionHeader :title="__('control.artist.dialog.header_3')"/>
    <div class="p-5 flex flex-col">
      <div v-for="(platform,platformIndex) in form.platforms" class="flex gap-4">
        <FormElement class="flex-1"
                     direction="vertical"
                     v-model="platform.value"
                     label-width="190px"
                     :label="__('control.artist.fields.platform_id')"
                     type="select"
                     :config="{data:usePage().props.platforms}"
                     :placeholder="__('control.artist.fields.platform_id_placeholder')">
          <template #option="scope">
            <span class="paragraph-sm c-strong-950">
              {{ scope.data.label }}
            </span>
          </template>
          <template #model="scope">
            <div v-if="scope.data" class="flex items-center gap-2">
              <span>{{ usePage().props.platforms.find((el) => el.value == scope.data)?.label }}</span>
            </div>
          </template>
        </FormElement>
        <FormElement class="flex-1" direction="vertical" v-model="platform.url" label-width="190px"
                     :label="__('control.artist.fields.platform_link')"
                     :placeholder="__('control.artist.fields.platform_link_placeholder')"/>
        <button @click="form.platforms.splice(platformIndex,1)" class="mt-2 c-error-500 label-sm">
          sil
        </button>
      </div>
      <button @click="form.platforms.push({})" class="flex items-center gap-2">
        <AddIcon color="var(--blue-500)"/>
        <p class="label-xs c-blue-500">
          {{ __('control.artist.fields.platform_link_button') }}
        </p>
      </button>
    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4">
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
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {AddIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore'
import {FormElement, ArtistInput} from '@/Components/Form'
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  modelValue: {
    default: false,
  },
  artist: {
    default: null,
  }
})
const isUpdating = computed(() => {
  return props.artist ? true : false;
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
const crudStore = useCrudStore();
const onPlatformsChoosen = (e) => {
  console.log(usePage().props.platforms);

  const finded = usePage().props.platforms?.find((el) => el.label == e.platform)
  finded.url = e.url;
  const findedIndex = form.platforms.findIndex((el) => el.value == finded.value);
  if (findedIndex < 0)
    form.platforms.push(finded);
  else form.platforms[findedIndex] = finded;

}
const emits = defineEmits(['update:modelValue', 'done', 'update']);
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
const onSubmit = async (e) => {
  console.log("SUBMİTTEYİZZZ");


  adding.value = true;
  if (image.value) {
    form.image = image.value?.file;
  }
  try {
    const response = await crudStore.post(route('control.catalog.artists.store'), form);


    if (response.type == 'success') {
      toast.success(response.message);
      emits('done', response.data)
      isDialogOn.value = false;
    }
  } catch (error) {
    console.log("ERERR", error);
    if (error.response) {
      form.errors = error.response?.data?.errors;

    }
  }


  // form.post(route('control.catalog.artists.store'), {
  //     onFinish: () => {
  //         adding.value = false;
  //     },
  //     onSuccess: async (e) => {
  //         console.log("BAŞARILIIII");

  //         // toast.success(e.props.notification.message);
  //         // emits('done', e.props.notification.data)
  //         // isDialogOn.value = false;
  //     },
  //     onError: (e) => {
  //         console.log("HATAAAA", e);
  //     },
  // },{ headers: { 'accept': 'application/json' }});


}

const checkIfDisabled = computed(() => {

  return form['image'] && form['name'] && form['about'] && form['contry_id'] && (form['artist_branches'].length > 0)

})
onMounted(() => {
  if (props.artist) {
    form['id'] = props.artist['id']
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
      if (e.id == 4) {
        choosenItunesField.value = e.url;
      }
      if (e.id == 2) {
        choosenSpotifyField.value = e.url;
      }


    })
    form['country_id'] = props.artist?.country?.id;

  }
});
</script>

<style scoped>

</style>
