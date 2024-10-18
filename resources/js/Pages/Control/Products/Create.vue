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

                            <ProductInfoTab v-if="currentTab == 0"></ProductInfoTab>
                            <SongDetailTab v-if="currentTab == 1"></SongDetailTab>
                            <PublishingDetailTab v-if="currentTab == 2"></PublishingDetailTab>


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
import {computed,ref} from 'vue';
import {IconButton} from '@/Components/Buttons'
import {FormElement} from '@/Components/Form';

import {ArrowLeftIcon,SearchIcon,ChevronRightIcon,StickyNoteFilledIcon,AddIcon,BroadcastTitleIcon,PersonIcon,GenreIcon,CalendarIcon, DurationIcon} from '@/Components/Icons';
import {router} from '@inertiajs/vue3';
import ProductInfoTab from './Tabs/Left/ProductInfoTab.vue'
import ProductSummaryTab from './Tabs/Right/ProductSummaryTab.vue'

import SongDetailTab from './Tabs/Left/SongDetailTab.vue'
import SongSummaryTab from './Tabs/Right/SongSummaryTab.vue'


import PublishingDetailTab from './Tabs/Left/PublishingDetailTab.vue'
import {AppStepper,AppStepperElement} from '@/Components/Stepper';

const onChangeTab = (e) => {
    console.log("DEĞİŞTİİİİ",e);
}
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
</script>

<style lang="scss" scoped>

</style>
