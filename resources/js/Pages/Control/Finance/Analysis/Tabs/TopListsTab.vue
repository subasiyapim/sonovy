

<script setup>
import {ref} from 'vue';
import {PersonIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon,LabelsIcon,AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
const props = defineProps({
    data : {

    },
    formattedDate:{

    },
    choosenDates:{},
});
</script>

<template>


    <div class="flex flex-col gap-6">
        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <PersonIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Sanatçıya Göre Gelir</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button><EyeOnIcon color="var(--sub-600)" /></button>
                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">Sanatçı Adı</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 !text-end">Oran</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Streams</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="artist in data.top_artists">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{artist.artist_name}}</span>
                        </td>
                        <td style="width:65%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="artist.percentage" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{artist.percentage}} </span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{artist.earning}}</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">{{artist.streams}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <BookReadLineIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Albüme Göre Gelir</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button><EyeOnIcon color="var(--sub-600)" /></button>
                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">Albüm Adı</td>
                        <td class="label-sm c-strong-950 !font-semibold">UPC</td>
                        <td class="label-sm c-strong-950 !font-semibold">Sanatçı</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 !text-end">Oran</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Streams</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="album in data.top_albums">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{album.album_name}}</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">{{album.upc_code}}</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">{{album.artist_name}}</span>
                        </td>
                        <td style="width:35%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="album.percentage" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{album.percentage}} %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{album.earning}}</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">{{album.streams}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <AudioIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Parçaya Göre Gelir</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button><EyeOnIcon color="var(--sub-600)" /></button>
                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">Parça Adı</td>
                        <td class="label-sm c-strong-950 !font-semibold">ISRC</td>
                        <td class="label-sm c-strong-950 !font-semibold">Sanatçı</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 !text-end">Oran</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Streams</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="song in data.top_songs">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{song.song_name}}</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">{{song.isrc_code}}</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">{{song.artist_name}}</span>
                        </td>
                        <td style="width:35%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="song.percentage" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{song.percentage}} %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{song.earning}}</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">{{song.streams}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <LabelsIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Plak Şirketine Göre Gelir</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button><EyeOnIcon color="var(--sub-600)" /></button>
                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">Plak Şirketi Ado Adı</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 !text-end">Oran</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="label in data.top_labels">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{label.label_name}}</span>
                        </td>
                        <td style="width:65%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="label.percentage" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{label.percentage}} %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{label.earning}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<style  scoped>

</style>
