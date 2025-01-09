<script setup>
import {computed,ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore'

import {More2LineIcon,TopRightArrowIcon,RemoveUserIcon,TrashIcon} from '@/Components/Icons';
const getBody = computed(() => {
    return document.querySelector('body');
});

const crudStore = useCrudStore();
const props = defineProps({
    user:{},
    label:{}
})
const emits = defineEmits(['onDetached']);
const myTippy = ref();
const onUnAssignProduct = () => {

    crudStore.post(route('control.user-management.users.detach-label',props.label.id));
    emits('onDetached')
    onCancel();
}
const onGoToDetail = () => {


    router.visit(route('control.catalog.labels.show',props.label.id))
    onCancel();
}



const onCancel = () => {
    myTippy.value?.hide();
}
</script>

<template>
    <tippy ref="myTippy" :allowHtml="true" :trigger="'click'" theme="light" :sticky="true" :interactive="true" :appendTo="getBody">
        <button class="bg-weak-50  w-8 h-8 rounded-lg flex items-center justify-center">
            <More2LineIcon color="var(--sub-600)"/>
        </button>
        <template #content>
            <div class="flex flex-col py-3 px-1">
                <p class="label-sm !font-bold c-sub-600 mb-2 px-3">{{ __('control.general.actions') }}</p>
                <button @click="onUnAssignProduct" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><RemoveUserIcon color="var(--sub-600)" /> {{ __('control.label.detach_label') }}</button>
                <button @click="onGoToDetail" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><TopRightArrowIcon color="var(--sub-600)" /> {{ __('control.label.go_to_detail') }}</button>
            </div>
        </template>
    </tippy>
</template>

<style scoped>

</style>
