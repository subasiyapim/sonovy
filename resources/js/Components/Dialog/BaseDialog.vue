<template>
    <teleport to="body">
       <div v-if="isDialogOn" @click="closeDialog" class="appDialogBg " >

       </div>

       <div v-if="isDialogOn" :class="dialogWrapperClass" class="absolute inset-0 flex  p-3 pointer-events-none">
            <div  class="dialogBody pointer-events-auto overflow-hidden flex flex-col"  :class="dialogStyle" >
                <div class="p-4 flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-white-600">
                        <slot name="icon" />
                    </div>
                    <div>
                        <p class="label-sm c-strong-950">{{title}}</p>
                        <p class="paragraph-xs c-sub-600">{{description}}</p>
                    </div>
                </div>
                <div class="overflow-scroll h-full">
                        <slot />
                </div>
            </div>
       </div>
    </teleport >
</template>

<script setup>
import {computed} from 'vue';
const props = defineProps({
    modelValue: {
        default:false,
    },
    align:{
        default:'center'
    },
    height:{
        default:'full'
    },
    static:{
        default:false
    },
    title:{},
    description:{},
})
const emits  =defineEmits(['update:modelValue']);
const isDialogOn = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
const showDialog = () => {

}
const closeDialog = () => {
    if(!props.static){
        isDialogOn.value = false;
    }

}
const dialogStyle = computed(() => {
    let classString = ` h-${props.height} `;
    return classString;
})
const dialogWrapperClass = computed(() => {
    let classString = `  `;
    if(props.align == 'center'){
        classString += " justify-center "
    }else if(props.align == 'left'){
        classString += " justify-start "
    }else if(props.align == 'right'){
        classString += " justify-end "
    }
    return classString;
})
</script>

<style lang="scss" scoped>

</style>
