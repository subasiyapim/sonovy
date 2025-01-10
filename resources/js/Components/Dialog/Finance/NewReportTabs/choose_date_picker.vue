<script setup>
import {ref,computed} from 'vue';
import moment from 'moment';

const props = defineProps({
    modelValue:{}
})

const emits = defineEmits(['update:modelValue']);

const element = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
});

const getFormatedDate = computed(() => {
    if (element.value.date && Array.isArray(element.value.date) && element.value.date.length === 2) {
    const [startDate, endDate] = element.value.date;
    const formattedStartDate = moment(startDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    const formattedEndDate = moment(endDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    return `${formattedStartDate} - ${formattedEndDate}`;
  }else {
        return 'Tarih seçimi yapınız';
    }
    return element.value.date;
})

const setDateRange = (type) => {
      const now = moment();
      switch (type) {
        case 'last7days':
          element.value.date = [now.clone().subtract(7, 'days').startOf('day').toDate(), now.endOf('day').toDate()];
          break;
        case 'last30days':
          element.value.date = [now.clone().subtract(30, 'days').startOf('day').toDate(), now.endOf('day').toDate()];
          break;
        case 'last3months':
          element.value.date = [now.clone().subtract(3, 'months').startOf('month').toDate(), now.endOf('day').toDate()];
          break;
        case 'last12months':
          element.value.date = [now.clone().subtract(12, 'months').startOf('month').toDate(), now.endOf('day').toDate()];
          break;
        case 'lastMonth':
          element.value.date = [
            now.clone().subtract(1, 'month').startOf('month').toDate(),
            now.clone().subtract(1, 'month').endOf('month').toDate(),
          ];
          break;
        case 'lastYear':
          element.value.date = [
            now.clone().subtract(1, 'year').startOf('year').toDate(),
            now.clone().subtract(1, 'year').endOf('year').toDate(),
          ];
          break;
        case 'allTime':
          element.value.date = [new Date(2000, 0, 1), now.endOf('day').toDate()]; // Example all-time range
          break;
        default:
          element.value.date = [];
      }
    };
</script>

<template>

      <div class="flex flex-col items-start">
            <p class="label-medium c-strong-950">Rapor için tarih seçimi</p>
            <div class="flex items-center gap-2">
                <p class="paragraph-xs c-sub-600">Tarih aralığı</p>
                <p class="label-sm c-strong-950">{{getFormatedDate}}</p>
            </div>
        </div>
        <div class="flex justify-between">
            <div class="flex flex-col">
                <button @click="setDateRange('last7days')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 7 Gün</button>
                <button @click="setDateRange('last30days')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 30 Gün</button>
                <button @click="setDateRange('last3months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 3 Ay</button>
                <button @click="setDateRange('last12months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 12 Ay</button>
                <button @click="setDateRange('lastMonth')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Geçen Ay</button>
                <button @click="setDateRange('lastYear')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Geçen Yıl</button>
                <button @click="setDateRange('allTime')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Tüm Zamanlar</button>
            </div>
            <div class="dateWrapper ">
                <VueDatePicker class="w-full" v-model="element.date" auto-apply :enable-time-picker="false" inline range multi-calendars />
            </div>

        </div>
</template>

<style >
    .dateWrapper .dp__action_row{
        display:none !important;
    }

</style>
