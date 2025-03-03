<template>

<BaseDialog  v-model="isDialogOn" width="462px" height="min-content" align="center" title="Rapor Hataları"
              description="rapor hataları listelenmiştir">
    <template #icon>
      <UploadIcon color="var(--dark-green-950)"/>
    </template>



    <div class="flex flex-col gap-2 px-5 py-5">
        <div v-for="error in errors" class="border-b border-soft-200">
          <p class="paragraph-xs c-strong-950">Satır:  {{error.row_number}}</p>
          <p class="paragraph-xs c-strong-950">  {{error.message}}</p>
          <p class="paragraph-xs c-strong-950"> Net: {{error.row_data.net_revenue}}</p>

        </div>
    </div>



    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
      <RegularButton @click="isDialogOn = false" class="flex-1">
        {{ __('control.general.cancel') }}
      </RegularButton>

    </div>
  </BaseDialog>


</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';

const props = defineProps({
  modelValue: {
    default: false,
  },
  errors:{
    default:[]
  }
})


const emits = defineEmits(['update:modelValue', 'done','update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})




</script>
