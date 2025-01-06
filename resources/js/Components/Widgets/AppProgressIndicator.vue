<template>

    <div class="flex items-center relative bg-white-600 rounded overflow-hidden w-full" :style="{'height' : `${height}px`}">
        <div class="absolute rounded-full"   :style="{'width' : element+'%','height' : `${height}px`,'background' : color}">
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
       color:{
        default:'#3F5562'
       }

    });
    const emits = defineEmits(['update:modelValue']);
    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })
</script>
