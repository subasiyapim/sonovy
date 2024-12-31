

<script setup>
import {ref} from 'vue';
import {PersonIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon,LabelsIcon,AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import {AppSwitchComponent} from '@/Components/Form'

const isSwitchOn = ref(false);
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <PersonIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Mağazaya Göre Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                </div>
                <div class="flex gap-3">

                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-spotify"></div> <span class="paragraph-xs c-strong-950">Spotify</span><AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-facebook"></div> <span class="paragraph-xs c-strong-950">Facebook</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-youtube"></div> <span class="paragraph-xs c-strong-950">Youtube</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-apple-music"></div> <span class="paragraph-xs c-strong-950">Apple Music </span><AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-tiktok"></div> <span class="paragraph-xs c-strong-950">Tiktok</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">Albüm Adı</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 ">Seçili Mağazalar Gelir & Streams</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Top. Gelir</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">Top. Streams</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="i in 6">
                        <td class="py-3">
                           <div class="flex items-center gap-2">
                                <div class="bg-gray-200 w-8 h-8 rounded">
                                </div>
                                <div class="flex flex-col">
                                    <span class="label-sm c-strong-950">Alem Alem İçinde</span>
                                    <span class="paragraph-xs c-sub-600">48584295354283</span>
                                </div>
                           </div>
                        </td>
                        <td style="width:55%;">
                            <div class="flex items-center gap-4">
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">$120.000,00</span>
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">901.458.758</span>
                                <div class="flex items-center gap-0.5 w-full h-2.5">
                                    <div class="rounded-sm h-full bg-spotify w-[10%]"></div>
                                    <div class="rounded-sm h-full bg-tiktok w-[5%]"></div>
                                    <div class="rounded-sm h-full bg-apple-music w-[25%]"></div>
                                    <div class="rounded-sm h-full bg-facebook w-[50%]"></div>
                                    <div class="rounded-sm h-full bg-youtube w-[10%]"></div>
                                </div>

                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">$1200.00,25</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">901.458.758</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>
</template>

<style  scoped>

</style>
