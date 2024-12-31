

<script setup>
import {ref} from 'vue';
import {PersonIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon,LabelsIcon,AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';

</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <PersonIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Sanatçıya Göre Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
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
                    <tr v-for="i in 6">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">Ed Stoltenberg</span>
                        </td>
                        <td style="width:65%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="24" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">60.90 %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">$1200.00,25</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">901.458.758</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <BookReadLineIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Albüme Göre Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
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
                    <tr v-for="i in 6">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">Ed Stoltenberg</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">48584295354283</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">Becky Keeling</span>
                        </td>
                        <td style="width:35%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="24" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">60.90 %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">$1200.00,25</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">901.458.758</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <AudioIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Parçaya Göre Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
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
                    <tr v-for="i in 6">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">Ed Stoltenberg</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">48584295354283</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">Becky Keeling</span>
                        </td>
                        <td style="width:35%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="24" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">60.90 %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">$1200.00,25</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">901.458.758</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <LabelsIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">Plak Şirketine Göre Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
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
                    <tr v-for="i in 6">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">Ed Stoltenberg</span>
                        </td>
                        <td style="width:65%;">
                            <div class="flex items-center gap-2">
                                <AppProgressIndicator height="8" color="#335CFF" :modelValue="24" />
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">60.90 %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">$1200.00,25</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<style  scoped>

</style>
