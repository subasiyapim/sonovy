<template>
  <AdminLayout :showDatePicker="false" :showGoBack="false" :showBreadCrumb="false"
 :title="__('control.finance.payments.header')" parentTitle="Katalog">
    <template #toolbar>
      <RegularButton>
        <template #icon>
          <DownloadIcon color="var(--sub-600)"/>
        </template>
        <p class="ms-2">{{ __('control.finance.payments.download_report') }}</p>
      </RegularButton>
    </template>
    <div class="flex items-start my-6">
      <div class="flex-1">
        <h1 class="subheading-regular c-strong-950 mb-0.5">{{ __('control.finance.payments.current_balance_status') }}</h1>
        <p class="label-sm c-neutral-500">{{ __('control.finance.payments.current_balance_status_description') }}</p>
      </div>
      <PrimaryButton @click="openPaymentModal" class="w-60">
        <template #icon>
          <WalletIcon color="var(--dark-green-500)"/>
        </template>
        <p>{{ __('control.finance.payments.withdraw_money') }}</p>

      </PrimaryButton>
    </div>
    <div class="grid grid-cols-3 gap-3 mb-5">
      <AppCard class="flex-1 w-full">
        <template #header>
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <WalletLineIcon color="var(--sub-600)"/>
          </div>
        </template>
        <template #tool>
            <Vue3Apexcharts height="40" width="60" :options="options" :series="series"></Vue3Apexcharts>
        </template>
        <template #body>
          <div class="flex flex-col mt-8">
            <p class="paragraph-sm c-sub-600 mb-0.5">{{ __('control.finance.payments.current_balance') }}</p>
           <div class="flex items-center gap-2">
             <p class="card-currency-header c-strong-950">{{ usePage().props.balance }}</p>
            <span class="px-2 py-0.5 rounded-full bg-[#BDECCD] text-[#0D2D23] label-xs" >+2%</span>
           </div>
          </div>
        </template>
      </AppCard>
      <AppCard class="flex-1 w-full">
        <template #header>
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <ExitIcon color="var(--sub-600)"/>
          </div>

        </template>
        <template #tool>

        </template>
        <template #body>
          <div class="flex flex-col mt-8">
            <p class="paragraph-sm c-sub-600 mb-0.5">{{ __('control.finance.payments.expected_payment') }}</p>
            <div class="flex items-center gap-2">
                <p class="card-currency-header c-strong-950">{{ usePage().props.total_pending_payment }}</p>
                <span class="px-2 py-0.5 rounded-full bg-[#CAC0FF] text-[#351A75] label-xs" >{{ __('control.finance.payments.expected_payment_description') }}</span>
            </div>
          </div>
        </template>
      </AppCard>
      <AppCard class="flex-1 w-full relative overflow-hidden">
        <template #header>
          <div class="flex items-center">
            <SpeedUpIcon color="var(--sub-600)"/>
            <div class="flex-1 ms-2"><p class="label-sm c-strong-950 !text-start">{{ __('control.finance.payments.payment_information') }}</p></div>
            <IconButton @click="openBankAccountModal" class="border border-soft-200 rounded-lg">
              <EditLineIcon color="var(--sub-600)"/>
            </IconButton>
          </div>
        </template>

        <template #body>
          <hr class="my-3">
          <div v-if="account">

            <div class="flex items-center mb-2">
              <BankLineIcon color="var(--sub-600)"/>
              <span class="flex-1 ms-2 paragraph-sm c-sub-600">{{ __('control.finance.payments.iban') }}</span>
              <span class="label-sm c-strong-950">{{ account.iban }}</span>
            </div>
            <div class="flex items-center">
              <img width="16" src="@/assets/images/circular_color_image.png"/>
              <span class="flex-1 ms-2 paragraph-sm c-sub-600">{{ __('control.finance.payments.email') }}</span>
              <span class="label-sm c-strong-950">{{ usePage().props.auth.user.email }}</span>
            </div>
            <div class="bg-[#F2F5F8] absolute left-0 right-0 bottom-0 flex items-center justify-start py-1 ps-4">
              <span class="paragraph-xs c-sub-600">{{ __('control.finance.payments.change_email_description') }}</span>
            </div>
          </div>

          <div v-else class="flex flex-col gap-2 items-center justify-center mt-8">
            <WalletIcon color="var(--sub-600)"/>
            <p class="label-sm c-strong-950">{{ __('control.finance.payments.add_account_description') }}</p>
          </div>


        </template>

      </AppCard>

    </div>


    <div class="flex justify-between items-start gap-2 bg-[#EBF1FF] p-3.5 rounded-lg my-12">
      <InfoFilledIcon class="mt-1" color="#335CFF"/>
      <div class="flex-1">
        <div class="flex justify-between items-center  mb-3">
          <div class="flex items-center gap-1 flex-1">
            <span
                class="label-medium !font-semibold c-strong-950">{{ __('control.finance.payments.withdraw_request_success') }}</span>
          </div>
          <div>
            <div class="flex items-center gap-3">
              <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full bg-dark-green-700 flex items-center justify-center">
                  <CheckIcon color="#fff"/>
                </div>
                <p class="c-strong-950 paragraph-sm">{{ __('control.finance.payments.withdraw_request_success_description') }}</p></div>
              <ChevronRightIcon color="var(--sub-600)"/>
              <div class="flex items-center gap-2">
                <div
                    class="w-5 h-5 rounded-full bg-[#FF8447] flex items-center justify-center text-white text-sm font-medium">
                  2
                </div>
                <p class="c-strong-950 paragraph-sm">{{ __('control.finance.payments.under_review') }}</p></div>
              <ChevronRightIcon color="var(--sub-600)"/>
              <div class="flex items-center gap-2">
                <div
                    class="w-5 h-5 rounded-full bg-white border border-soft-200 flex items-center justify-center c-sub-600 text-sm font-medium">
                  3
                </div>
                <p class="c-sub-600 paragraph-sm">{{ __('control.finance.payments.amount_will_be_sent') }}</p></div>
            </div>
          </div>
        </div>
        <div>
          <p class="paragraph-sm c-neutral-600 w-3/4">
            <!-- {{ pending_payment.date }} tarihinde {{ pending_payment.amount }} tutarında ödeme talep
            ettiniz. Ödeme isteğiniz ekibimiz tarafından onaylanacak ve {{ pending_payment.planned_payment_date }}
            tarihine kadar aracı şirkete gönderilecek. -->
                            {{ pending_payment.date }} {{ __('control.finance.payments.requested_payment',
                    { amount: pending_payment.amount }) }}. {{ __('control.finance.payments.payment_request_approval') }}
                    {{ pending_payment.planned_payment_date }} {{ __('control.finance.payments.payment_request_sent') }}.
          </p>
        </div>
      </div>
    </div>
    <AppTable :showAddButton="false" :buttonLabel="__('control.finance.add_new')" ref="pageTable"
              :config="appTableConfig"
              v-model="usePage().props.payments">
      <template #tableHeader>
        <p class="subheading-regular c-strong-950"> İşlem Tarihçesi</p>
      </template>
      <AppTableColumn :label="__('control.finance.payments.table.column_1')" align="left" sortable="name">
        <template #default="scope">
            <div>
                <p class="label-sm c-strong-950">{{ moment(scope.row.date).format('D MMMM Y') }}</p>
                <p class="label-sm c-strong-950">{{ moment(scope.row.date).format('HH:mm:ss') }}</p>
            </div>

        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.table.column_2')" sortable="name" >
        <template #default="scope">

            <div class="rounded-full px-2 py-0.5" :class="scope.row.status == 1 ? 'bg-[#FFD5C0]' : (scope.row.status == 2 ? 'bg-[#D8E5ED]' : (scope.row.status == 4 ? 'bg-[#FFC0C5]' : 'bg-[#BDECCD]') )">
                <p class="label-xs c-sub-600 whitespace-nowrap " :class="scope.row.status == 1 ? 'text-[#682F12]' : (scope.row.status == 2 ? 'text-[#060E2F]' : (scope.row.status == 4 ? 'text-[#681219]' : 'text-[#0D2D23]') )">{{ scope.row.status_text }}</p>
            </div>

        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.finance.payments.table.column_3')" sortable="name">
        <template #default="scope">
          <p class="paragraph-xs c-sub-600">{{ scope.row.description }}</p>
        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.finance.payments.table.column_4')" sortable="name">
        <template #default="scope">
            <div class="border border-soft-200 rounded px-2 py-1">
                <p class="paragraph-xs c-sub-600">{{ scope.row.amount }}</p>
            </div>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.table.column_5')" sortable="name">
        <template #default="scope">
            <div class="border border-soft-200 rounded px-2 py-1">
                <p class="paragraph-xs c-sub-600">{{ scope.row.balance }}</p>
            </div>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.table.column_6')" align="right">
        <template #default="scope">
         <button><PdfIcon /></button>
        </template>
      </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">{{ __('control.finance.payments.table.empty_header') }}</h2>
          </div>

        </div>
      </template>
    </AppTable>

    <WithdrawModal @update="onUpdate" v-if="isModalOn" v-model="isModalOn"/>
    <BankAccountModal :account="account" @update="onUpdate" @done="onDone" v-if="isBankAccountModalOn"
                      v-model="isBankAccountModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed, onMounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton, RegularButton} from '@/Components/Buttons'
