<script setup>
import {ref,computed} from 'vue';
import {AppProgressIndicator} from '@/Components/Widgets';


const props = defineProps({
    modelValue:{}
})
const emits = defineEmits(['update:modelValue','remove']);
const onRemove = () => {
    emits('remove');
}
const meta = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
});
</script>

<template>

    <div class="h-16 w-full py-3 ps-3 pe-5 rounded-lg border border-dashed border-soft-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full flex items-center justify-center p-3 bg-white-600">
            <img src="@/assets/images/mp3_passive.png">
        </div>
        <div class="w-full">
            <p class="label-sm c-solid-950">Geleceğe Doğru Yol Alırken</p>
            <div class="flex items-center justify-start gap-2">
                <span class="paragraph-xs c-sub-600 ">0 KB of 2.3 MB</span>
                <span class="paragraph-xs c-sub-600">∙</span>

                <div class="w-32" v-if="!meta.errorMessage"> <AppProgressIndicator  :height="6" v-model="meta.percentage"/></div>
                <p v-else class="c-error-500 paragraph-xs "> {{meta.errorMessage}}</p>
            </div>
        </div>
        <button @click="onRemove" class="c-error-500 paragraph-sm hover:underline">
            sil
        </button>
    </div>
</template>

<style scoped>

</style>
