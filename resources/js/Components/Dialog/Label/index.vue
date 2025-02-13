<template>
  <BaseDialog v-model="isDialogOn" align="right" title="Plak şirketi"
              description="Temel bilgileri girerek sanatçı oluşturabilirsiniz. ">
    <template #icon>
      <AddIcon color="var(--dark-green-950)"/>
    </template>
    <SectionHeader :title="__('control.label.dialog.header_1')"/>

    <div class="p-5 flex flex-col gap-6">

      <FormElement label-width="190px" :required="true" :error="form.errors.image" v-model="form.image"
                    @onImageDelete="onImageDelete"
                   :label="__('control.label.fields.image')" type="upload"
                   :config="{label:__('control.label.fields.image_description'),note:'Min 400x400px, PNG or JPEG',image:label?.image?.thumb}"></FormElement>
      <FormElement label-width="190px" :required="true" :error="form.errors.name" v-model="form.name"
                   :label="__('control.label.fields.name')"
                   :placeholder="__('control.label.fields.name_placeholder')"></FormElement>
      <FormElement label-width="190px" :required="true" :error="form.errors.address" v-model="form.address"
                   :config="{letter:500}" :label="__('control.label.fields.address')" type="textarea"
                   :placeholder="__('control.label.fields.address_placeholder')"></FormElement>
      <FormElement label-width="190px" :required="true" :error="form.errors.country_id" v-model="form.country_id"
                   :label="__('control.label.fields.country_id')" type="select" :config="countryConfig"
                   :placeholder="__('control.label.fields.country_placeholder')">
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
    </div>
    <SectionHeader :title="__('control.label.dialog.header_2')"/>
    <div class="p-5 flex flex-col gap-6">
      <FormElement label-width="190px" :error="form.errors.phone" v-model="form.phone"
                   :config="{codes:usePage().props.countryCodes}" :label="__('control.label.fields.phone')" type="phone"
                   placeholder="(555) 000-0000"></FormElement>
      <FormElement label-width="190px" :error="form.errors.email" v-model="form.email"
                   :label="__('control.label.fields.email')" type="text" placeholder="examp@example.com"></FormElement>
      <FormElement label-width="190px" :error="form.errors.website" v-model="form.website"
                   :label="__('control.label.fields.web')" placeholder="www.example.com" type="web"></FormElement>
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
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {AddIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {FormElement} from '@/Components/Form'
import {useForm, usePage} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';

const props = defineProps({
  modelValue: {
    default: false,
  },
  label: {
    default: null
  },
})
const crudStore = useCrudStore();
const isUpdating = computed(() => {
  return !!props.label;
});
const adding = ref(false)
const image = ref();
const form = useForm({
  name: '',
  country_id: "",
  address: "",
  image: "",
  phone: "",
  email: "",
  website: "",

});
const emits = defineEmits(['update:modelValue', 'done','update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const platforms = ref([{}]);


const countryConfig = computed(() => {
  return {
    data: usePage().props.countries,
  };
})
const onImageDelete = async (e) => {
   var response = await  crudStore.del(route('control.media.destroy',props.label?.image?.id))
}
const onSubmit = (e) => {
  adding.value = true;
  if (image.value) {
    form.image = image.value?.file;

  }


  if (isUpdating.value) {
    form
        .transform((data) => ({
          ...data,
          _method: 'PUT'
        }))
        .post(route('control.catalog.labels.update', props.label.id), {
          preserveScroll: true,
          onSuccess: (e) => {
           toast.success(e.props.notification.message);
            emits('update', e.props.notification.data)
            isDialogOn.value = false;
          },
          onError: (e) => {
            console.log("HATAAA", e);
          }
        });
    return;
  }
  form.post(route('control.catalog.labels.store'), {
    onFinish: () => {
      adding.value = false;
    },
    onSuccess: async (e) => {
      toast.success(e.props.notification.message);
      emits('done', e.props.notification.data)
      isDialogOn.value = false;

      setTimeout(() => {
        window.location.reload();
      }, 1000);
    },
    onError: (e) => {
      console.log("HATAAAA", e);
    },
  });

}
const checkIfDisabled = computed(() => {

  return form['name'] && form['address'] && form['contry_id'] && (form['artist_branches'].length > 0)

})
onMounted(() => {
  if (props.label) {
    form['name'] = props.label['name']
    form['address'] = props.label['address']
    form['phone'] = props.label['phone']
    form['website'] = props.label['website']
    form['email'] = props.label['email']

    form['platforms'] = props.label['platforms']
    form['country_id'] = props.label?.country?.id;

  }
});
</script>

<style lang="scss" scoped>

</style>
