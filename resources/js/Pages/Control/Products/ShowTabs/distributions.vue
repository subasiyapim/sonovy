<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {Icon,HistoryIcon,ScheduledIcon,WarningDocumentIcon} from '@/Components/Icons';
const props = defineProps({
    product:{}
});
</script>
<template>
    <AppTable :hasSelect="true" v-model="product.distributions"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="Platform">
            <template #default="scope">
                <div class="flex items-center gap-2">
                    <Icon :icon="scope.row.icon" />
                   <p class="label-sm c-strong-950"> {{scope.row.name}}</p>
                </div>
            </template>
        </AppTableColumn>
         <AppTableColumn label="Oluşturulma Tarihi">
            <template #default="scope">

            </template>
        </AppTableColumn>
         <AppTableColumn label="Yayınlanma Tarihi">
            <template #default="scope">
                <div class="flex flex-col items-start">
                    <span class="c-sub-600 paragraph-xs">{{scope.row.publish_date}}</span>
                    <span class="c-soft-400 paragraph-xs">Ön Sipariş: {{scope.row.pre_order_date}}</span>
                </div>

            </template>
        </AppTableColumn>
        <AppTableColumn label="Durum">
            <template #default="scope">
            <div class="rounded border border-soft-200 px-2 py-1">
                    <span class="label-xs c-sub-600">{{scope.row.status}}</span>
            </div>

            </template>
        </AppTableColumn>
        <AppTableColumn label="Tarihçe" align="center">
            <template #default="scope">
                <HistoryIcon color="var(--neutral-500)" />
            </template>
        </AppTableColumn>

        <AppTableColumn label="Aksiyon" align="right">
            <template #default="scope">
                <div class="flex items-center gap-3">
                    <button class="label-xs c-neutral-500">Dağıt</button>
                    <button class="label-xs c-neutral-500">Güncelle</button>
                    <button class="label-xs c-neutral-500">Tekrar Gönder</button>
                    <button class="label-xs c-neutral-500">Geri Çek</button>
                </div>

            </template>
        </AppTableColumn>
        <template #empty>
            Dağıtım bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
