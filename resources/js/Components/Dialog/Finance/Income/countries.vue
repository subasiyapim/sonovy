
<template>

<BaseDialog :showClose="true" v-model="isDialogOn"  height="min-content" align="center" title="Ülkelere Göre Gelir"
              :description="formattedDates">


    <template #icon>
      <WorldIcon color="var(--dark-green-950)"/>
    </template>

    <hr>
   <div class="p-5">
     <table class="w-full" v-if="!loading">
        <thead>
            <tr>
                <td class="label-sm c-strong-950 !font-semibold">Ülke Adı</td>
                <td class="label-sm c-strong-950  !text-end !font-semibold pe-3 ">Oran</td>
                <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in tableData.sort((a,b) => b.percentage - a.percentage)">
                <td class="py-3">
                   <span class="label-sm c-strong-950">{{item.country}}</span>

                </td>
                <td style="width:60%;">
                    <div class="flex items-center gap-2">
                        <AppProgressIndicator color="var(--blue-500)" :modelValue="item.percentage" />
                        <span class="w-12 !text-end paragraph-xs c-sub-600 flex items-center">{{item.percentage}}%</span>
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
import moment from 'moment';
import  'moment/dist/locale/tr';
moment.locale('tr');
import {WorldIcon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore'
import {computed, ref, onMounted} from 'vue';
import {AppProgressIndicator} from '@/Components/Widgets';



const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {
    default: false,
  },
  choosenDates:{

  },
   formattedDates:{},
})

const emits = defineEmits(['update:modelValue']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const loading = ref(false);
const tableData = ref({});
const getData =  async ()  =>  {
    loading.value = true;
    try {
        const response = await crudStore.get(route('control.finance.analysis.show'),{
            slug:'earning_from_countries',
            request_type:'view',
            start_date:props.choosenDates != null ? props.choosenDates[0] : moment().subtract(1, 'year'),
            end_date:props.choosenDates != null ? props.choosenDates[1] : moment(),
        });
        tableData.value = response;
    } catch (error) {

    }
    loading.value = false;
}
onMounted(() => {
    getData();
});

</script>
