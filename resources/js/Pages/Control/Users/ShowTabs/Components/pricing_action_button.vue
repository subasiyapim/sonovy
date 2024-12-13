<script setup>
import {computed,ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {useCrudStore} from '@/Stores/useCrudStore'
import {ChangePackageIcon,More2LineIcon} from '@/Components/Icons';

const getBody = computed(() => {
    return document.querySelector('body');
});

const crudStore = useCrudStore();
const props = defineProps({
    user:{},
    label:{}
})
const emits = defineEmits(['onChangeClicked']);
const myTippy = ref();

const onChangePackage = () => {
    emits('onChangeClicked')
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
                <p class="label-sm !font-bold c-sub-600 mb-2 px-3">İşlemler</p>
                <button @click="onChangePackage" class="flex items-center gap-2 label-sm c-sub-600 py-3 rounded-lg px-3 hover:bg-[#FAF9F8]"><ChangePackageIcon color="var(--sub-600)" />Paketi Değiştir</button>
            </div>
        </template>
    </tippy>
</template>

<style scoped>

</style>
