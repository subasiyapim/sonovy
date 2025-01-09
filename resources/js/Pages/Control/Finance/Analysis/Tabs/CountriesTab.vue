

<script setup>
import {ref,onMounted} from 'vue';
import {PersonIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon,LabelsIcon,AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import {AppSwitchComponent} from '@/Components/Form'
const props = defineProps({
    data : {

    },
    formattedDate:{

    }
})
const isSwitchOn = ref(false);

const showPlatforms = ref({});
onMounted(() => {
   props.data.countries.forEach(element => {
    showPlatforms.value[element.toLowerCase()] = true;
   });
});


</script>

<template>

    <div class="flex flex-col gap-6">
        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <PersonIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.country_wise_earnings') }}</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">

                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
                <div class="flex items-center gap-8">
                    <div v-for="country in data.countries" class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#122368]" :class="'bg-'+country.toLowerCase().replace(' ','-')"></div> <span class="paragraph-xs c-strong-950">{{country}}</span><AppSwitchComponent v-model="showPlatforms[country.toLowerCase()]" /></div>
                    <!-- <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#2547D0]"></div> <span class="paragraph-xs c-strong-950">Amerika</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#335CFF]"></div> <span class="paragraph-xs c-strong-950">İngiltere</span> <AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#6895FF]"></div> <span class="paragraph-xs c-strong-950">Portekiz </span><AppSwitchComponent v-model="isSwitchOn" /></div>
                    <div class="flex items-center gap-1"> <div class="w-3 h-3 rounded-full bg-[#C0D5FF]"></div> <span class="paragraph-xs c-strong-950">İspanya</span> <AppSwitchComponent v-model="isSwitchOn" /></div> -->
                </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.album_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 ">{{ __('control.finance.analysis.selected_stores_earnings_streams') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.total_earnings') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.total_streams') }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="country in data.releases">
                        <td class="py-3">
                           <div class="flex items-center gap-2">
                                <div class="bg-gray-200 w-8 h-8 rounded">
                                </div>
                                <div class="flex flex-col">
                                    <span class="label-sm c-strong-950">{{country.release_name}}</span>
                                    <span class="paragraph-xs c-sub-600">48584295354283</span>
                                </div>
                           </div>
                        </td>
                        <td style="width:55%;">
                            <div class="flex items-center gap-4">
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{country.total_earning}}</span>
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{country.total_quantity}}</span>
                                <div class="flex items-center gap-0.5 w-full h-2.5">
                                    <template  v-for="countryKey in Object.keys(country.countries)" >
                                        <div v-show="showPlatforms[countryKey.toLowerCase()]" class="rounded-sm h-full " :class="'bg-'+countryKey.toLowerCase().replace(' ','-')" :style="{width:country.countries[countryKey].percentage+'%'}"></div>
                                    </template>


                                </div>

                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{country.total_earning}}</span></td>
                        <td class="ps-3"> <span class="paragraph-xs c-sub-600">{{country.total_quantity}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>
</template>

<style  scoped>

</style>
