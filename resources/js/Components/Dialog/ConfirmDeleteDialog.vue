
<template>
   <div class="p-5">
        <div class="flex items-start gap-3 ">

            <div class="w-10 h-10 rounded flex items-center justify-center bg-red-50">
                <WarningIcon color="var(--error-500)" />
            </div>

            <div class="flex-1">
                <p class="label-medium c-strong-950 !text-start">{{title}}</p>
                <p class="c-sub-600 paragraph-sm !text-start">{{description}}</p>
            </div>
        </div>
        <hr class="my-4">
        <div class="flex items-center justify-end gap-2">
            <RegularButton @click="$emit('cancel')">Vazgeç</RegularButton>
            <DangerButton @click="$emit('confirm')">Evet, Sil</DangerButton>
        </div>
   </div>
</template>

<script  setup>
import {WarningIcon} from '@/Components/Icons'
import {RegularButton,DangerButton} from '@/Components/Buttons'
const props = defineProps({
    title:{},
    description:{},
});
const emits = defineEmits(['cancel','confirm'])
</script>

<style scoped>

</style>
