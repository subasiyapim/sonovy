<template>


    <div class="p-4 border-text-input flex items-start radius-8 c-white-500 p-3 cursor-pointer" >
        <div @click="playMusic">
            <PlayCircleFillIcon color="var(--dark-green-500)" />
        </div>

        <div class="w-full relative">
        <p class="absolute text-black -right-2 bottom-3 paragraph-xs c-sub-600"> {{config?.formatter(config.max)}}</p>
            <VueSlider :minRange="config?.range" :max="config?.max" :interval="1" :maxRange="config?.range" :tooltip-formatter="config?.formatter" :processStyle="config?.railStyle" class="!w-full" v-model="element"  contained></VueSlider>

        </div>
    </div>


</template>

<script setup>
    import VueSlider from 'vue-slider-component'
    import {PlayCircleFillIcon} from '@/Components/Icons'
    import { useSlots,ref,computed } from 'vue'
    const isChecked = ref(false)
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        placeholder: { type: String},
        modelValue:{},
        config:{},

    })
    const emits = defineEmits(['update:modelValue','change','input','play']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })
    const playMusic = () => {
        emits('play')
    }


</script>

<style  >
.vue-slider-process{
    background-color:var(--dark-green-800) !important;
}
.vue-slider-dot-tooltip-inner{
    background-color:var(--dark-green-800) !important;
    color:#fff;

}
.vue-slider-dot-tooltip-inner-top::after{
    border-top-color:var(--dark-green-800) !important;
}
.vue-slider-dot-handle-focus{

    box-shadow: 0px 2px 4px 0px #0E121B08;
    border:1px solid var(--soft-200);


}
.vue-slider-dot-handle{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:center;
    border:1px solid var(--soft-200);
     box-shadow: 0px 2px 4px 0px #0E121B08;
}
.vue-slider-dot-handle::after{
    content:"";
    position:absolute;
    inset:0;
    margin:3px;
    border-radius:50%;
    background:var(--dark-green-800);
}
</style>
