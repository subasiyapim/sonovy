<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Tarihçe'"
              :description="'Platforma ait tarih detaylarını görüntüleyin'">
    <template #icon>
      <ChartsIcon color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-5 ">
      <div class="flex items-center gap-2">
        <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center ">
          <Icon :icon="platform.icon" />
        </div>
        <div class="flex flex-col items-start">
          <p class="label-xs c-sub-600 mb-1">Platform Adı</p>
          <p class="label-sm c-strong-950">{{ platform.name }}</p>
        </div>
      </div>
      <hr class="my-3">
    <AppTable  v-model="platform.histories"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="Aktivite">
            <template #default="scope">
                 <p class="c-sub-600 label-xs">{{scope.row.status}}</p>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Aksiyon Tarihi">
            <template #default="scope">
                <p class="paragraph-xs c-sub-600">{{scope.row.pre_order_date}}</p>
            </template>
        </AppTableColumn>


        <AppTableColumn label="Tamamlanma Tarihi">
            <template #default="scope">
                 <p class="paragraph-xs c-sub-600">{{scope.row.publish_date}}</p>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Toplam Süre">
            <template #default="scope">
                 <p class="paragraph-xs c-sub-600">{{scope.row.price}} </p>
            </template>
        </AppTableColumn>

        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
    </div>

  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {ChartsIcon, CopyIcon,Icon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {FormElement, AppFancyRadio} from '@/Components/Form'

const props = defineProps({
  modelValue: {
    default: false,
  },

  platform: {}
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
