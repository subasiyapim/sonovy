
<template>

<BaseDialog :showClose="true" v-model="isDialogOn"  height="min-content" align="center" title="Ürüne Tipine Göre Gelir"
              description="Ocak 2024 - Haziran 2024">
    <template #icon>
      <WorldIcon color="var(--dark-green-950)"/>
    </template>


   <div class="p-5">
    <table class="w-full" v-if="!loading">
        <thead>
            <tr>
                <td class="label-sm c-strong-950 !font-semibold">Mağaza Adı</td>
                <td class="label-sm c-strong-950  !text-end !font-semibold pe-3 ">Oran</td>
                <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="key in Object.keys(tableData)">
                <td class="py-3">
                   <span class="label-sm c-strong-950">{{key}}</span>

                </td>
                <td style="width:55%;">
                    <div class="flex items-center gap-2">
                        <AppProgressIndicator color="#D02533" :modelValue="parseInt(tableData[key].percentage.split(-1))" />
                        <span class="paragraph-xs c-sub-600">{{tableData[key].percentage}}</span>
                    </div>

                </td>
                <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{tableData[key].earning}}</span></td>
            </tr>
        </tbody>
    </table>
    <div v-else class="h-64 flex items-center justify-center">
        Yükleniyor
    </div>
   </div>



  </BaseDialog>


</template>

<script setup>
import BaseDialog from '@/Components/Dialog/BaseDialog.vue';

import {WorldIcon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore'
import moment from 'moment';
import {computed, ref, onMounted} from 'vue';
import {AppProgressIndicator} from '@/Components/Widgets';



const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {
    default: false,
  },
})


const emits = defineEmits(['update:modelValue']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const tableData = ref([]);
const loading = ref(false);

const getData =  async ()  =>  {
    loading.value = true;
   try {
        const response = await crudStore.get(route('control.finance.analysis.show'),{
            slug:'earning_from_sales_type',
            request_type:'view',
            start_date:props.choosenDates != null ? props.choosenDates[0] : moment().subtract(1, 'year'),
            end_date:props.choosenDates != null ? props.choosenDates[1] : moment(),
        })
        tableData.value = response;
   } catch (error) {

   }
    loading.value = false;
}
onMounted(() => {
        getData();
});


</script>
