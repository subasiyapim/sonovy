<template>
 <DragUploadInput label="Albüm Kapağı" note="JPEG, PNG ve WEBP"></DragUploadInput>
                    <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <BroadcastTitleIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Albüm Adı</p>
                            <span class="label-sm c-strong-950">Geleceğe Doğru</span>
                       </div>
                    </div>

                     <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <PersonIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Sanatçılar</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>
                    </div>
                     <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <PersonIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Düet</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>
                    </div>

                     <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <GenreIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Tarz</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>
                    </div>



                    <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <CalendarIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Yayın Tarihi</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>


                    </div>



                    <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <DurationIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Toplam Süre</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>


                    </div>

</template>

<script setup>
import {DragUploadInput} from '@/Components/Form';
import {AddIcon,BroadcastTitleIcon,PersonIcon,GenreIcon,CalendarIcon, DurationIcon} from '@/Components/Icons';

</script>

<style lang="scss" scoped>

</style>
