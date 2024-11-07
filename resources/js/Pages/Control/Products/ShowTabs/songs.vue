<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
const props = defineProps({
    product:{}
});
</script>
<template>
    <AppTable :hasSelect="true" v-model="product.songs"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="#" sortable="id">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="tür">
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

        <AppTableColumn label="Parça Adı">
            <template #default="scope">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center p-3 bg-dark-green-800">
                    <img src="@/assets/images/mp3_active.png">
                </div>
                <div>
                    <p class="label-sm c-solid-950"> {{scope.row.name}}</p>
                    <p class="paragraph-xs c-sub-600"> {{(scope.row.size / (1024 * 1024)).toFixed(2)}} MB</p>
                </div>
            </div>

            </template>
        </AppTableColumn>
        <AppTableColumn label="Süre">
            <template #default="scope">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                        <PlayCircleFillIcon color="var(--dark-green-500)" />
                    </div>
                    <p class="label-sm c-strong-950">
                        {{scope.row.duration ?? '2.35'}}
                    </p>
                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Sanatçı">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Linkler">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Katılımcılar">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Analiz">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyonlar" align="right">
            <template #default="scope">
                <IconButton><StarIcon color="var(--sub-600)" /></IconButton>
                    <tippy :maxWidth="440" ref="myTippy" theme="light" :allowHtml="true" :sticky="true" trigger="click" :interactive="true" :appendTo="getBody">
                    <IconButton>
                        <TrashIcon color="var(--sub-600)" />
                    </IconButton>
                    <template #content>
                        <ConfirmDeleteDialog @confirm="onDeleteSong(scope.row)" @cancel="onCancel"  title="Parçayı silmek istediğinze emin misin" description="Yüklediğiniz parçalardan albümden silenecektir. Daha sonra tekrar yayınlanma öncesi albüme parça ekleyebilirsiniz." />
                    </template>
                </tippy>

                <IconButton @click="openEditDialog(scope.row)"><EditIcon color="var(--sub-600)" /></IconButton>
            </template>
        </AppTableColumn>
        <template #empty>
            Şarkı bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
