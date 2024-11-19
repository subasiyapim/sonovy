<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Albüm Analizleri'"
              :description="'Albümünüze ait analiz detaylarını görüntüleyin'">
    <template #icon>
      <ChartsIcon color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-5 ">
      <div class="flex items-center gap-2">
        <div class="w-10 h-10 rounded flex items-center justify-center bg-gray-200">
          <img class="image-fluid" :src="product.image">
        </div>
        <div class="flex flex-col items-start">
          <p class="label-xs c-sub-600 mb-2">Albüm Adı</p>
          <p class="label-sm c-strong-950">{{ product.album_name }}</p>
        </div>
      </div>
      <hr class="my-3">

      <div class="w-full border p-4 bg-primary" v-if="song.analysis">
        <h2 class="font-semibold text-lg">ACR Cloud raporu</h2>
        <ul v-for="(value, key) in flattenObject(song.analysis)" :key="key">
          <li>
            <span class="font-semibold uppercase">{{ key }}</span>
            <span class="font-light">: {{ value }}</span>
          </li>
        </ul>
      </div>
    </div>

  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {ChartsIcon, CopyIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';

import {FormElement, AppFancyRadio} from '@/Components/Form'

const props = defineProps({
  modelValue: {
    default: false,
  },
  song: {},
  product: {}
})


const form = useForm({
  id: "",
  album_name: '',
  type: 1

});


const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

function flattenObject(ob) {
  let result = {};

  for (const i in ob) {
    if ((typeof ob[i]) === 'object' && !Array.isArray(ob[i])) {
      const temp = flattenObject(ob[i]);
      for (const j in temp) {
        result[i + '.' + j] = temp[j];
      }
    } else if (Array.isArray(ob[i])) {
      ob[i].forEach((item, index) => {
        if (typeof item === 'object') {
          const temp = flattenObject(item);
          for (const j in temp) {
            result[i + '[' + index + '].' + j] = temp[j];
          }
        } else {
          result[i + '[' + index + ']'] = item;
        }
      });
    } else {
      result[i] = ob[i];
    }
  }
  return result;
}

onMounted(() => {

});
</script>

<style scoped>

</style>
