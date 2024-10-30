<template>

    <div class="flex h-screen overflow-hidden">
        <div class="flex-1 flex flex-col">
            <div class="flex-1 relative   ">
                <div class="flex items-center staticTopInfo h-20">
                    <div class="flex items-center gap-3.5 flex-1 ">

                        <IconButton  hasBorder size="medium">
                                <ArrowLeftIcon color="var(--sub-600)" />
                        </IconButton>
                        <div class="flex flex-col flex-1">
                                <p class="label-lg c-strong-950">Tüm Yayınlar</p>
                                <div class="flex items-center gap-2">
                                    <span class="label-xs c-soft-400">Katalog</span>
                                    <span class="label-xs c-soft-400">.</span>
                                    <span class="label-xs c-soft-400">Tüm Yayınlar</span>
                                </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="w-full h-full bg-white-500 flex flex-col gap-10 p-8 overflow-hidden">
                <AppStepper v-model="currentTab" @change="onChangeTab">
                    <AppStepperElement title="Yayın Bilgileri"></AppStepperElement>
                    <AppStepperElement title="Şarkı Detay"></AppStepperElement>
                    <AppStepperElement title="Yayınlama Detayları"></AppStepperElement>
                    <AppStepperElement title="Pazarlama ve Onay"></AppStepperElement>
                    <AppStepperElement title="Özet"></AppStepperElement>
                </AppStepper>

                <div class="h-full bg-white w-full shadow rounded-xl px-8 py-8 overflow-scroll">

                        <ProductInfoTab v-model="step1Element" :genres="genres" v-if="currentTab == 0"></ProductInfoTab>
                        <SongDetailTab v-if="currentTab == 1"></SongDetailTab>
                        <PublishingDetailTab v-if="currentTab == 2"></PublishingDetailTab>


                </div>
                <div class="flex items-center justify-center w-full">
                    <div class="flex-1 flex items-center gap-2 justify-center">
                        <p class="label-medium">%{{percent}} Tamamlandı</p>
                        <div class="w-48">
                            <AppProgressIndicator :value="percent" />
                        </div>
                    </div>
                    <PrimaryButton @click="submitStep">
                        Devam Et
                    </PrimaryButton>
                </div>
            </div>

        </div>
        <div class="w-80 border-l border-soft-200 h-full flex flex-col">
            <div class="flex items-center gap-3.5 px-5 py-4 h-20 border-b  border-soft-200">
                <div class="w-12 h-12 bg-dark-green-800 border border-soft-200 rounded-full flex items-center justify-center">
                        <StickyNoteFilledIcon color="var(--dark-green-500)" />
                </div>
                    <h1 class="label-lg c-strong-950">Yayın özeti</h1>


            </div>
            <div class="p-6 flex flex-col h-full gap-5 overflow-scroll">
                <ProductSummaryTab v-if="currentTab == 0"></ProductSummaryTab>
                <ProductSummaryTab v-if="currentTab == 1"></ProductSummaryTab>
                <ProductSummaryTab v-if="currentTab == 2"></ProductSummaryTab>

            </div>
        </div>

    </div>


</template>

<script setup>
import {computed,ref,onMounted} from 'vue';
import {IconButton} from '@/Components/Buttons'
import {FormElement} from '@/Components/Form';

import {ArrowLeftIcon,SearchIcon,ChevronRightIcon,StickyNoteFilledIcon,AddIcon,BroadcastTitleIcon,PersonIcon,GenreIcon,CalendarIcon, DurationIcon} from '@/Components/Icons';
import {router} from '@inertiajs/vue3';
import ProductInfoTab from './Tabs/Left/ProductInfoTab.vue'
import ProductSummaryTab from './Tabs/Right/ProductSummaryTab.vue'
import {useForm} from '@inertiajs/vue3';

import SongDetailTab from './Tabs/Left/SongDetailTab.vue'
import SongSummaryTab from './Tabs/Right/SongSummaryTab.vue'
import {PrimaryButton} from '@/Components/Buttons'
import {AppProgressIndicator} from '@/Components/Widgets'
import PublishingDetailTab from './Tabs/Left/PublishingDetailTab.vue'
import {AppStepper,AppStepperElement} from '@/Components/Stepper';
import {useCrudStore} from '@/Stores/useCrudStore';


const crudStore = useCrudStore();
const props = defineProps({
    product:{},
    genres:{},
})

const onChangeTab = (e) => {
    console.log("DEĞİŞTİİİİ",e);
}

const step1Element = useForm({
    album_name:props.product.album_name,
    version:props.product.version,
    main_artists:[],
    featuring_artists:[],
    genre_id:props.product.genre_id,
    sub_genre_id:props.product.sub_genre_id,
    format:props.product.format,
    label_id:props.product.label_id,
    p_line:props.product.p_line,
    c_line:props.product.c_line,
    upc_code:props.product.upc_code,
    ean_code:props.product.ean_code,
    catalog_number:props.product.catalog_number,
    language_id:props.product.language_id,
    main_price:props.product.main_price,
});

const percent = computed(() => {
    let percent_value = 0;
    if(currentTab.value == 0){
        Object.keys(step1Element).forEach((key) => {

            if(step1Element[key]){

                percent_value++;
            }
        })
    }
    return percent_value;
});
const currentTab = ref(0);
const selectConfig = computed(() => {
    return {
        hasSearch:true,
        data: [
            {value:1,label:"MERHABA"},
            {value:2,label:"MERHABA"},
        ]
    }
})

const submitStep = async () => {
    if(currentTab.value == 0){
        step1Element.post(route('control.catalog.products.form.step1',props.product.id));

    }

}
onMounted(() => {

});

</script>

<style lang="scss" scoped>

</style>
