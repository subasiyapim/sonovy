<template>


    <div class="w-full flex  items-start justify-center radius-8 c-white-500 p-3  gap-3 appRadioInput" :class="config?.optionDirection == 'vertical' ? 'flex-col' : 'flex-row'" @click="isChecked = !isChecked">
        <label v-for="option in config?.options" @click="element = option.value" class="cursor-pointer paragraph-sm c-strong-950 flex items-center gap-3" >
            <input type="radio" :checked="element == option.value" class="focus:ring-0">
            {{option.label}}
        </label>
    </div>


</template>

<script setup>
    import { useSlots,ref,computed } from 'vue'
    const isChecked = ref(false)
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        modelValue:{},
        config:{

        }

    })

    const emits = defineEmits(['update:modelValue','change','input']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })


</script>

<style scoped>
.appRadioInput input[type="radio"]:checked {
  background: var(--dark-green-600);
   border:none;
   position:relative;
   box-shadow: 0px 2px 2px 0px #1B1C1D1F;

}
.appRadioInput input[type="radio"]:checked:after {
 content:"";
 position:absolute;
 background:white;
 inset:0;
 width:8px;
 height:8px;
 margin:0px;
 left:4px;
 top:4px;
 border-radius:50%;

}
.appRadioInput input[type="radio"]{
    border:2px solid var(--soft-200);

}

</style>
