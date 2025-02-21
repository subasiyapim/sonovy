<template>

<BaseDialog  v-model="isDialogOn" width="462px" height="min-content" align="center" title="İçe Rapor Aktar"
              description="Bilgileri doldurarak içe rapor aktarın">
    <template #icon>
      <UploadIcon color="var(--dark-green-950)"/>
    </template>



    <div class="flex flex-col gap-2 px-5 py-5">
        <FormElement
                :required="true"
                   label-width="190px"
                    direction="vertical"
                    v-model="form.platform_id"
                    :label="__('control.finance.imports.fields.platform_id')"
                    :config="platformConfig"
                    :placeholder="'Lütfen seçiniz'"
                    type="select">

            <template #option="scope">
                <span v-html="scope.data.icon"></span>
                <span class="paragraph-sm c-strong-950">{{ scope.data.name }}</span>
            </template>
            <template #model="scope">

                <div v-if="scope.data" class="flex items-center gap-2">
                    <span v-html="platformConfig.data.find((el) => el.id == scope.data)?.icon"></span>
                    <span class="paragraph-sm c-strong-950 ms-1">{{ platformConfig.data.find((el) => el.id == scope.data)?.name }}</span>
                </div>
            </template>
        </FormElement>
        <FormElement label-width="190px" direction="vertical" v-model="form.name" :label="__('control.finance.imports.fields.reports_name')" placeholder="Lütfen giriniz"></FormElement>
            <div class="flex flex-col mb-4">
                <span class="label-sm c-strong-950 mb-0">Rapor Tarihi</span>
                <VueDatePicker hide-input-icon v-model="form.report_date" class="!rounded-sm h-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">

                </VueDatePicker>
            </div>
          <DragUploadInput uploadType="file" ref="imageUploadFile"  @change="onChange" label="Choose a file or drag & drop it here."
                   note="csv, xlsx "></DragUploadInput>

    </div>



    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
      <RegularButton @click="isDialogOn = false" class="flex-1">
        {{ __('control.general.cancel') }}
      </RegularButton>
      <PrimaryButton :loading="adding" @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
       <template v-if="account"> Güncelle  </template>
       <template v-else> Kaydet  </template>
      </PrimaryButton>
    </div>
  </BaseDialog>


</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {UploadIcon,InfoFilledIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {FormElement} from '@/Components/Form'
import {useForm, usePage} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';

const imageUploadFile = ref();
import {DragUploadInput} from '@/Components/Form';

const props = defineProps({
  modelValue: {
    default: false,
  },
  account:{
    default:null
  }
})
const crudStore = useCrudStore();

const adding = ref(false)

const emits = defineEmits(['update:modelValue', 'done','update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const form = ref({

    name:null,
    file:null,
    platform_id:null,
    report_date:null
});

const platformConfig = computed(() => {
  return {
    value:'id',
    data: usePage().props.platforms,
  };
})

const onChange = (e) => {
    form.value.file = e;
    console.log("EE",e);

    imageUploadFile.value.showImage = true;
}
const onSubmit = async (e) => {
    adding.value = true;
    try {


        const response  = await crudStore.formData(route('control.finance.reports.uploadFile'),{
            ...form.value
        })

        toast.success("işlem başarlı");


        isDialogOn.value = false;
        adding.value = false;


    } catch (error) {
        if(error.response?.data){
             toast.error(error.response.data.message);
        }

       adding.value = false;

    // error.response

    }
}
const checkIfDisabled = computed(() => {

  return false;

})

</script>
