<template>
  <AdminLayout  :title="__('control.finance.payments.header')" parentTitle="Katalog">
    <template #toolbar>
        <RegularButton>
            <template #icon>
                <DownloadIcon color="var(--sub-600)" />
            </template>
           <p class="ms-2"> Raporu İndir</p>
        </RegularButton>
    </template>
    <div class="flex items-start my-6">
        <div class="flex-1">
            <h1 class="subheading-regular c-strong-950 mb-0.5"> Mevcut Bakiye Durumu</h1>
            <p class="label-sm c-neutral-500">Mevcut bakiyenizi ve yakın zamanda gelecek olan ödemelerinizi buradan takip edebilrsiniz.</p>
        </div>
        <PrimaryButton @click="openPaymentModal" class="w-60">
            <template #icon>
                <WalletIcon color="var(--dark-green-500)" />
            </template>
            <p>Para Çek</p>

        </PrimaryButton>
    </div>
    <div class="flex grid grid-cols-3 gap-3 mb-5">
        <AppCard class="flex-1 w-full">
            <template #header>
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center"><WalletLineIcon color="var(--sub-600)" /></div>
            </template>
            <template #tool>

            </template>
            <template #body>
                <div class="flex flex-col mt-5">
                    <p class="paragraph-sm c-sub-600 mb-0.5">Mevcut Bakiye</p>
                    <p class="card-currency-header c-strong-950">$0.00</p>
                </div>
            </template>
        </AppCard>
        <AppCard class="flex-1 w-full">
                <template #header>
                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center"><ExitIcon color="var(--sub-600)" /></div>

                </template>
                <template #tool>

                </template>
              <template #body>
                <div class="flex flex-col mt-5">
                    <p class="paragraph-sm c-sub-600 mb-0.5">Beklenen ödemde</p>
                    <p class="card-currency-header c-strong-950">$0.00</p>
                </div>
            </template>
        </AppCard>
        <AppCard class="flex-1 w-full relative overflow-hidden">
            <template #header>
                <div class="flex items-center">
                    <SpeedUpIcon color="var(--sub-600)" />
                    <div class="flex-1 ms-2"><p class="label-sm c-strong-950 !text-start">Ödeme Bilgileri</p></div>
                    <IconButton class="border border-soft-200 rounded-lg">
                        <EditLineIcon color="var(--sub-600)" />
                    </IconButton>
                </div>
            </template>

            <template #body>
                <hr class="my-3">
                <div class="flex items-center mb-2">
                    <BankLineIcon color="var(--sub-600)" />
                    <span class="flex-1 ms-2 paragraph-sm c-sub-600">IBAN</span>
                    <span class="label-sm c-strong-950">TR 4785 **** **** 1234</span>
                </div>
                <div class="flex items-center">
                    <img width="16" src="@/assets/images/circular_color_image.png" />
                    <span class="flex-1 ms-2 paragraph-sm c-sub-600">E-Mail</span>
                    <span class="label-sm c-strong-950">info@g***.com</span>
                </div>
                <div class="bg-[#F2F5F8] absolute left-0 right-0 bottom-0 flex items-center justify-start py-1 ps-4">
                    <span class="paragraph-xs c-sub-600">Değişiklik için Payooner hesabınıza gitmelisiniz.</span>
                </div>

            </template>
        </AppCard>

    </div>
    <AppTable :showAddButton="false"  :buttonLabel="__('control.finance.add_new')" ref="pageTable" :config="appTableConfig"
              v-model="usePage().props.payment_requests" @addNewClicked="openDialog">
        <template #tableHeader>
          <p class="subheading-regular c-strong-950">  İşlem Tarihçesi</p>
        </template>
      <AppTableColumn :label="__('control.finance.payments.table.column_1')" align="left" sortable="name">
        <template #default="scope">


        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.table.column_2')" sortable="name" width="140">
        <template #default="scope">

        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.finance.payments.table.column_3')" sortable="name">
        <template #default="scope">

        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.finance.payments.table.column_4')" sortable="name">
        <template #default="scope">

        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.table.column_5')" sortable="name">
        <template #default="scope">

        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.table.column_6')" align="right">
        <template #default="scope">

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

    <WithdrawModal  @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton,RegularButton} from '@/Components/Buttons'
import {StatusBadge} from '@/Components/Badges'
import {AddIcon, LabelsIcon,WalletIcon,DownloadIcon, BankLineIcon, TrashIcon, EditIcon, ExitIcon, WalletLineIcon,SpeedUpIcon,EditLineIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {WithdrawModal} from '@/Components/Dialog';
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();
const pageTable = ref();

const props = defineProps({
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})


const data = ref([])
const choosenLabel = ref(null);
const isModalOn = ref(false);
const openPaymentModal = () => {

    isModalOn.value = !isModalOn.value;
}

const deleteRow = (row) => {
  pageTable.value.removeRowDataFromRemote(row);
}
const editRow = (label) => {

  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}
const onDone = (e) => {
  pageTable.value.addRow(e);
}

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})
const onUpdate = (e) => {
  pageTable.value.editRow(e);
}
</script>

<style lang="scss" scoped>

</style>
