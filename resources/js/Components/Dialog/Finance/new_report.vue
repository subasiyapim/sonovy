<template>

  <BaseDialog v-model="isDialogOn" width="740px" height="min-content" align="center" title="Rapor Oluştur"
              description="Adım adım ihtiyacınıza göre rapor oluşturabilirsiniz.">
    <template #icon>
      <BankLineIcon color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="py-6 px-5 flex flex-col gap-4">

      <AppStepper :modelValue="currentTab" @change="onChangeTab">
        <AppStepperElement title="Dönem Seç"></AppStepperElement>
        <AppStepperElement title="Rapor Tipi"></AppStepperElement>
        <AppStepperElement title="Rapor İçeriği"></AppStepperElement>
        <AppStepperElement title="Oluştur"></AppStepperElement>

      </AppStepper>
      <hr>
      <ChooseReportDate v-model="form" v-if="currentTab == 0"></ChooseReportDate>
      <ChooseReportType v-model="form" v-if="currentTab == 1"></ChooseReportType>
      <ChooseReportContent v-model="form" v-if="currentTab == 2"></ChooseReportContent>
      <ReportSummary v-model="form" v-if="currentTab == 3"></ReportSummary>


    </div>

    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
      <RegularButton @click="isDialogOn = false" class="flex-1">
        {{ __('control.general.cancel') }}
      </RegularButton>
      <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
        <template v-if="currentTab <3">Devam Et</template>
        <template v-else>Raporu Olluştur</template>
        <template #suffix>
          <ChevronRightIcon v-if="currentTab <3" class="ms-2" color="var(--dark-green-500)"/>
          <CheckIcon v-else class="ms-2" color="var(--dark-green-500)"/>
        </template>
      </PrimaryButton>
    </div>
  </BaseDialog>


</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {BankLineIcon, InfoFilledIcon, ChevronRightIcon, CheckIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {FormElement} from '@/Components/Form'
import {useForm, usePage} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';
import {AppStepper, AppStepperElement} from '@/Components/Stepper';
import ChooseReportDate from './NewReportTabs/choose_date_picker.vue';
import ChooseReportType from './NewReportTabs/choose_report_type.vue';
import ChooseReportContent from './NewReportTabs/choose_report_content.vue';
import ReportSummary from './NewReportTabs/report_summary.vue';

const props = defineProps({
  modelValue: {
    default: false,
  },
})
const crudStore = useCrudStore();
const currentTab = ref(0);
const adding = ref(false)


const form = ref({
  type: 1,
  report_content_type: 1,
  date: null,
  choosenValues: [],
})
const emits = defineEmits(['update:modelValue', 'done', 'update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})


const reporttypes = {
  1: "all",
  2: "artists",
  3: "labels",
  4: "songs",
  5: "platforms",
  6: "countries",
  7: "multiple_labels",
  8: "multiple_artists",
  9: "multiple_products",
  10: "multiple_platforms",
  11: "multiple_countries",
}

const onSubmit = async (e) => {
  if (currentTab.value < 3) {
    currentTab.value++;
  } else {


    try {
      await crudStore.post(route('control.finance.reports.store'), {
        start_date: form.value.date[0],
        end_date: form.value.date[1],
        report_type: form.value.type == 1 ? reporttypes[form.value.report_content_type] : reporttypes[form.value.report_content_type],

        ids: form.value.choosenValues,
      })
      toast.success("işlem başarılı");
      location.reload();
    } catch (error) {
      console.log("ERROR", error);

      toast.error(error.response);
    }
  }
  //TODO SUBMİT REPORT DATA

}

const onChangeTab = (tab) => {
  currentTab.value = tab;
}
const checkIfDisabled = computed(() => {

  return false

})

</script>
