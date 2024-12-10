<script setup>
import {computed,ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore'

import {More2LineIcon,TopRightArrowIcon,RemoveUserIcon,TrashIcon,EditIcon} from '@/Components/Icons';
const getBody = computed(() => {
    return document.querySelector('body');
});

const crudStore = useCrudStore();
const props = defineProps({
    user:{},
})
const emits = defineEmits(['onUserDetached','updateUser']);
const myTippy = ref();
const onUnAssignUser = () => {

    crudStore.post(route('control.user-management.users.detach-parent',props.user.id));
    emits('onUserDetached')
    onCancel();
}
const onGotoUserDetail = () => {


    router.visit(route('control.user-management.users.show',props.user.id))
    onCancel();
}
const onRemoveUser = () => {
     router.visit(route('control.user-management.users.destroy',props.user.id), { method: 'delete'});
    //   router.post(route('control.user-management.users.destroy',props.user.id))
    onCancel();
};


const onUpdateModalOn = () => {
    emits('updateUser');
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
                <p class="label-sm !font-bold c-sub-600 mb-2 px-3">İşlemler</p>
                <button @click="onUnAssignUser" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><RemoveUserIcon color="var(--sub-600)" />Alt Kullanıcıyı Kaldır</button>
                <button @click="onGotoUserDetail" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><TopRightArrowIcon color="var(--sub-600)" />Kullanıcı Detayına Git</button>
                <button @click="onUpdateModalOn" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><EditIcon color="var(--sub-600)" />Kullanıcı Düzenle</button>
                <button @click="onRemoveUser" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><TrashIcon color="var(--sub-600)" />Kullanıcıyı Sil</button>
            </div>
        </template>
    </tippy>

</template>

<style scoped>

</style>
