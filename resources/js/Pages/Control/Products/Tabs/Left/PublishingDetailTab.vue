<template>

  <div class="pb-6">
            <p class="label-xl c-strong-950">
               Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
            </p>
        </div>
<div class="">

<div class="flex flex-col gap-6">
        <SectionHeader title="YAYINLANMA TARİHLERİ"></SectionHeader>
     <FormElement label-width="190px" type="select" label="Yapım Yılı" placeholder="Yapım Yılı" :config="selectConfig">

     </FormElement>
     <FormElement label-width="190px" type="fancyCheck" label="Daha önce Yayınlandı mı?" placeholder="Daha önce Yayınlandı mı?" :config="selectConfig">

     </FormElement>
     <FormElement label-width="190px" type="select" label="Yayınlanma Yılı" placeholder="Yayınlanma Yılı" :config="selectConfig">

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

    <AppTable v-model="platforms"  :isClient="true" :hasSearch="false" :showAddButton="false">
            <AppTableColumn label="#">
                <template #default="scope"> {{ scope.row }}</template>
            </AppTableColumn>
            <AppTableColumn label="Platform">
                <template #default="scope">{{scope.row}}</template>
            </AppTableColumn>

            <AppTableColumn label="İndirme Fiyatı">
                <template #default="scope">{{scope.row}}</template>
            </AppTableColumn>

            <AppTableColumn label="Ön Sipariş Tarihi">
                <template #default="scope">{{scope.row}}</template>
            </AppTableColumn>

            <AppTableColumn label="Yayın Tarihi" align="end">
                <template #default="scope">{{scope.row.name}}</template>
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
import {FormElement} from '@/Components/Form';
import {AddIcon} from '@/Components/Icons'
const countryRadioValue = ref(2)
const platforms = ref([])
const selectConfig = computed(() => {
    return {
        hasSearch:true,
        data: [
            {value:1,label:"MERHABA"},
            {value:2,label:"MERHABA"},
        ]
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

</script>

<style lang="scss" scoped>

</style>
