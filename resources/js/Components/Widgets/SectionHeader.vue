<template>
    <div class="bg-white-600 px-5 py-2 " >
        <p class="c-white-900 subheading-xs">
            {{title}}
        </p>
    </div>
</template>

<script setup>
const props = defineProps({
    title:{type:String}
})
</script>

<style lang="scss" scoped>

</style>
