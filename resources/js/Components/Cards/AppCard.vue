<template>
    <div :class="width" class="w-full flex flex-col p-5 radius-16 border border-soft-200">
        <div class="flex items-start ">
            <div class="flex-1">
                <slot name="header" />

            </div>
            <div>
               <slot name="tool" />
            </div>
        </div>
        <div>
             <slot name="body" />
        </div>
    </div>
</template>

<script setup>
    import { useSlots } from 'vue';

    const slots = useSlots()
    const hasSlot = (name) => {
        return !!slots[name];
    }
    const props  = defineProps({
        width:{
            default:'w-[360px]'
        }
    });
</script>


<style>


</style>
