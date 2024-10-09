<template>
    <AdminLayout  title="Tüm Sanatçılar" parentTitle="Katalog">

        <AppTable v-model="usePage().props.artists" @addNewClicked="openDialog">
            <AppTableColumn label="Sanatçı Adı">
                <template #default="scope"> {{ scope.row.name }}</template>
            </AppTableColumn>
            <AppTableColumn label="Durum">
                <template #default="scope">{{scope.row.name}}</template>
            </AppTableColumn>

             <AppTableColumn label="Top. Parça Sayısı">
                <template #default="scope">-</template>
            </AppTableColumn>

            <AppTableColumn label="Tarzlar">
                <template #default="scope">-</template>
            </AppTableColumn>
              <AppTableColumn label="Aksiyonlar">
                <template #default="scope">
                   <div class="flex gap-3">
                         <IconButton>
                            <TrashIcon color="var(--sub-600)" />
                        </IconButton>
                        <IconButton>
                            <EditIcon color="var(--sub-600)" />
                        </IconButton>
                   </div>
                </template>
            </AppTableColumn>
            <template #empty>
                <div class="flex flex-col items-center justify-center gap-8">
                    <div>
                        <h2 class="label-medium c-strong-950">Henüz yayınız bulunmamaktadır.</h2>
                        <h3 class="paragraph-medium c-neutral-500">Oluşturucağınız tüm yayınlar burada listelenecektir.</h3>
                    </div>
                    <PrimaryButton>
                        <template #icon>
                                <AddIcon />
                        </template>
                        İlk Yayını Oluştur
                    </PrimaryButton>
                </div>
            </template>
        </AppTable>

        <AddArtistDialog v-model="isModalOn" />
    </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton,IconButton} from '@/Components/Buttons'
import {AddIcon,LabelsIcon, ArtistsIcon,TrashIcon,EditIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {AddArtistDialog} from '@/Components/Dialog';


const data =  ref([
    {
        name:"asdasd"
    },
     {
        name:"ikinci"
    },
])

const isModalOn = ref(false);
const openDialog = () => {
    isModalOn.value = !isModalOn.value;
}
</script>

<style lang="scss" scoped>

</style>
