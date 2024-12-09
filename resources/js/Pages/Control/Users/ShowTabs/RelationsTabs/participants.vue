

<template>
         <AppTable  v-model="tableData"  :isClient="true" >
        <AppTableColumn label="Kullanıcı Adı">
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn label="Kullanıcı Rolü">
            <template #default="scope">

            </template>
        </AppTableColumn>


        <AppTableColumn label="Realizasyon/Hakediş">
            <template #default="scope">

            </template>
        </AppTableColumn>


         <AppTableColumn label="Durumu">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyon">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
</template>

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed} from 'vue';
const props = defineProps({
    modelValue:{}
});
const emits = defineEmits(['update:modelValue']);

const tableData = computed({
    get:() => props.modelValue,
    set:(val) => emits('update:modelValue',val)
})
</script>
<style  scoped>

</style>
