<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Yeni Şablon Oluştur'"
              :description="'Aşağıdaki bilgileri doldurarak yeni bir şablon oluşturuabilirsiniz. '">
    <template #icon>
      <FileList2Line color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-4 flex flex-col gap-4">
        <FormElement label-width="210px" :required="true" :error="form.errors.name" v-model="form.name"
                   :label="__('control.announcement.form.name')"
                        :placeholder="__('control.label.fields.name_placeholder')"></FormElement>
        <FormElement label-width="210px" class="my-4" :required="true" :error="form.errors.type" v-model="form.type"
                    :label="__('control.announcement.form.type')"
                    :config="announcementTemplateRadioConfig"
                    type="radio"
                    ></FormElement>
        <FormElement label-width="210px" class="my-4" :required="true" :error="form.errors.template_id" v-model="form.template_id"
                    :label="__('control.announcement.form.select_template')"
                    :placeholder="__('control.announcement.form.select_template')"
                    :config="announcementTemplateRadioConfig"
                    type="select"
                    >
            <template #first_child>
                <button @click="openArtistCreateDialog('main_artists')"
                        class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
                    <AddIcon color="var(--dark-green-600)"/>
                    Şablon Oluştur
                </button>
            </template>
            <template #empty>
                <button @click="openArtistCreateDialog('main_artists')"
                        class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
                    <AddIcon color="var(--dark-green-600)"/>
                    Şablon Oluştur
                </button>
            </template>
        </FormElement>
        <FormElement label-width="210px" :required="true" :error="form.errors.text" v-model="form.text"
                    :label="__('control.announcement.form.text')"
                        type="textarea"
                    :placeholder="__('control.announcement.form.text_placeholder')"></FormElement>
        <FormElement label-width="210px" class="my-4" :required="true" :error="form.errors.template_send_type" v-model="form.template_send_type"
            :label="__('control.announcement.form.template_send_type')"
            :config="announcementSendTypeConfig"
            type="radio"
            ></FormElement>

        <FormElement label-width="210px" class="my-4"  :error="form.errors.from"
            :label="__('control.announcement.form.from')"
            :config="announcementSendTypeConfig"
            type="custom"
            >
                <div class="flex items-center gap-2">
                    <VueDatePicker  v-model="form.from" class="!rounded-sm h-8" auto-apply :enable-time-picker="false" placeholder="Başlangıç Tarih"></VueDatePicker>
                    <VueDatePicker  v-model="form.from" class="!rounded-sm h-8" auto-apply time-picker  placeholder="Başlangıç Tarih"></VueDatePicker>
                </div>
        </FormElement>
        <FormElement label-width="210px" class="my-4" :error="form.errors.to" v-model="form.to"
            :label="__('control.announcement.form.to')"
            :config="announcementSendTypeConfig"
            type="custom"
            >
                <div class="flex items-center gap-2">
                    <VueDatePicker  v-model="form.from" class="!rounded-sm h-8" auto-apply :enable-time-picker="false" placeholder="Başlangıç Tarih"></VueDatePicker>
                    <VueDatePicker  v-model="form.from" class="!rounded-sm h-8" auto-apply time-picker  placeholder="Başlangıç Tarih"></VueDatePicker>
                </div>
        </FormElement>
        <FormElement label-width="210px" class="my-4"  :error="form.errors.receivers_type" v-model="form.receivers_type"
            :label="__('control.announcement.form.receivers_type')"
            :config="announcementReceiversTypeConfig"
            type="radio"
            ></FormElement>
    </div>
    <div class="sticky bottom-0 bg-white flex p-5 border-t border-soft-200 gap-4">
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
  text:"",
  template_id:null,

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

const announcementSendTypeConfig = computed(() => {
    return {
        // optionDirection: 'vertical',
        options: [
            {
                "label" : "Otomatik",
                "value" :1,
            },
            {
                "label" : "Manuel",
                "value" :2,
            },
        ]
    }
})
const announcementReceiversTypeConfig = computed(() => {
    return {
        optionDirection: 'vertical',
        options: [
            {
                "label" : "Tüm kullanıcılara duyuru yapılsın",
                "value" :1,
            },
            {
                "label" : "Seçilenler hariç tüm kullanıcılara duyuru yapılsın",
                "value" :2,
            },
            {
                "label" : "Sadece seçilen kullanıcılara duyuru yapılsın",
                "value" :3,
            },

        ]
    }
})
onMounted(() => {

});
</script>

<style scoped>

</style>
