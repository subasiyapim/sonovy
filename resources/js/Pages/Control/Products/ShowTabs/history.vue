<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
const props = defineProps({
    product:{}
});
</script>
<template>
    <AppTable :hasSelect="true" v-model="product.history"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="Platform">
            <template #default="scope">

            </template>
        </AppTableColumn>
         <AppTableColumn label="Aksiyon">
            <template #default="scope">

            </template>
        </AppTableColumn>
         <AppTableColumn label="Aksiyon Tarihi">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Tamamlanma tarihi">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Tamamlanma Süresi">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <template #empty>
            Dağıtım bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
