<script setup>
import {computed,ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore'
import {ConfirmDeleteDialog} from '@/Components/Dialog'

import {More2LineIcon,TopRightArrowIcon,FileCloseLineIcon,TrashIcon} from '@/Components/Icons';
const getBody = computed(() => {
    return document.querySelector('body');
});

const crudStore = useCrudStore();
const props = defineProps({
    user:{},
    label:{}
})

const isConfirmDialogOn = ref(false);
const emits = defineEmits(['onDetached']);
const myTippy = ref();
const confirmTippy = ref();
const onUnAssignProduct = () => {

    console.log("confirm tippit",confirmTippy.value);

    confirmTippy.value?.hide();
    isConfirmDialogOn.value = false;
    crudStore.post(route('control.user-management.users.detach-label',props.label.id));
    emits('onDetached')
    onCancel();
}
const onGoToDetail = () => {

    confirmTippy.value?.hide();
    router.visit(route('control.catalog.labels.show',props.label.id))
    onCancel();
}



const onCancel = () => {
    confirmTippy.value?.hide();
    isConfirmDialogOn.value = false;
    myTippy.value?.hide();
};

const onConfirmTippyClose = () => {
        isConfirmDialogOn.value = false;
}
</script>

<template>
    <tippy ref="myTippy" :hideOnClick="!isConfirmDialogOn" :allowHtml="true" :trigger="'click'" theme="light"  :sticky="true" :interactive="true" :appendTo="getBody">
        <button class="bg-weak-50  w-8 h-8 rounded-lg flex items-center justify-center">
            <More2LineIcon color="var(--sub-600)"/>
        </button>
        <template #content>
            <div class="flex flex-col py-3 px-1">
                <p class="label-sm !font-bold c-sub-600 mb-2 px-3">{{ __('control.general.actions') }}</p>
                <tippy @hide="onConfirmTippyClose" ref="confirmTippy" theme="light" :allowHtml="true" :sticky="true" :trigger=" 'click'"
                    :appendTo="getBody" placement="bottom-start"
                    :interactive="true">
                        <button @click="isConfirmDialogOn = true" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><FileCloseLineIcon color="var(--sub-600)" /> {{ __('control.label.detach_label') }}</button>
                        <template #content>
                            <div @click.stop>
                                <ConfirmDeleteDialog @confirm="onUnAssignProduct" @cancel="onCancel"
                                                    :title="'Alt kullanıcıdan plak şirketini kaldırmak istediğinizden emin misiniz?'"
                                                    :description="'Alt kullanıcıdan plak şirketini kaldırmanız plak şirketini sistemden silmez.'"/>
                            </div>
                        </template>
                </tippy>
                <button @click="onGoToDetail" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><TopRightArrowIcon color="var(--sub-600)" /> {{ __('control.label.go_to_detail') }}</button>
            </div>
        </template>
    </tippy>
</template>

<style scoped>
:deep(.tippy-box) {
    max-width: none !important;
}
</style>
