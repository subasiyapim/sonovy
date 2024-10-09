<template>

   <div class="w-full flex  border-text-input flex items-center radius-8 c-white-500  pb-2">
        <div v-if="hasSlot('icon')">
            <slot  name="icon" />
        </div>
        <textarea rows="5" class="border-none focus:outline-none focus:border-none  focus:border-transparent focus:ring-0 h-full w-full bg-transparent paragraph-sm c-strong-950" type="textarea" :placeholder="placeholder" >

        </textarea>
   </div>

</template>

<script setup>
    import { useSlots,computed } from 'vue'
    const slots = useSlots()
    const props = defineProps({
         modelValue:{},
        placeholder: { type: String}
    })
        const emits = defineEmits(['update:modelValue']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })

    const hasSlot = (name) => {
        return !!slots[name];
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
