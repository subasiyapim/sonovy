<template>


    <div class="flex items-center gap-3">
        <div v-for="option in config?.options" :class="element == option.value ? 'bg-dark-green-25 border-dark-green-500' : 'bg-white border-soft-200'" class="w-full flex gap-2 border flex items-center radius-12  p-3 cursor-pointer" @click="onCheckValue(option)">
            <div :class="element == option.value ? 'bg-dark-green-800 border-dark-green-500' : 'bg-white border-soft-200 '" class="w-10 h-10 rounded-full border shadow-sm bg-white flex items-center justify-center">
                <component :color="element == option.value ? 'var(--dark-green-500)' :'var(--sub-600)'" :is="option.icon" />
            </div>

            <p class="label-sm c-strong-950 flex-1">
            {{option.title}}

            </p>
            <div :class="element == option.value ? 'bg-dark-green-500' : 'bg-white border border-soft-200'" class="w-4 h-4 rounded-full flex items-center justify-center  shadow ">
                <div v-if="element == option.value" class="w-2 h-2 bg-white rounded-full"></div>
            </div>
        </div>
    </div>



</template>

<script setup>
    import { useSlots,ref,computed } from 'vue'
    const isChecked = ref(false)
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        placeholder: {type: String},
        modelValue:{},
        config:{}

    })
    const emits = defineEmits(['update:modelValue','change']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })
    const onCheckValue = (option) => {


        element.value = option.value;

        emits('change',option)
    }

</script>

<style scoped>


</style>
