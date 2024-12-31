<template>

<BaseDialog width="400px" v-model="isDialogOn"  height="min-content" align="center" title="Para Çek"
              description="Mevcut Bakiye. $6.240,28">
    <template #icon>
      <BankLineIcon color="var(--dark-green-950)"/>
    </template>

    <div class="h-32 flex items-center flex-col justify-center">
        <p class="paragraph-xs c-sub-600">Tutar Giriniz</p>
        <input v-model="amount" class="card-currency-header c-strong-950 placeholder:text-[#CACFD8] !text-center border-none focus:ring-0 " placeholder="$0.00"></input>
    </div>
    <SectionHeader :title="'TUTAR BİLGİLERİ'"/>
    <div class="bg-[#F2F5F8] flex items-center rounded gap-2 my-2 mx-3 p-2">
        <InfoFilledIcon color="var(--sub-600)" />
        <p class="paragraph-xs c-sub-600">En Az $50 ve üstü çekim yapabilirsiniz</p>
    </div>
    <hr>
    <div class="p-5 flex gap-4 my-2">
        <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <BankLineIcon color="var(--sub-600)" />
        </div>
        <div class="flex flex-col flex-1">
            <p class="label-sm c-strong-950">Banka bilgileri</p>
            <p class="paragraph-xs c-sub-600">TR *** **** ****9876</p>
        </div>
        <button class="label-sm c-dark-green-500">
            Düzenle
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
  </BaseDialog>


</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {BankLineIcon,InfoFilledIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {FormElement} from '@/Components/Form'
import {useForm, usePage} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';

const props = defineProps({
  modelValue: {
    default: false,
  },
})
const crudStore = useCrudStore();

const adding = ref(false)
const amount = ref(null);
const emits = defineEmits(['update:modelValue', 'done','update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const onSubmit = async (e) => {
    try {
        const response  = await crudStore.post(route('control.finance.payments.store'),{
            amount:amount.value,
            process_type:1,
        })
    } catch (error) {
        console.log("ERROR",error.response.data);
    // error.response
     toast.error(error.response.data.message);
    }
}
const checkIfDisabled = computed(() => {

  return !amount.value

})

</script>
