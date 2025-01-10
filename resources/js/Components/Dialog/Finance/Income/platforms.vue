
<template>

<BaseDialog :showClose="true" v-model="isDialogOn"  height="min-content" align="center" title="Aylık Net Gelir"
              description="Ocak 2024 - Haziran 2024">
    <template #icon>
      <BankLineIcon color="var(--dark-green-950)"/>
    </template>


   <div class="p-5">
    <table class="w-full">
        <thead>
            <tr>
                <td class="label-sm c-strong-950 !font-semibold">Mağaza Adı</td>
                <td class="label-sm c-strong-950  !text-end !font-semibold pe-3 ">Oran</td>
                <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="i in 6">
                <td class="py-3">
                   <span class="label-sm c-strong-950">Alem Alem İçinde</span>

                </td>
                <td style="width:55%;">
                    <div class="flex items-center gap-2">
                        <AppProgressIndicator color="#D02533" :modelValue="10" />
                        <span class="paragraph-xs c-sub-600">$1200.00,25</span>
                    </div>

                </td>
                <td class=" ps-3"> <span class="paragraph-xs c-sub-600">$1200.00,25</span></td>
            </tr>
        </tbody>
    </table>
   </div>



  </BaseDialog>


</template>

<script setup>
import BaseDialog from '@/Components/Dialog/BaseDialog.vue';

import {BankLineIcon,InfoFilledIcon} from '@/Components/Icons'
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

const loading = ref(false);

const getData =  async ()  =>  {
    loading.value = true;
    const response = await crudStore.get(route('control.finance.analysis.show'),{
        slug:'earning_from_platforms',
        request_type:'view',
        start_date:props.choosenDates != null ? props.choosenDates[0] : moment().subtract(1, 'year'),
        end_date:props.choosenDates != null ? props.choosenDates[1] : moment(),
    })
    loading.value = false;
}
onMounted(() => {
    getData();
});

</script>
