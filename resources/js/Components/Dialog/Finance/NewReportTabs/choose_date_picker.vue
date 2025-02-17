<script setup>
import { ref, computed, watch } from 'vue';
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

// Compute formatted date range for display
const getFormatedDate = computed(() => {
  if (element.value.date && Array.isArray(element.value.date) && element.value.date.length === 2) {
    const [startDate, endDate] = element.value.date;
    const formattedStartDate = moment(startDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    const formattedEndDate = moment(endDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    return `${formattedStartDate} - ${formattedEndDate}`;
  } else {
    return 'Tarih seçimi yapınız';
  }
});

// Define reactive variables for min and max selectable dates
const minDate = ref(null);
const maxDate = ref(null);

const onModelChange = (newVal) =>{



     if (newVal && Array.isArray(newVal)) {
      if (newVal.length === 2) {
        // If both start and end dates are selected, reset min/max limits
        minDate.value = null;
        maxDate.value = null;
      } else if (newVal.length === 1) {

        const firstDate = moment(newVal[0]);

        // Set the min and max range dynamically
        minDate.value = firstDate.clone().subtract(2, 'months').toDate();
        maxDate.value = firstDate.clone().add(2, 'months').toDate();
      }
    } else {
      // Reset min/max dates if no date is selected
      minDate.value = null;
      maxDate.value = null;
    }

}
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
    case 'last2months':
      range = [
        { month: moment().subtract(2, 'months').month(), year: moment().subtract(2, 'months').year() },
          { month: moment().month(), year: moment().year() },
      ];
      break;
    case 'last3months':
      range = [
        { month: moment().subtract(3, 'months').month(), year: moment().subtract(3, 'months').year() },
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
      <button @click="setDateRange('last2months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 2 Ay</button>
      <button @click="setDateRange('last3months')" class="p-3 hover:bg-[#F5F7FA] label-sm c-sub-600">Son 3 Ay</button>
    </div>
    <div class="dateWrapper mx-auto">
      <VueDatePicker
        @internal-model-change="onModelChange"
        ref="vueDatePicker"
        class="w-full"
        v-model="element.date"

        auto-apply
        :enable-time-picker="false"
        month-picker
        inline
        range
        multi-calendars
        :min-date="minDate"
        :max-date="maxDate"
      />
    </div>
  </div>
</template>

<style>
.dateWrapper .dp__action_row {
  display: none !important;
}
</style>
