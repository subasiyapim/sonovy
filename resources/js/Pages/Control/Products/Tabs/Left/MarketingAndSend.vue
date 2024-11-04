<template>

  <div class="pb-6">
            <p class="label-xl c-strong-950">
              Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
            </p>
        </div>

<div class="flex flex-col gap-6">
    <SectionHeader title="YAYIN VE TANITIM METNİ"></SectionHeader>
    <div class="flex flex-col gap-6" v-for="t in publicationTexts">
        <FormElement label-width="190px" type="select" label="Tanıtım Dil" :required="true" placeholder="Lütfen Seçiniz" :config="selectConfig">
        </FormElement>
        <FormElement label-width="190px" type="text" label="Vurucu Cümle" placeholder="Yapım Yılı" >
        </FormElement>
        <FormElement label-width="190px" type="textarea" :required="true" label="Yayın Tanıtım Metni" placeholder="Metni giriniz">
        </FormElement>
    </div>
    <div>
        <button @click="publicationTexts.push({})" class="flex items-center justify-center gap-2 w-auto">
            <AddIcon color="var(--blue-500)" />
            <p class="label-xs c-blue-500">Farklı Dilde Tanıtım Metni Ekle</p>
        </button>
    </div>




</div>
</template>

<script setup>
import {SectionHeader,AppAccordion} from '@/Components/Widgets';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref} from 'vue';
import {FormElement,AppTextInput} from '@/Components/Form';
import {AddIcon,Icon} from '@/Components/Icons'
import { usePage} from '@inertiajs/vue3';
const props = defineProps({
    product:{},
    modelValue:{},
})

const emits = defineEmits(['update:modelValue']);

const form = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const countryRadioValue = ref(2)
const selectConfig = computed(() => {
    return {
        hasSearch:true,
        data: usePage().props.languages,
    }
})
const publicationTexts = ref([{}]);

</script>

<style lang="scss" scoped>

</style>
