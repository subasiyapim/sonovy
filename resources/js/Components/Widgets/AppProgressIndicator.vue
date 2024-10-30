<template>

    <div class="flex items-center my-4 relative bg-white-600 rounded overflow-hidden w-full h-3" >
        <div class="absolute bg-dark-green-600 h-3" :style="{'width' : value+'%'}">
        </div>
    </div>
</template>

<script setup>
    const props = defineProps({
       value:{
        default:0,
       },

    });
</script>
