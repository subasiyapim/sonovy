<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
const props = defineProps({
    product:{}
});
</script>
<template>
    <AppTable :hasSelect="true" v-model="product.platforms"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="Platform">
            <template #default="scope">

            </template>
        </AppTableColumn>
         <AppTableColumn label="Oluşturulma Tarihi">
            <template #default="scope">

            </template>
        </AppTableColumn>
         <AppTableColumn label="Yayınlanma Tarihi">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Durum">
            <template #default="scope" :showIcon="true">
                    <StatusBadge type="pending">
                        <p class="c-orange-700"> Bilgiler Eksik</p>
                    </StatusBadge>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Tarihçe">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn label="Aksiyon" align="right">
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
