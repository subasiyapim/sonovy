<template>

  <div class="pb-6">
            <p class="label-xl c-strong-950">
               Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
            </p>
        </div>
<div class="">

<div class="flex flex-col gap-6">
    <SectionHeader title="YAYINLANMA TARİHLERİ"></SectionHeader>


        <FormElement label-width="190px" :error="form.errors.production_year" v-model="form.production_year" type="select" label="Yapım Yılı" placeholder="Yapım Yılı" :config="selectConfig">

        </FormElement>
        <FormElement label-width="190px" :error="form.errors.is_published_before" v-model="form.is_published_before" @change="onChangeIsPublishedBefore" type="fancyCheck" label="Daha önce Yayınlandı mı?" placeholder="Daha önce Yayınlandı mı?">

        </FormElement>
        <FormElement label-width="190px" :error="form.errors.publish_year" :disabled="form.is_published_before" v-model="form.publish_year" type="select" label="Yayınlanma Yılı" placeholder="Yayınlanma Yılı" :config="selectConfig">

        </FormElement>


    <SectionHeader title="ÜLKE VE BÖLGE TERCİHLERİ"></SectionHeader>

        <FormElement label-width="190px"  :error="form.errors.published_country_type" v-model="form.published_country_type" type="radio" label="Tercihler"  :config="countryRadioConfig">

        </FormElement>
        <div class="flex">
            <div class="w-[190px] label-sm c-strong-950">Yayınlanacak Ülkeler</div>
            <div class="flex flex-col w-full gap-3">

                <div class="w-full" v-for="(value,key) in usePage().props.countriesGroupedByRegion">
                        <AppAccordion :title="key" description="Tüm ülkeler seçildi">

                            <div class="flex items-center ">
                                <div class="flex-1">
                                    <p class="label-medium c-strong-950 !text-start">Ülkeler</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click.stop="chooseAll(key)" class="c-blue-500 label-xs hover:underline">Tümünü Seç</button>
                                    <button @click.stop="unChooseAll(key)" class="c-blue-500 label-xs hover:underline">Seçimi Kaldır</button>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="grid grid-cols-3 gap-2 ">
                                <div v-for="country in value">
                                    <label @click.stop class="flex items-center gap-2 items-center">
                                        <input type="checkbox" @click="onCountryCheck(country)" :checked="form.published_countries?.find((el) => el ==country.value)" class="focus:ring-0 rounded appCheckbox border border-soft-200" />

                                        {{country.iconKey}}
                                        <span class="paragraph-xs c-strong-950">{{country.label}}</span>
                                    </label>
                                </div>
                            </div>
                        </AppAccordion>
                </div>
            </div>
        </div>
    <SectionHeader title="PLATFORM TERCİHLERİ"></SectionHeader>

    <AppTable v-model="form.platforms" @selectionChange="onPlatformSelected" :isClient="true" :hasSelect="true"  :hasSearch="false" :showAddButton="false">

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
                    <AppTextInput v-model="scope.row.download_price" placeholder="0.00"> </AppTextInput>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Ön Sipariş Tarihi">

                <template #default="scope">

                    <VueDatePicker v-model="scope.row.pre_order_date" class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
                        <template #input-icon>
                        <div class="p-3">
                                <CalendarIcon color="var(--sub-600)"/>
                        </div>
                        </template>
                    </VueDatePicker>
                 </template>
            </AppTableColumn>

            <AppTableColumn label="Yayın Tarihi" align="end">

                <template #default="scope">
                    <VueDatePicker    v-model="scope.row.publish_date" class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
                        <template #input-icon>
                        <div class="p-3">
                                <CalendarIcon color="var(--sub-600)"/>
                        </div>
                        </template>
                    </VueDatePicker>
                </template>
            </AppTableColumn>


        </AppTable>

</div>




</div>
</template>

<script setup>
import {SectionHeader,AppAccordion} from '@/Components/Widgets';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref,onBeforeMount} from 'vue';
import {FormElement,AppTextInput} from '@/Components/Form';
import {AddIcon,Icon,CalendarIcon} from '@/Components/Icons'
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
// const platforms = ref(usePage().props.platforms);

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
        options: usePage().props.product_published_country_types

        // [
        //     {value:1,label:"Tüm ülkelerde yayınlansın"},
        //     {value:2,label:"Seçilenler hariç tüm ülkelerde yayınlansın"},
        //     {value:3,label:"Sadece seçilen ülkelerde yayınlansın"},
        // ]
    }
})

const onChangeIsPublishedBefore = (e) => {
    if(!e){
        form.value.publish_year = null;
    }
}


const onCountryCheck = (e) => {
    const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
    if(findedIndex >= 0){
        form.value.published_countries.splice(findedIndex,1);
    }else {
        form.value.published_countries.push(e.value);
    }
}

const chooseAll = (key) => {
    usePage().props.countriesGroupedByRegion[key].forEach((e) => {
        const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
        if(findedIndex < 0){
            form.value.published_countries.push(e.value);
        }
    });
}
const unChooseAll = (key) => {
    usePage().props.countriesGroupedByRegion[key].forEach((e) => {
        const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
        if(findedIndex >= 0){
            form.value.published_countries.splice(findedIndex,1);
        }
    });
}

const onPlatformSelected = (rows) => {
    form.value.platforms.forEach(element => {
       element.isSelected = null;
    });
    rows.forEach(element => {

        const finded = form.value.platforms.find((el) => el == element);
        if(finded)
            finded.isSelected = true;

    });
//    form.value.platforms = rows.map((row) =>  { return {id:row.value,download_price:0,pre_order_date:null,publish_date:null}});
}
onBeforeMount(() => {
    form.value.platforms = usePage().props.platforms;
    if(form.value.published_country_type){
        form.value.published_countries = [];
        Object.keys(usePage().props.countriesGroupedByRegion).forEach((key) => {
            usePage().props.countriesGroupedByRegion[key].forEach((e) => {
                const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
                if(findedIndex < 0){
                    form.value.published_countries.push(e.value);
                }
            });
        })


    }

});
</script>

<style lang="scss" scoped>

</style>
