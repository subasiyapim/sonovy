
<template>

<BaseDialog :showClose="true" v-model="isDialogOn"  height="min-content" align="center" title="Satış Tipine Göre Gelir"
              :description="formattedDates">
    <template #icon>
      <Table2Icon color="var(--dark-green-950)"/>
    </template>

    <hr>
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
            <tr v-for="item in tableData">
                <td class="py-3">
                   <span class="label-sm c-strong-950">{{item.platform}}</span>

                </td>
                <td style="width:55%;">
                    <div class="flex items-center gap-2">
                        <AppProgressIndicator :color="getColor(item.platform) ?? 'var(--blue-500)'" :modelValue="parseInt(item.percentage.split(-1))" />
                        <span class="paragraph-xs c-sub-600">{{item.percentage}}</span>

                    </div>

                </td>
                <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{item.earning.toFixed(2)}}</span></td>
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

import {Table2Icon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore'
import moment from 'moment';
import  'moment/dist/locale/tr';
moment.locale('tr');
import {computed, ref, onMounted} from 'vue';
import {AppProgressIndicator} from '@/Components/Widgets';



const crudStore = useCrudStore();
const props = defineProps({
    modelValue: {
        default: false,
    },
    choosenDates:{},
    formattedDates:{},
})

const getColor = computed(() => {
    return (slug) => {
        let c = {
            "Stream" : '#1D3557',
            "Creation" : '#4ECDC4',
            "Download" : '#6C757D',
            "Platform Promotion" : '#D3D3D3',
        }
        return c[slug];
    }
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
