<template>

   <div class="w-full flex relative border-text-input flex items-center radius-8 c-white-500  pb-2 pe-2">
        <div v-if="hasSlot('icon')">
            <slot  name="icon" />
        </div>
        <textarea rows="5" v-model="element" :maxlength="config?.letter ?? 999999999" class="border-none focus:outline-none focus:border-none  focus:border-transparent focus:ring-0 h-full w-full bg-transparent paragraph-sm c-strong-950" type="textarea" @keydown="onInput" @input="onInput" :placeholder="placeholder" >

        </textarea>
        <span v-if="config?.letter > 0" class="absolute right-6 bottom-1.5 subheading-2xs c-soft-400">{{element?.length ?? '0'}} / {{config?.letter}}</span>
   </div>

</template>

<script setup>
    import { useSlots,computed } from 'vue'
    const slots = useSlots()
    const props = defineProps({
        modelValue:{},
        config:{},
        placeholder: { type: String}
    })
        const emits = defineEmits(['update:modelValue','change','input']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })

    const hasSlot = (name) => {
        return !!slots[name];
    }

    const onInput =  (e) => {
        emits('input',e);
    }
</script>

<style scoped>
input::placeholder{
    color:var(--soft-400)
}
input{
    color:var(--sub-600)
}
</style>
