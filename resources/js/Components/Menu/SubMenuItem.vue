<template>

  <a  :href="path !='#' ? route(path) : path" :class="path == route().current() ? 'active' :''" class="sonovySubMenuItem radius-8 sub-menu-item label-sm my-0.5"><slot /></a>
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
