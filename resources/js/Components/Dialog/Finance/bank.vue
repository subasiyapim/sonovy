<template>

<BaseDialog  v-model="isDialogOn"  height="min-content" align="center" title="Banka Bilgileri"
              description="Banka bilgilerini girebilirsiniz">
    <template #icon>
      <BankLineIcon color="var(--dark-green-950)"/>
    </template>


    <SectionHeader :title="'TEMEL BİLGİLERİ'"/>
    <div class="flex flex-col gap-5 px-5 py-5">
            <FormElement label-width="190px" v-model="form.title" label="Başlık" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px"
                   :required="true"
                        v-model="form.country_id"
                        :label="__('control.artist.fields.country')"
                        :config="countryConfig"
                        :placeholder="__('control.artist.fields.country_placeholder')"
                        type="select">

                <template #option="scope">
                <span>{{ scope.data.iconKey }}</span>
                <span class="paragraph-sm c-strong-950">{{ scope.data.label }}</span>
                </template>
                <template #model="scope">
                <div v-if="scope.data" class="flex items-center gap-2">
                    <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.iconKey }}</span>
                    <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.label }}</span>
                </div>
                </template>
            </FormElement>
            <FormElement label-width="190px" v-model="form.name"  label="İsim" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" v-model="form.iban"  label="İban" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" v-model="form.swift_code"  label="Swift Code" placeholder="Lütfen giriniz"></FormElement>
    </div>



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

const emits = defineEmits(['update:modelValue', 'done','update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const form = ref({

});

const countryConfig = computed(() => {
  return {
    data: usePage().props.countries,
  };
})
const onSubmit = async (e) => {
    try {
        const response  = await crudStore.post(route('control.bank.account.store'),{
           ...form.value
        })
    } catch (error) {
        if(error.response?.data){
             toast.error(error.response.data.message);
        }
       console.log("ERROR",error);

    // error.response

    }
}
const checkIfDisabled = computed(() => {

  return false;

})

</script>
