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
            <span class="paragraph-xs c-sub-600">Türkçe</span>
        </div>
        <div class="mt-1">
            <p class="label-medium c-strong-950 !text-start">Lorem ipsum dolor sit amet consectetur. Dignissim a neque fermentum mauris blandit auctor purus vel a.</p>
            <p class="paragraph-sm c-sub-600 !text-start">Aenean pulvinar enim iaculis tristique id sed. Elementum purus non mauris diam. A eget mattis id aliquam in sit ridiculus. Diam tortor vitae tortor a nibh pretium morbi ipsum turpis. Phasellus gravida eu in suspendisse. Fermentum at vitae commodo tristique vestibulum. At at nisl id tristique amet dignissim in sed. Suspendisse lectus id massa diam bibendum aliquam orci risus nullam. Sed diam id pharetra id faucibus nec. Risus sagittis tellus fermentum tristique pellentesque purus eget. Pharetra est magna pellentesque risus platea vel nunc pellentesque.

                Fermentum malesuada eget cursus nisi vulputate velit varius massa ornare. Ipsum amet condimentum ac quam. Est scelerisque sed pellentesque vitae diam interdum molestie aliquam. Convallis morbi vitae tellus ultrices gravida pharetra. Enim ut semper at neque libero proin. Vitae nascetur in nunc tortor amet aliquet sit magna cursus. Lacus at ipsum facilisis tellus vel nec sed. Aliquam aliquet quis nunc non odio. Egestas donec duis amet leo eget purus.
            </p>
        </div>
        {{promotion}}
    </div>
</template>

<style scoped>

</style>
