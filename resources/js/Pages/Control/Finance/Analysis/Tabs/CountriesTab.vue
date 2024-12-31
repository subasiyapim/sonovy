

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
                    <p class="label-medium c-strong-950">Ülkeye Göre Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                </div>
                <div class="flex gap-3">

                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#122368]"></div> <span class="paragraph-xs c-strong-950">Türkiye</span><AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#2547D0]"></div> <span class="paragraph-xs c-strong-950">Amerika</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#335CFF]"></div> <span class="paragraph-xs c-strong-950">İngiltere</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#6895FF]"></div> <span class="paragraph-xs c-strong-950">Portekiz </span><AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#C0D5FF]"></div> <span class="paragraph-xs c-strong-950">İspanya</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
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
                                    <div class="rounded-sm h-full bg-[#122368] w-[10%]"></div>
                                    <div class="rounded-sm h-full bg-[#C0D5FF] w-[5%]"></div>
                                    <div class="rounded-sm h-full bg-[#6895FF] w-[25%]"></div>
                                    <div class="rounded-sm h-full bg-[#2547D0] w-[50%]"></div>
                                    <div class="rounded-sm h-full bg-[#335CFF] w-[10%]"></div>
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
