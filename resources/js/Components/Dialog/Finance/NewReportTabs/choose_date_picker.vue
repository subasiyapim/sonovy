<script setup>
import { ref, computed } from 'vue';
import moment from 'moment';
import 'moment/dist/locale/tr';
moment.locale('tr');

const vueDatePicker = ref();
const props = defineProps({
  modelValue: {},
  formattedDates: {},
});

const emits = defineEmits(['update:modelValue']);

const element = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value),
});

const getFormatedDate = computed(() => {
  if (
    element.value.date &&
    Array.isArray(element.value.date) &&
    element.value.date.length === 2
  ) {
    const [startDate, endDate] = element.value.date;
    const formattedStartDate = moment(startDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    const formattedEndDate = moment(endDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    return `${formattedStartDate} - ${formattedEndDate}`;
  } else {
    return 'Tarih seçimi yapınız';
  }
});

const setDateRange = (type) => {
  const now = new Date();
  let range = [];

  switch (type) {
    case 'last30days':

        range = [
            { month: moment().subtract(1, 'months').month(), year: moment().subtract(1, 'months').year() },
            { month: moment().month(), year: moment().year() },
        ];
        break;
    case 'last3months':
      range = [
        { month: moment().subtract(3, 'months').month(), year: moment().subtract(3, 'months').year() },
          { month: moment().month(), year: moment().year() },
      ];
      break;
    case 'last6months':
      range = [
        { month: moment().subtract(6, 'months').month(), year: moment().subtract(6, 'months').year() },
          { month: moment().month(), year: moment().year() },
      ];
      break;
    case 'last12months':


      range = [
        { month: moment().subtract(1, 'year').month(), year: moment().subtract(1, 'year').year() },
        { month: moment().month(), year: moment().year() },
      ];
      break;

    case 'allTime':
      range = [
        {
          month: 0,
          year: 2000,
        }, // Starting from January 2000
         { month: moment().month(), year: moment().year() },
      ];
      break;
    default:
      range = [];
  }
    console.log("RANGE",range);

  // Update the element value with the range in {month, year} format
  element.value.date =  range;
};
</script>

<template>
  <div class="flex flex-col items-start">
    <p class="label-medium c-strong-950">Rapor için tarih seçimi</p>
    <div class="flex items-center gap-2">
      <p class="paragraph-xs c-sub-600">Tarih aralığı</p>
      <p class="label-sm c-strong-950">{{ getFormatedDate }}</p>
    </div>
  </div>
  <div class="flex justify-between">
    <div class="flex flex-col flex-1">
      <button @click="setDateRange('last30days')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 1 Ay</button>
      <button @click="setDateRange('last3months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 3 Ay</button>
      <button @click="setDateRange('last6months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 6 Ay</button>
      <button @click="setDateRange('last12months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 12 Ay</button>
      <button @click="setDateRange('allTime')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Tüm Zamanlar</button>
    </div>
    <div class="dateWrapper mx-auto">
      <VueDatePicker ref="vueDatePicker" class="w-full" v-model="element.date" auto-apply :enable-time-picker="false" month-picker inline range multi-calendars />
    </div>
  </div>
</template>

<style>
.dateWrapper .dp__action_row {
  display: none !important;
}
</style>
