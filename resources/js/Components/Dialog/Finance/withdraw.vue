<template>

  <BaseDialog :width="isFinal ? '600px' :'400px'" v-model="isDialogOn" :showHeader="!isFinal" height="min-content"
              align="center" title="Para Çek"
            >
    <template #icon>
      <BankLineIcon color="var(--dark-green-950)"/>
    </template>
      <SectionHeader :title="'TUTAR BİLGİLERİ'"/>
    <template #description>
        <div class="flex gap-0.5">
            <p class="paragraph-xs c-sub-600">Mevcut Bakiye </p>
            <p class="paragraph-xs c-strong-950 !font-bold ">{{usePage().props.balance}}</p>
        </div>

    </template>

    <template v-if="!isFinal">
      <div class="h-32 flex items-center flex-col justify-center">
        <p class="paragraph-xs c-sub-600">Tutar Giriniz</p>
      <input
        ref="inputRef"
        v-model="formattedAmount"
        class="card-currency-header c-strong-950 placeholder:text-[#CACFD8] !text-center border-none focus:ring-0"
        placeholder="$0.00"
        @input="updateAmount"
    />
      </div>

      <div class="bg-[#F2F5F8] flex items-center rounded gap-2 my-2 mx-3 p-2">
        <InfoFilledIcon color="var(--sub-600)"/>
        <p class="paragraph-xs c-sub-600">En Az ${{ usePage().props.minPaymentRequest }} ve üstü çekim
          yapabilirsiniz</p>
      </div>
      <hr>
      <div class="p-5 flex gap-4 my-2">
        <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
          <BankLineIcon color="var(--sub-600)"/>
        </div>
        <div class="flex flex-col flex-1 justify-center">
          <p v-if="usePage().props.account" class="label-sm c-strong-950">Banka bilgileri</p>
          <p v-if="usePage().props.account" class="paragraph-xs c-sub-600">{{ usePage().props.account.iban }}</p>
          <p v-else class="label-sm c-strong-950">Banka bilgisi ekle</p>
        </div>
        <button @click="openBankAccountModal" class="label-sm c-dark-green-500">
          <template v-if="usePage().props.account"> Düzenle</template>
          <template v-else> Ekle</template>
        </button>
      </div>
      <hr>

      <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
        <RegularButton @click="isDialogOn = false" class="flex-1">
          {{ __('control.general.cancel') }}
        </RegularButton>
        <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
          Talebi Gönder
        </PrimaryButton>
      </div>
    </template>
    <template v-else>
      <div class="flex flex-col items-center gap-3 px-5 pt-16 pb-6">
        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-dark-green-700">
          <CheckIcon color="var(--dark-green-500)"/>
        </div>
        <p class="label-xl c-strong-950 !text-center">
          $ {{ amount }} para çekme balebiniz başarılı bir şekilde iletildi.
        </p>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2">
            <div class="w-5 h-5 rounded-full bg-dark-green-700 flex items-center justify-center">
              <CheckIcon color="#fff"/>
            </div>
            <p class="c-strong-950 paragraph-sm">Talep Edildi</p></div>
          <ChevronRightIcon color="var(--sub-600)"/>
          <div class="flex items-center gap-2">
            <div
                class="w-5 h-5 rounded-full bg-[#FF8447] flex items-center justify-center text-white text-sm font-medium">
              2
            </div>
            <p class="c-strong-950 paragraph-sm">İnceleniyor</p></div>
          <ChevronRightIcon color="var(--sub-600)"/>
          <div class="flex items-center gap-2">
            <div
                class="w-5 h-5 rounded-full bg-white border border-soft-200 flex items-center justify-center c-sub-600 text-sm font-medium">
              3
            </div>
            <p class="c-sub-600 paragraph-sm">Tutar Gönderilecek</p></div>
        </div>
        <p class="c-neutral-500 paragraph-sm !text-center">Ödeme isteğiniz ekibimiz tarafından onaylanacak ve sonrasında
          gönderilecek.</p>
        <RegularButton @click="onCloseModal" class="mt-5">Tamam</RegularButton>
      </div>

    </template>
  </BaseDialog>

  <BankAccountModal :account="usePage().props.account" @update="onUpdate" @done="onDone" v-if="isBankAccountModalOn"
                    v-model="isBankAccountModalOn"/>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {BankLineIcon, InfoFilledIcon, CheckIcon, ChevronRightIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted,nextTick} from 'vue';
import {FormElement} from '@/Components/Form'
import {useForm, usePage} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';
import {BankAccountModal} from '@/Components/Dialog';

const isBankAccountModalOn = ref(false);
const isFinal = ref(false)
const props = defineProps({
  modelValue: {
    default: false,
  },
  account: {}
})
const crudStore = useCrudStore();

const adding = ref(false)
const amount = ref(null);
const emits = defineEmits(['update:modelValue', 'done', 'update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const onUpdate = (e) => {
  usePage().props.account = e;
}

const inputRef = ref(null); // Ref for the input element

const formattedAmount = computed({
  get() {
    if (amount.value === null) return "";
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(amount.value);
  },
  set(value) {
    const numericValue = parseFloat(value.replace(/[^0-9.-]+/g, ""));
    amount.value = isNaN(numericValue)
      ? null
      : parseFloat(numericValue.toFixed(2));
  },
});

const updateAmount = (event) => {
  const input = event.target;
  const cursorPosition = input.selectionStart;

  // Update the formattedAmount based on input
  formattedAmount.value = input.value;

  // Get the new formatted value
  const formattedValue = formattedAmount.value;

  // Reposition the cursor
  nextTick(() => {
    if (inputRef.value) {
      const decimalIndex = formattedValue.indexOf(".");
      const newCursorPosition =
        decimalIndex === -1 ? formattedValue.length : decimalIndex;
      inputRef.value.setSelectionRange(newCursorPosition, newCursorPosition);
    }
  });
};


const openBankAccountModal = () => {

  isBankAccountModalOn.value = !isBankAccountModalOn.value;
}
const onSubmit = async (e) => {
  try {
    const response = await crudStore.post(route('control.finance.payments.store'), {
      amount: amount.value,
      account_id: usePage().props.account.id,
      process_type: 1
    })

    isFinal.value = true;
  } catch (error) {
    console.log("ERROR", error.response.data);
    // error.response
    toast.error(error.response.data.message);
  }
}
const checkIfDisabled = computed(() => {

  return !amount.value

})
const onDone = (e) => {
    usePage().props.account = e;

}
const onCloseModal = () => {
  isDialogOn.value = false;
  location.reload();
}
</script>
