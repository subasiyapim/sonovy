<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Yeni Şablon Oluştur'"
              :description="'Aşağıdaki bilgileri doldurarak yeni bir şablon oluşturuabilirsiniz. '">
    <template #icon>
      <FileList2Line color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-4">
        <FormElement label-width="190px" :required="true" :error="form.errors.name" v-model="form.name"
                   :label="__('control.announcement.form.name')"
                        :placeholder="__('control.label.fields.name_placeholder')"></FormElement>
            <FormElement label-width="190px" class="my-4" :required="true" :error="form.errors.type" v-model="form.type"
                        :label="__('control.announcement.form.type')"
                        :config="announcementTemplateRadioConfig"
                        type="radio"
                        ></FormElement>
            <FormElement label-width="190px" :required="true" :error="form.errors.text" v-model="form.text"
                        :label="__('control.announcement.form.text')"
                            type="textarea"
                        :placeholder="__('control.announcement.form.text_placeholder')"></FormElement>
    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4">
      <RegularButton @click="isDialogOn = false" class="flex-1">

        Kapat
      </RegularButton>
      <PrimaryButton @click="isDialogOn = false" class="flex-1">
        <template #icon>
            <AddIcon color="var(--dark-green-500)" />
        </template>
        Şablon Oluştur
      </PrimaryButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {FileList2Line,AddIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';

import {FormElement, AppFancyRadio} from '@/Components/Form'

const props = defineProps({
  modelValue: {
    default: false,
  },
  song:{

  }
})


const form = useForm({
  name: "",
  type: 1,
  text:""

});


const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})



const announcementTemplateRadioConfig = computed(() => {
  return {
    // optionDirection: 'vertical',
    options: [
        {
            "label" : "Site İçi",
            "value" :1,
        },
        {
            "label" : "Bakım",
            "value" :2,
        },
        {
            "label" : "E-Mail",
            "value" :3,
        },
        {
            "label" : "SMS",
            "value" :4,
        }
    ]
  }
})
onMounted(() => {

});
</script>

<style scoped>

</style>
