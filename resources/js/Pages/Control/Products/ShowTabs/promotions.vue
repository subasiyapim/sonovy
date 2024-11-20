<script setup>
import {SectionHeader} from '@/Components/Widgets';

const props = defineProps({
    product:{}
});
</script>

<template>

    <SectionHeader class="mb-4" :title="__('control.product.show.promotion_header')"/>
    <div class="border border-soft-200 p-4 rounded-lg mb-4" v-for="promotion in product.promotions">
        <div class="flex items-center gap-2">
            <span class="paragraph-xs c-sub-600">
            {{promotion.country?.emoji}}
            {{promotion.country?.name}}
            </span>
        </div>
        <div class="mt-1">
            <p class="label-medium c-strong-950 !text-start">
                {{promotion.title}}
            </p>
            <p class="paragraph-sm c-sub-600 !text-start">

                {{promotion.description}}
             </p>
        </div>
    </div>

</template>

<style scoped>

</style>
