<template>

  <a :href="path" :class="path == route(route().current()) ? 'active' :''" class="radius-8 sub-menu-item"><slot /></a>
</template>

<script setup>
    import { useSlots ,ref} from 'vue'

    defineProps({
        title:{type:String},
        path:{type:String,default:'#'},

    })

    const slots = useSlots()
    const hasSlot = (name) => {
        return !!slots[name];
    }

</script>



<style scoped>

</style>
