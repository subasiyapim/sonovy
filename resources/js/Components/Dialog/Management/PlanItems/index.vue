<template>
  <BaseDialog width="480px" height="min-content" v-model="isDialogOn" align="center" :title="'Plan Öğeleri'"
              :description="'Plan ögelerinizi ihtiyacınıza göre düzenleyebilirsiniz.. '">
    <template #icon>
      <EditLineIcon color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-4 flex flex-col gap-2">

        <FormElement label-width="210px" direction="vertical"  :required="true" :error="form.errors.template_id" v-model="form.template_id"
                :label="'Kategori Seçimi'"
                :placeholder="'Kategori Seçimi'"
                :config="planItemCategoryConfig"
                type="select"
                >

        </FormElement>
        <FormElement label-width="210px" direction="vertical" :required="true" :error="form.errors.template_id" v-model="form.template_id"
                :label="'Tip Seçiniz'"
                :placeholder="'Tip Seçimi'"
                :disabled="true"
                :config="planItemCategoryConfig"
                type="select"
                >

        </FormElement>

        <FormElement label-width="210px" direction="vertical" :required="true" :error="form.errors.name" v-model="form.name"
                   :label="'Öğe Adı'"
                        :placeholder="'Öğe adını giriniz'"></FormElement>
        <FormElement label-width="210px" direction="vertical"  :required="true" :error="form.errors.status" v-model="form.status"
                    :label="'Durum'"
                    :config="planItemStatusRadioConfig"
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
         Oluştur
      </PrimaryButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {EditLineIcon,AddIcon} from '@/Components/Icons'
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


const planItemCategoryConfig = computed(() => {
  return {
    data: [],
  };
})

const planItemStatusRadioConfig = computed(() => {
    return {
        // optionDirection: 'vertical',
        options: [
            {
                "label" : "Aktif",
                "value" :1,
            },
            {
                "label" : "Pasif",
                "value" :2,
            },
        ]
    }
})

onMounted(() => {

});
</script>

<style scoped>

</style>
