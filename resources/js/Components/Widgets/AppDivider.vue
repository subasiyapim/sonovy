<template>
    <div class="flex items-center w-full my-4" :class="title ? 'gap-3' : '' ">
        <hr class="flex-1 border border-soft-200 ">
        <span v-if="title" class="subheading-2xs c-soft-400">{{title}}</span>
        <hr class="flex-1  border border-soft-200  ">
    </div>
</template>

<script setup>
    const props = defineProps({
        title : {
            default:''
        },
        titleClass: {

        }
    });
</script>
