
<template>
    <tippy  :maxWidth="440" ref="myTippy" theme="light" :allowHtml="true" :sticky="true" trigger="click"
            :interactive="true" :appendTo="getBody">
        <IconButton>
        <TrashIcon color="var(--sub-600)"/>
        </IconButton>
        <template #content>
        <ConfirmDeleteDialog @confirm="onDeleteSong" @cancel="onCancel"
                                title="Parçayı silmek istediğinze emin misin"
                                description="Yüklediğiniz parçalardan albümden silenecektir. Daha sonra tekrar yayınlanma öncesi albüme parça ekleyebilirsiniz."/>
        </template>
    </tippy>
</template>

<script setup>
    import {computed,ref} from 'vue';
import { ConfirmDeleteDialog} from '@/Components/Dialog';
import { IconButton} from '@/Components/Buttons';
import { TrashIcon} from '@/Components/Icons';

    const emits = defineEmits(['onDeleteSong'])
    const getBody = computed(() => {
        return document.querySelector('body');
    });
    const myTippy = ref();

    const onCancel = () => {
        myTippy.value?.hide();
    }
    const onDeleteSong = () => {
        onCancel()
        emits('onDeleteSong');
    }
</script>

<style  scoped>

</style>
