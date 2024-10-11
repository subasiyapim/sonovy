<template>
    <div class="radius-8 border border-soft-200 flex items-center gap-2 py-1 px-2">
        <template v-if="showIcon">
                <CheckFilledIcon color="var(--dark-green-600)" />
        </template>
        <slot name="default" />
          <span v-if="showClose" class="cursor-pointer " @click="$emit('close')">
                <CloseIcon  color="var(--soft-400)" />
        </span>
    </div>
</template>

<script setup>
    import {CheckFilledIcon,CloseIcon} from '@/Components/Icons'
     defineProps({
        showIcon:{
            default:true // empty
        },
        showClose:{
            default:false
        },
    })

</script>

<style lang="scss" scoped>

</style>
