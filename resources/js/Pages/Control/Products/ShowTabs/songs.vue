<script setup>
import {ref} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton} from '@/Components/Buttons';
import {SongParticipantModal,SongDetailModal,SongAcrResponseModal} from '@/Components/Dialog';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {AudioIcon,RingtoneIcon,MusicVideoIcon,PlayCircleFillIcon,ChartsIcon,EyeOnIcon,EditIcon} from '@/Components/Icons'
const props = defineProps({
    product:{}
});
const isSongParticipantModalOn = ref(false);
const isSongDetailModalOn = ref(false);
const isAcrResponseModalOn = ref(false);
const choosenSong = ref(null);
const openParticipantModal = (song) => {
    isSongParticipantModalOn.value = true;
    choosenSong.value = song;
};
const openSongDetailModal = (song) => {
    isSongDetailModalOn.value = true;
    choosenSong.value = song;
};
const openAcrResponseModal = (song) => {
    isAcrResponseModalOn.value = true;
    choosenSong.value = song;
};
</script>
<template>
    <AppTable :hasSelect="true" v-model="product.songs"  :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="#" sortable="id">
            <template #default="scope">
                <p class="table-name-text">
                    {{scope.row.id}}
                </p>
            </template>
        </AppTableColumn>
        <AppTableColumn label="tür">
            <template #default="scope">
                 <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

                    <RingtoneIcon color="var(--sub-600)" v-if="scope.row.type == 1" />
                    <MusicVideoIcon color="var(--sub-600)" v-if="scope.row.type == 2" />

                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Durum">
            <template #default="scope" :showIcon="true">
                    <div class="flex items-center gap-2 px-2 py-1 rounded-lg border border-soft-200">
                        <div class="w-4 h-4 rounded-full bg-dark-green-500 flex items-center justify-center">
                            <div class="w-1 h-1 rounded-full bg-white"></div>
                        </div>
                       <p class="label-sm c-strong-950"> Yayında</p>
                    </div>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Parça Adı">
            <template #default="scope">

            <div class="flex items-center gap-3">

                <div>
                    <p class="label-sm c-solid-950"> {{scope.row.name}}</p>
                    <p class="paragraph-xs c-sub-600"> {{scope.row.isrc}} </p>
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

            <div class="flex items-center gap-2">

                <div class="flex -space-x-3 rtl:space-x-reverse">
                    <img class="w-8 h-8 border-2 border-white rounded-full " v-for="a in scope.row.artists" src="/docs/images/people/profile-picture-5.jpg" alt="">
                    <a class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full  " href="#">+5</a>
                </div>
            </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Linkler">
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn label="Katılımcılar">
            <template #default="scope">
                <button @click="openParticipantModal(scope.row)">
                    <div class="flex items-center gap-2 label-xs c-sub-600 border border-soft-200 px-2 py-1 rounded-lg">
                        <p class="w-max"> {{scope.row.participants?.length ?? 0}} Katılımcı</p>
                    </div>
                </button>

            </template>
        </AppTableColumn>
        <AppTableColumn label="Analiz">
            <template #default="scope">
                <button @click="openAcrResponseModal(scope.row)" class="flex items-center gap-2">
                    <ChartsIcon color="var(--neutral-500)" />
                    <span class="c-neutral-500 label-xs"> Analiz</span>
                    <span class="w-3 h-3 rounded-full bg-[#FF8447]"></span>
                </button>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyonlar" align="right">
            <template #default="scope">
                <IconButton @click="openSongDetailModal(scope.row)"><EyeOnIcon color="var(--sub-600)" /></IconButton>
                <IconButton @click="openEditDialog(scope.row)"><EditIcon color="var(--sub-600)" /></IconButton>
            </template>
        </AppTableColumn>
        <template #empty>
            Şarkı bulunamadı
        </template>
    </AppTable>
    <SongParticipantModal v-if="isSongParticipantModalOn" v-model="isSongParticipantModalOn" :song="choosenSong"></SongParticipantModal>
    <SongDetailModal v-if="isSongDetailModalOn" v-model="isSongDetailModalOn" :song="choosenSong"></SongDetailModal>
    <SongAcrResponseModal v-if="isAcrResponseModalOn" v-model="isAcrResponseModalOn" :product="product" :song="choosenSong"></SongAcrResponseModal>
</template>

<style scoped>

</style>
