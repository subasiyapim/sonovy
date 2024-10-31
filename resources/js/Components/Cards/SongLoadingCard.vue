<script setup>
import {ref} from 'vue';
import {AppProgressIndicator} from '@/Components/Widgets';
const percent = ref(10);
</script>

<template>
    <div class="h-64 w-full py-3 ps-3 pe-5 rounded-sm border border-dashed border-soft-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full flex items-center justify-center p-3 bg-white-600">
            <img src="@/assets/images/mp3_passive.png">
        </div>
        <div>
            <p class="label-sm c-solid-950">Geleceğe Doğru Yol Alırken</p>
            <div class="flex items-center gap-2">
                <span class="paragraph-xs c-sub-600">0 KB of 2.3 MB</span>
                <span class="paragraph-xs c-sub-600">∙</span>
                <AppProgressIndicator  :value="percent"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
