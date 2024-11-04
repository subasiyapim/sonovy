<template>

  <div class="pb-6">
            <p class="label-xl c-strong-950">
              Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
            </p>
        </div>

<div class="flex flex-col gap-6">
    <SectionHeader title="YAYIN VE TANITIM METNİ"></SectionHeader>
    <div class="flex flex-col gap-6" v-for="(info,index) in form.promotion_info">
        <FormElement label-width="190px" :error="form.errors[`promotion_info.${index}.language_id`]" type="select" label="Tanıtım Dil" v-model="info.language_id" :required="true" placeholder="Lütfen Seçiniz" :config="selectConfig">
        </FormElement>
        <FormElement label-width="190px"  :error="form.errors[`promotion_info.${index}.title`]" type="text" label="Vurucu Cümle" v-model="info.title" placeholder="Yapım Yılı" >
        </FormElement>
        <FormElement label-width="190px"  :error="form.errors[`promotion_info.${index}.promotion_text`]" type="textarea" :required="true" v-model="info.promotion_text" label="Yayın Tanıtım Metni" placeholder="Metni giriniz">
        </FormElement>
    </div>
    <div>
        <button @click="form.promotion_info.push({})" class="flex items-center justify-center gap-2 w-auto">
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
