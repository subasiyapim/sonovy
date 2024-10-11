<template>
    <teleport to="body">
        <div class="appNotificationMessage absolute z-10 paragraph-sm text-white px-5 py-2 radius-8 w-64 flex items-center gap-2" :class="type" style="top:20px; right:20px;">
           <InfoFilledIcon v-if="type == 'error'" />
           <CheckFilledIcon v-if="type == 'success'" />
            {{title}}
        </div>
    </teleport>
</template>

<script setup>
import {onMounted} from 'vue';
import {InfoFilledIcon,CheckFilledIcon} from '@/Components/Icons'
const props = defineProps({
    title:{},
    type:"success"
})


onMounted(() => {

});
</script>

<style  scoped>
.appNotificationMessage.error{
    background:var(--error-500)
}

.appNotificationMessage.success{
         background:var(--dark-green-600)
}
</style>
