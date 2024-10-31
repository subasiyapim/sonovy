<template>

    <div class="flex items-center relative bg-white-600 rounded overflow-hidden w-full" :style="{'height' : `${height}px`}">
        <div class="absolute bg-dark-green-600"   :style="{'width' : element+'%','height' : `${height}px`}">
        </div>
    </div>
</template>

<script setup>
    import {computed} from 'vue';
    const props = defineProps({
       modelValue:{
        default:0,
       },
       height:{
        default:8,
       },

    });
    const emits = defineEmits(['update:modelValue']);
    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })
</script>
