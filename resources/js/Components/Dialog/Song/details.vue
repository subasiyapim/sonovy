<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Şarkı Detayları'"
              :description="'Şarkı detayını ve meta verilerini görüntüleyin'">
    <template #icon>
      <DocumentIcon color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-5 grid grid-cols-2 gap-6">

        <div class="flex gap-3.5 items-center" v-for="detail in Object.keys(song.details?.details)">
            <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
            </div>
            <div>
                <p class="paragraph-xs c-sub-600" v-text="__(`control.product.show.tabs.song_tab.details.${detail}`)"/>
                <span class="label-sm c-strong-950" v-text="song.details?.details[detail]"/>
            </div>
        </div>
    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4">
      <RegularButton @click="isDialogOn = false" class="flex-1">
      <template #icon>
        <CopyIcon color="var(--sub-600)" />
      </template>
       Meta Datalarını Kopyala
      </RegularButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {DocumentIcon,CopyIcon} from '@/Components/Icons'
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
  id: "",
  album_name: '',
  type:1

});


const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})



onMounted(() => {

});
</script>

<style scoped>

</style>
