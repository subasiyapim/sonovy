<template>

    <div class="pb-6">
        <p class="label-xl c-strong-950">
            Şimdi, yayına dahil eklemek istediğiniz parçaları yükleyin 👍🏻
        </p>
    </div>
    <div class="flex items-center gap-3 mb-6">
        <RegularButton>Katalogdan Seç</RegularButton>
        <PrimaryButton @click="onSongAdd">
            <template #icon>
                <AddIcon  color="var(--dark-green-600)" />
            </template>
            Parça Ekle
        </PrimaryButton>
    </div>
    <div class="flex gap-10">

        <div class="flex-1 flex flex-col overflow-scroll gap-6">

        <AppTable :hasSelect="true" v-model="songs" :showEmptyImage="false" :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="#">
                    <template #default="scope">
                        <DraggableIcon color="var(--sub-600)" />
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
                            <div class="w-9 h-9 rounded-full border border-soft-200 flex items-center justify-center">
                                <MusicVideoIcon color="var(--dark-green-600)" />
                            </div>
                                {{scope.row.duration ?? '2.35'}}
                        </div>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Durum">
                    <template #default="scope" :showIcon="true">
                            <StatusBadge type="pending">
                               <p class="c-orange-700"> Bilgiler Eksik</p>
                            </StatusBadge>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Aksiyonlar" align="right">
                    <template #default="scope">
                        <IconButton><StarIcon color="var(--sub-600)" /></IconButton>
                        <IconButton><TrashIcon color="var(--sub-600)" /></IconButton>
                        <IconButton @click="openEditDialog(scope.row)"><EditIcon color="var(--sub-600)" /></IconButton>
                    </template>
                </AppTableColumn>
                <template #appends v-if="attemps.length > 0 && showAttempt">
                    <div class="flex flex-col gap-2">
                        {{attemps}}
                        <SongLoadingCard v-model="attemps[index]" :key="index" v-for="(loadingCardAttempt,index) in attemps" />
                    </div>
                </template>
                <template #empty>
                    <TusUploadInput :product_id="product.id" ref="tusUploadElement" @start="onTusStart" @progress="onTusProgress" @complete="onTusComplete"></TusUploadInput>
                </template>
            </AppTable>
        </div>

    </div>
    <SongDialog v-if="isSongDialogOn" @done="onComplete" v-model="isSongDialogOn" :song="choosenSong"></SongDialog>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref,useSlots,nextTick} from 'vue';
import {FormElement} from '@/Components/Form';
import {AddIcon} from '@/Components/Icons'
import {TusUploadInput} from '@/Components/Form'
import {SongLoadingCard} from '@/Components/Cards';
import {StatusBadge} from '@/Components/Badges';
import {SongDialog} from '@/Components/Dialog';
import {RegularButton,PrimaryButton,IconButton}  from '@/Components/Buttons'
import {StarIcon,TrashIcon,EditIcon,DraggableIcon,MusicVideoIcon} from '@/Components/Icons';

const attemps = ref([],{deep:true});
const props = defineProps({
    product:{}
})
const songs = ref(props.product.songs);
const tusUploadElement  = ref();
const showAttempt = ref(false);
const isSongDialogOn = ref(false);
const onTusStart = (e) => {
    showAttempt.value = true;
    console.log("STARTT",e);
    attemps.value.push(e);
}

const choosenSong = ref();

const onSongAdd = () => {
    tusUploadElement.value.triggerFileInput();
}
const onTusProgress = (e) => {
    showAttempt.value = false;
    const findedIndex = attemps.value.findIndex((el) => el.filename == e.filename);
    if(findedIndex >= 0){
        attemps.value[findedIndex].percentage = e.percentage;
    }
    nextTick(() => {
         showAttempt.value = true;
    })
}
const onTusComplete = (e) => {
    const findedIndex = attemps.value.findIndex((el) => el.orignalName == e.name);

    if(findedIndex >= 0){
        attemps.value.splice(findedIndex,1);
        songs.value.push(e);
    }
    console.log("BİTTİİ");

}

const openEditDialog = (song) => {
    isSongDialogOn.value = true
    choosenSong.value = song
}

const onComplete = (e) => {
    choosenSong.value = JSON.parse(JSON.stringify(e));
    isSongDialogOn.value = false;
    choosenSong.value = null;

}
</script>

<style lang="scss" scoped>

</style>
