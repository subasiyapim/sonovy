<script setup>
const props = defineProps({
    totalPages: {
        type: Number,
        required: true
    },
    currentPage: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['page-change']);

const pages = Array.from({ length: props.totalPages }, (_, i) => i + 1);

const changePage = (page) => {
    if (page !== props.currentPage && page > 0 && page <= props.totalPages) {
        emit('page-change', page);
    }
};
</script>

<template>
    <div class="flex items-center gap-2">
        <!-- Önceki sayfa butonu -->
        <button 
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 rounded border"
            :class="currentPage === 1 ? 'border-gray-200 text-gray-400' : 'border-gray-300 hover:bg-gray-50'">
            &lt;
        </button>

        <!-- Sayfa numaraları -->
        <template v-for="page in pages" :key="page">
            <button 
                v-if="page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)"
                @click="changePage(page)"
                class="px-3 py-1 rounded"
                :class="page === currentPage ? 'bg-dark-green-500 text-white' : 'hover:bg-gray-50'">
                {{ page }}
            </button>
            <span v-else-if="page === currentPage - 2 || page === currentPage + 2" class="px-1">...</span>
        </template>

        <!-- Sonraki sayfa butonu -->
        <button 
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 rounded border"
            :class="currentPage === totalPages ? 'border-gray-200 text-gray-400' : 'border-gray-300 hover:bg-gray-50'">
            &gt;
        </button>
    </div>
</template>

<style scoped>
button:disabled {
    cursor: not-allowed;
}
</style> 