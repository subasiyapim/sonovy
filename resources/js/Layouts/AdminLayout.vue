<template>
  <Head :title="title"/>
    <div class="flex h-screen w-full flex-1 flex-col md:flex-row ">

        <Sidebar>
        </Sidebar>
        <div class="flex-1 relative overflow-scroll">
            <div class="flex items-center staticTopInfo">
                <div class="flex items-center gap-3.5 flex-1">
                       <IconButton @click="goBack" hasBorder size="medium">
                            <ArrowLeftIcon color="var(--sub-600)" />
                       </IconButton>
                       <div class="flex flex-col flex-1">
                            <p class="label-lg c-strong-950">{{title}}</p>
                            <div v-if="!hasSlot('breadcrumb')" class="flex items-center gap-2">
                                <span v-if="parentTitle" class="label-xs c-soft-400">{{parentTitle}}</span>
                                <span v-if="parentTitle" class="label-xs c-soft-400">•</span>

                                <span v-if="subParent" class="label-xs c-soft-400">{{subParent}}</span>
                                <span v-if="subParent" class="label-xs c-soft-400">•</span>
                                <span class="label-xs c-soft-400">{{title}}</span>
                            </div>
                            <div v-else class="flex items-center gap-2">
                                <slot name="breadcrumb" />
                            </div>
                       </div>
                       <slot name="toolbar" />
                         <IconButton>
                             <SearchIcon color="var(--sub-600)" />
                        </IconButton>
                        <IconButton>
                            <NotificationIcon color="var(--sub-600)" />
                        </IconButton>
                        <div class="w-[229px]" v-if="showDatePicker">


                            <VueDatePicker  @cleared="onDateCleared" @range-end="onDateChaned"  range v-model="choosenDate" class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
                                <template #input-icon>
                                <div class="p-3">
                                        <CalendarIcon color="var(--sub-600)"/>
                                </div>
                                </template>
                            </VueDatePicker>
                       </div>

                </div>

            </div>
           <div class="" :class="hasPadding ? 'px-8 pt-6 pb-10' :'' ">
                 <slot />
           </div>


        </div>
    </div>


</template>

<style>
.searchResultWrapper .el-card__body {
    padding: 10px !important;
}

.searchResultWrapper .el-card {
    border-radius: 8px;
}
</style>


<script setup>
import {computed, onMounted,ref,nextTick,useSlots} from 'vue'
import Sidebar from '@/Layouts/Partials/Sidebar.vue';
import {SecondaryButton,IconButton} from '@/Components/Buttons'
import {ArrowLeftIcon,SearchIcon,NotificationIcon,CalendarIcon} from '@/Components/Icons';
import AppTextInput from '@/Components/Form/AppTextInput.vue';
import {router, usePage} from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
const choosenDate = ref();
const emits = defineEmits(['dateChoosen'])
const props = defineProps({
    title:{type:String},
    parentTitle:{type:String},
    hasPadding:{
        default:true,
    },
    showDatePicker:{
        default:true
    },
    subParent:{
        default:null
    }
})
const slots = useSlots()
const hasSlot = (name) => {
    return !!slots[name];
}
let params = new URLSearchParams(window.location.search)

if (params.get('e_date') && params.get('s_date')) {
    choosenDate.value = [params.get('s_date'),params.get('e_date')]
}
const onDateChaned = (e) => {
  nextTick(() => {
    emits('dateChoosen',choosenDate.value);
  })
}
const goBack = () => {
    window.history.back();
}
const onDateCleared = (e) => {

   emits('dateChoosen',null);
}


onMounted(() => {

});

</script>

