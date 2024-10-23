<template>
    <div class="w-full flex items-center gap-4">
        <!-- <slot  /> -->

        <render @change="onIndexChange" :index="index" v-for="(currentSlot,index) in slots.default()">
            <!-- {{a}} -->
        </render>

    </div>
</template>

<script setup>
    import {computed,useSlots,h} from 'vue';
    const props = defineProps({
        modelValue:{
        },

    })
    const emits = defineEmits(['update:modelValue','change']);

    const slots = useSlots()
    const hasSlot = (name) => {
        return !!slots[name];
    }
    const activeIndex = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })

    const onIndexChange = (e) => {
        activeIndex.value = e;
        emits('change',e)
    }
    const render = (e) => {
        return h(slots.default()[e.index], {
            currentIndex:e.index,
            activeIndex: activeIndex.value,
            showIcon:slots.default().length-1 > e.index
        }, slots.default()[e.index].children);
    };

</script>

<style lang="scss" scoped>

</style>