import {StatusBadge} from '@/Components/Badges'
import Vue3Apexcharts from 'vue3-apexcharts'
import moment from 'moment'
import  'moment/dist/locale/tr';

moment.locale('tr');


import {
  AddIcon,
  InfoFilledIcon,
  PdfIcon,
  CheckIcon,
  ChevronRightIcon,
  LabelsIcon,
  WalletIcon,
  DownloadIcon,
  BankLineIcon,
  TrashIcon,
  EditIcon,
  ExitIcon,
  WalletLineIcon,
  SpeedUpIcon,
  EditLineIcon
} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {WithdrawModal, BankAccountModal} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();
const pageTable = ref();

const props = defineProps({
  balance: {
    type: [Number, String],
    required: true
  },
  total_pending_payment: {
    type: [Number, String],
    required: true
  },
  pending_payment: {
    type: Object,
    required: true
  },
  account: {
    type: Object,
    required: true
  },
})


const data = ref([])
const choosenLabel = ref(null);
const isModalOn = ref(false);
const isBankAccountModalOn = ref(false);
const openPaymentModal = () => {

  isModalOn.value = !isModalOn.value;
}
const openBankAccountModal = () => {

  isBankAccountModalOn.value = !isBankAccountModalOn.value;
}

const deleteRow = (row) => {
  pageTable.value.removeRowDataFromRemote(row);
}
const editRow = (label) => {

  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}
const onDone = (e) => {
  props.account = e;
}

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})
const onUpdate = (e) => {
  props.account.iban = e.iban;
}


const series = ref([
        {
          name: "Data",
          data: [10, 20, 15, 25, 20, 30],
        },
      ]);
const options = ref({
        chart: {
          type: "area",
          toolbar: {
            show: false,
          },
          sparkline: {
            enabled: true, // Removes extra padding for a compact chart
          },
        },
        stroke: {
          curve: "smooth", // Smoothens the line
          width: 2,
        },
        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
            stops: [0, 90, 100],
          },
        },
        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          },
          axisTicks: {
            show: false,
          },
        },
        yaxis: {
          show: false,
        },
        grid: {
          show: false,
        },
        tooltip: {
          enabled: false,
        },
      });

</script>

<style lang="scss" scoped>

</style>
