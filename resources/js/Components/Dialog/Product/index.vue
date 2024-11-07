<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="__('control.product.page_title')"
              :description="__('control.product.modal_description')">
    <template #icon>
      <AddIcon color="var(--dark-green-950)"/>
    </template>

    <div class="p-5 flex flex-col gap-6">
        <AppFancyRadio v-model="form.type" :config="chooseProductTypeConfig"></AppFancyRadio>
        <FormElement direction="vertical" label-width="190px" :required="true" :error="form.errors.album_name" v-model="form.album_name"
            :label="__('control.product.fields.album_name')"
            :placeholder="__('control.product.fields.album_name_placeholder')"></FormElement>
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
import {AddIcon,AudioIcon,RingtoneIcon,MusicVideoIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';

import {FormElement, AppFancyRadio} from '@/Components/Form'

const props = defineProps({
  modelValue: {
    default: false,
  }
})


const form = useForm({
  id: "",
  album_name: '',
  type:1

});


const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const adding = ref(false);
const onSubmit = (e) => {
    adding.value = true;
    form.post(route('control.catalog.products.store'), {
        onFinish: () => {
            adding.value = false;
        },
        onSuccess: async (e) => {
            console.log("BŞARILIR");

        },
        onError: (e) => {
            console.log("HATAAAA", e);
        },
    });

}

const checkIfDisabled = computed(() => {

  return !form['album_name'];

})

const chooseProductTypeConfig = ref({
    options:[
        {
            title:"Ses",
            value:1,
            icon:AudioIcon,
        },
        {
            title:"Video",
            value:2,
            icon:MusicVideoIcon,
        },
        {
            title:"Zil Sesi",
            value:3,
            icon: RingtoneIcon,
        },
    ]
})
onMounted(() => {

});
</script>

<style scoped>

</style>
