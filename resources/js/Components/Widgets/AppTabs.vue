<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    tabs: {
        type: Array,
        required: true
    },
    slug: {
        type: String,
        required: true
    }
});

const emits = defineEmits(['change']);

const activeTab = ref(props.slug);

// props.slug değiştiğinde activeTab'i güncelle
watch(() => props.slug, (newSlug) => {
    activeTab.value = newSlug;
}, { immediate: true });

const onTabClicked = (tab) => {
    activeTab.value = tab.slug;
    emits('change', tab);
};
</script>

<template>
    <div class="pe-8 flex items-center gap-5 cursor-pointer border-b border-soft-200">
        <div v-for="item in tabs"
             :key="item.slug"
             @click="onTabClicked(item)"
             class="label-sm pb-3"
             :class="activeTab === item.slug ? 'c-strong-950 border-b-2 border-dark-green-500' : 'c-sub-600'">
            {{ item.title }}
        </div>
    </div>
</template>

<style scoped>
.border-b-2 {
    border-bottom-width: 2px;
    margin-bottom: -1px;
}
</style>
