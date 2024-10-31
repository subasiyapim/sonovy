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
        <AppTable v-model="songs" :showEmptyImage="false" :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="#">
                    <template #default="scope">
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Parça Adı">
                    <template #default="scope"></template>
                </AppTableColumn>
                <AppTableColumn label="Süre">
                    <template #default="scope"></template>
                </AppTableColumn>

                <AppTableColumn label="Durum">
                    <template #default="scope"></template>
                </AppTableColumn>

                <AppTableColumn label="Aksiyonlar" align="right">
                    <template #default="scope">
                        <IconButton><StarIcon color="var(--sub-600)" /></IconButton>
                        <IconButton><TrashIcon color="var(--sub-600)" /></IconButton>
                        <IconButton><EditIcon color="var(--sub-600)" /></IconButton>
                    </template>
                </AppTableColumn>
                <template #appends v-if="attemps.length > 0 && showAttempt">
                    <div class="flex flex-col gap-2">
                        {{attemps}}
                        <SongLoadingCard v-model="attemps[index]" :key="index" v-for="(loadingCardAttempt,index) in attemps" />
                    </div>
                </template>
                <template #empty>
                    <TusUploadInput ref="tusUploadElement" @start="onTusStart" @progress="onTusProgress" @complete="onTusComplete"></TusUploadInput>
                </template>
            </AppTable>
        </div>

    </div>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref,useSlots,nextTick} from 'vue';
import {FormElement} from '@/Components/Form';
import {AddIcon} from '@/Components/Icons'
import {TusUploadInput} from '@/Components/Form'
import {SongLoadingCard} from '@/Components/Cards';
import {RegularButton,PrimaryButton,IconButton}  from '@/Components/Buttons'
import {StarIcon,TrashIcon,EditIcon} from '@/Components/Icons';
const songs = ref([]);
const attemps = ref([],{deep:true});

const tusUploadElement  = ref();
const showAttempt = ref(false);
const onTusStart = (e) => {
    showAttempt.value = true;
    console.log("STARTT",e);
    attemps.value.push(e);
}

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
    const findedIndex = attemps.value.findIndex((el) => el.filename == e.filename);
    if(findedIndex >= 0){
        attemps.value.splice(findedIndex,1);
        songs.value.push(attemps.value[findedIndex]);
    }

}
</script>

<style lang="scss" scoped>

</style>
