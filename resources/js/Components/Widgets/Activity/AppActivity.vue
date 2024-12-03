<script setup>

const props = defineProps({
    items:{
        default:[]
    }
});


</script>

<template>
    <div v-if="items.length > 0" class="flex flex-col relative">

        <div class="flex items-start gap-4 z-10" v-for="item in items">
            <div  class=" bg-dark-green-25 border border-dark-green-700 w-5 h-5 rounded-full">
            </div>
            <div class="flex flex-1 flex-col items-start">
                <p class="label-sm c-strong-950">{{item.title}}</p>
                <p class="label-xs c-soft-400">{{item.description}}</p>
            </div>
        </div>


        <div class="h-full absolute left-[10px] bottom-0 top-0 w-[1px] bg-soft-200">
        </div>
    </div>
    <div v-else>
        Activite bulunamadı
    </div>
</template>

<style lang="css" scoped>

</style>
