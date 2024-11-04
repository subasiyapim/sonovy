<template>

  <div class="pb-6">
            <p class="label-xl c-strong-950">
               Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
            </p>
        </div>
<div class="">

<div class="flex flex-col gap-6">
    <SectionHeader title="YAYINLANMA TARİHLERİ"></SectionHeader>
        <FormElement label-width="190px" v-model="form.production_year" type="select" label="Yapım Yılı" placeholder="Yapım Yılı" :config="selectConfig">

        </FormElement>
        <FormElement label-width="190px" v-model="form.is_published_before" @change="onChangeIsPublishedBefore" type="fancyCheck" label="Daha önce Yayınlandı mı?" placeholder="Daha önce Yayınlandı mı?">

        </FormElement>
        <FormElement label-width="190px" :disabled="form.is_published_before" v-model="form.publish_year" type="select" label="Yayınlanma Yılı" placeholder="Yayınlanma Yılı" :config="selectConfig">

        </FormElement>

    <SectionHeader title="ÜLKE VE BÖLGE TERCİHLERİ"></SectionHeader>
        <FormElement label-width="190px" type="radio" label="Tercihler" v-model="countryRadioValue" :config="countryRadioConfig">

        </FormElement>
        <div class="flex">
            <div class="w-[190px] label-sm c-strong-950">Yayınlanacak Ülkeler</div>
            <div class="w-full">
                    <AppAccordion title="Güney Amerika" description="Tüm ülkeler seçildi">
                        sdasd
                    </AppAccordion>
            </div>
        </div>
    <SectionHeader title="PLATFORM TERCİHLERİ"></SectionHeader>

    <AppTable v-model="platforms"  :isClient="true" :hasSelect="true"  :hasSearch="false" :showAddButton="false">

            <AppTableColumn label="Platform">
                <template #default="scope">
                <div class="flex items-center justify-center gap-2">
                        <Icon :icon="scope.row.iconKey" />

                        <p class="label-sm c-solid-950">
                        {{scope.row.label}}
                        </p>
                </div>

                </template>
            </AppTableColumn>

            <AppTableColumn label="İndirme Fiyatı">
                <template #default="scope">
                    <AppTextInput placeholder="0.00"> </AppTextInput>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Ön Sipariş Tarihi">
                 <AppTextInput placeholder="0.00"> </AppTextInput>
            </AppTableColumn>

            <AppTableColumn label="Yayın Tarihi" align="end">
                 <AppTextInput placeholder="0.00"> </AppTextInput>
            </AppTableColumn>


        </AppTable>

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
const platforms = ref(usePage().props.platforms)

function getLast100Years() {
  const currentYear = new Date().getFullYear();
  const years = [];

  for (let i = 0; i < 100; i++) {
    years.push({
        "value" : currentYear - i,
       "label": currentYear - i
    });
  }

  return years;
}
const selectConfig = computed(() => {
    return {
        hasSearch:false,
        data: getLast100Years(),
    }
})


const countryRadioConfig = computed(() => {
    return {
        optionDirection:'vertical',
        options: [
            {value:1,label:"Tüm ülkelerde yayınlansın"},
            {value:2,label:"Seçilenler hariç tüm ülkelerde yayınlansın"},
            {value:3,label:"Sadece seçilen ülkelerde yayınlansın"},
        ]
    }
})

const onChangeIsPublishedBefore = (e) => {
    if(!e){
        form.value.publish_year = null;
    }
}
</script>

<style lang="scss" scoped>

</style>
