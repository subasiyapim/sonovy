<template>
    <div class="border border-soft-200 rounded-xl h-10 flex  overflow-hidden w-min">
        <button class="bg-white  w-10 flex items-center justify-center" @click="onDecrement">
            <MinusIcon color="var(--sub-600)" />
        </button>
        <input @input="onInput"  class="pointer-events-none appIncrementerButton bg-white w-16 text-center paragragraph-sm c-strong-950 border-none focus:ring-0" v-model="formattedValue" />
        <button class="bg-white  w-10  flex items-center justify-center" @click="onIncrement">
            <AddIcon color="var(--sub-600)" />
        </button>
    </div>
</template>

<script setup>
   import { ref,computed } from 'vue'
    import {MinusIcon,AddIcon} from '@/Components/Icons'
    const props = defineProps({
        type:{type:String},
        modelValue:{
            type:Number,
            default:0
        },
        config:{}
    })

    const emits = defineEmits(['update:modelValue','change','input']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })

    const formattedValue = computed({
        get:() => {
            if(props.config.formatter){

                return props.config.formatter(element.value);
            }else {
                return element.value;
            }
        },
        set:(value) => {
            console.log("LOOOG",value);
          return element.value = value;
        }
    })
    const onDecrement = () => {
        if(element.value > 0)
            element.value -= props.config?.step ?? 1;
    }
    const onIncrement = () => {
        console.log("adsdas");
        element.value += props.config?.step ?? 1;
    }
    const onInput = (event) => {
        // console.log("EVENT TARGET",event.target.value);
        // if(event.target.value == "")
        //     element.value = 0;
        // else
        //     element.value = parseInt(event.target.value)
    }

</script>

<style  scoped>

    .appIncrementerButton::-webkit-outer-spin-button,
    .appIncrementerButton::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    .appIncrementerButton[type=number] {
    -moz-appearance: textfield;
    }
</style>
