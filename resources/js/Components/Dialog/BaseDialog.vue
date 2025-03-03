<template>
    <teleport to="body">
       <div v-if="isDialogOn" @click="closeDialog" class="appDialogBg z-10" >

       </div>

       <div v-if="isDialogOn" :class="dialogWrapperClass" class="absolute inset-0 flex overflow-hidden p-3 pointer-events-none z-10 mx-auto" :style="{'width':width}">
            <div  class="dialogBody pointer-events-auto overflow-hidden flex flex-col"  :style="dialogStyle" >

                <div v-if="showHeader" class="p-4 flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-white-600">
                        <slot name="icon" />
                    </div>
                    <div>
                        <p class="label-sm c-strong-950">{{title}}</p>
                        <p v-if="!hasSlot('description')" class="paragraph-xs c-sub-600">{{description}}</p>
                        <slot v-else name="description" />
                    </div>
                    <div v-if="showClose" class="ms-auto">
                        <button class="p-2" @click="isDialogOn = false"><CloseIcon color="var(--sub-600)" /></button>
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
import {computed,useSlots} from 'vue';
import {CloseIcon} from '@/Components/Icons'

const slots = useSlots()
const props = defineProps({
    modelValue: {
        default:false,
    },
    align:{
        default:'center'
    },
    height:{
        default:'100%'
    },
    width:{

    },
    static:{
        default:true
    },
    title:{},
    description:{},
    verticalAlign:{
        default:'center'
    },
    showHeader:{
        default:true
    },
    showClose:{
        default:false,
    }
})
const emits  =defineEmits(['update:modelValue']);
const isDialogOn = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
const showDialog = () => {

}

const hasSlot = (name) => {
    return !!slots[name];
}
const closeDialog = () => {
    if(!props.static){
        isDialogOn.value = false;
    }

}
const dialogStyle = computed(() => {
    let classString = {'height' : props.height, 'max-height' : "100%",'width' : props.width};
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
     if(props.verticalAlign == 'center'){
        classString += " items-center "
    }else if(props.verticalAlign == 'top'){
        classString += " items-start "
    }else if(props.verticalAlign == 'bottom'){
        classString += " items-end "
    }
    return classString;
})
</script>

<style lang="scss" scoped>

</style>
