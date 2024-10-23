<template>
    <div class="border  radius-10 p-3.5 w-full cursor-pointer" @click="isOpen = !isOpen" :class="isOpen ? 'border-green-700 bg-white-500' :'border-soft-200'">
        <div class="flex items-center">
            <div class="flex-1">
                <div class="label-sm c-strong-950">{{title}}</div>
                <div class="paragraph-sm c-sub-600">{{description}}</div>
            </div>
            <div>
                <AddIcon v-if="!isOpen" color="var(--soft-400)" />
                <MinusIcon v-else color="var(--soft-400)" />
            </div>
        </div>
        <div v-if="isOpen">
            <slot />
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {AddIcon,MinusIcon} from '@/Components/Icons'
const props = defineProps({
    title:{},
    description:{},
})
const isOpen =ref(false);
</script>

<style lang="scss" scoped>

</style>
