<script setup>
import {ref, onMounted} from 'vue';
import {PersonIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon,Building2LineIcon,AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import moment from 'moment'
import {
    FinanceTopListsSongs,
    FinanceTopListsArtists,
    FinanceTopListsLabels,
    FinanceTopListsProducts
} from '@/Components/Dialog'

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    formattedDate: {
        type: String,
        required: true
    },
    choosenDates: {
        type: Array,
        default: () => []
    },
});

onMounted(() => {
    console.log('TopListsTab mounted', {
        data: props.data,
        hasTopArtists: props.data?.top_artists?.length > 0,
        hasTopAlbums: props.data?.top_albums?.length > 0,
        hasTopSongs: props.data?.top_songs?.length > 0,
        hasTopLabels: props.data?.top_labels?.length > 0,
        choosenDates: props.choosenDates,
        topAlbumsData: props.data?.top_albums
    });
});

const isFinanceTopListsSongs = ref(false)
const isFinanceTopListsArtists = ref(false)
const isFinanceTopListsLabels = ref(false)
const isFinanceTopListsProducts = ref(false);

const getDateRange = () => {
    const defaultStartDate = moment().subtract(1, 'year').format("YYYY-MM-DD");
    const defaultEndDate = moment().format("YYYY-MM-DD");

    if (Array.isArray(props.choosenDates) && props.choosenDates.length >= 2) {
        return {
            startDate: moment(props.choosenDates[0]).format("YYYY-MM-DD"),
            endDate: moment(props.choosenDates[1]).format("YYYY-MM-DD")
        };
    }

    return {
        startDate: defaultStartDate,
        endDate: defaultEndDate
    };
};

const goToArtists = () => {
    const { startDate, endDate } = getDateRange();
    const params = {
        slug: 'top_artists',
        request_type: 'download',
        start_date: startDate,
        end_date: endDate,
    };
    window.location.href = route('control.finance.analysis.show', params);
};

const gotoLabels = () => {
    const { startDate, endDate } = getDateRange();
    const params = {
        slug: 'top_labels',
        request_type: 'download',
        start_date: startDate,
        end_date: endDate,
    };
    window.location.href = route('control.finance.analysis.show', params);
};

const gotoProducts = () => {
    const { startDate, endDate } = getDateRange();
    const params = {
        slug: 'top_albums',
        request_type: 'download',
        start_date: startDate,
        end_date: endDate,
    };
    window.location.href = route('control.finance.analysis.show', params);
};

const gotoSongs = () => {
    const { startDate, endDate } = getDateRange();
    const params = {
        slug: 'top_songs',
        request_type: 'download',
        start_date: startDate,
        end_date: endDate,
    };
    window.location.href = route('control.finance.analysis.show', params);
};


</script>

<template>


    <div class="flex flex-col gap-6">
        <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <PersonIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.artists_earnings') }}</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button @click="isFinanceTopListsArtists = true"><EyeOnIcon color="var(--sub-600)" /></button>
                    <button @click="goToArtists"><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.artist_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold  !text-end">{{ __('control.finance.analysis.rate') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.earnings') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.streams') }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!data.top_artists || data.top_artists.length === 0">
                        <td colspan="4" class="py-4 text-center">
                            <span class="paragraph-xs c-sub-600">Henüz veri bulunmamaktadır.</span>
                        </td>
                    </tr>
                    <tr v-else v-for="artist in (data.top_artists || []).slice(0,5)">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{artist.artist_name}}</span>
                        </td>
                        <td style="width:65%;">
                            <div class="flex items-center gap-2">
                                <div class="w-[90%]"><AppProgressIndicator height="12" color="#335CFF" :modelValue="artist.percentage" /></div>
                                <span class="paragraph-xs !text-end flex-1 c-sub-600 whitespace-nowrap">{{artist.percentage}} </span>
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
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.albums_earnings') }}</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button @click="isFinanceTopListsProducts = true"><EyeOnIcon color="var(--sub-600)" /></button>
                    <button @click="gotoProducts"><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.album_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.upc') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.artist_name') }}</td>
                            <td class="label-sm c-strong-950 !font-semibold  !text-end">{{ __('control.finance.analysis.rate') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.earnings') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.streams') }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!data.top_albums || data.top_albums.length === 0">
                        <td colspan="6" class="py-4 text-center">
                            <span class="paragraph-xs c-sub-600">Henüz veri bulunmamaktadır.</span>
                        </td>
                    </tr>
                    <tr v-else v-for="album in data.top_albums.slice(0,5)">
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
                                <div class="w-96"><AppProgressIndicator height="12" color="#335CFF" :modelValue="album.percentage" /></div>
                                <span class="paragraph-xs !text-end flex-1 c-sub-600 whitespace-nowrap">{{album.percentage}} %</span>
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
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.songs_earnings') }}</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button @click="isFinanceTopListsSongs = true"><EyeOnIcon color="var(--sub-600)" /></button>
                    <button @click="gotoSongs"><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.song_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.isrc') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.artist_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 !text-end">{{ __('control.finance.analysis.rate') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.earnings') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.streams') }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!data.top_songs || data.top_songs.length === 0">
                        <td colspan="6" class="py-4 text-center">
                            <span class="paragraph-xs c-sub-600">Henüz veri bulunmamaktadır.</span>
                        </td>
                    </tr>
                    <tr v-else v-for="song in (data.top_songs || []).slice(0,5)">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{song.song_name}}</span>
                        </td>
                        <td class="py-2" style="width:130px;">
                            <span class="paragraph-xs c-sub-600">{{song.isrc_code}}</span>
                        </td>
                        <td class="py-2">
                            <span class="paragraph-xs c-sub-600">{{song.artist_name}}</span>
                        </td>
                        <td style="width:35%;">
                            <div class="flex items-center gap-2">
                                <div class="w-96"> <AppProgressIndicator height="12" color="#335CFF" :modelValue="song.percentage" /></div>
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
                    <Building2LineIcon color="var(--sub-600)" />
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.labels_earnings') }}</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button @click="isFinanceTopListsLabels = true"><EyeOnIcon color="var(--sub-600)" /></button>
                    <button @click="gotoLabels"><DownloadIcon color="var(--sub-600)" /></button>
                </div>

            </div>
            <hr>
            <table>
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.label_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 !text-end">{{ __('control.finance.analysis.rate') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.earnings') }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!data.top_labels || data.top_labels.length === 0">
                        <td colspan="3" class="py-4 text-center">
                            <span class="paragraph-xs c-sub-600">Henüz veri bulunmamaktadır.</span>
                        </td>
                    </tr>
                    <tr v-else v-for="label in (data.top_labels || []).slice(0,5)">
                        <td class="py-2">
                            <span class="label-sm c-strong-950">{{label.label_name}}</span>
                        </td>
                        <td style="width:65%;">
                            <div class="flex items-center gap-2">
                                <div class="w-[90%]"><AppProgressIndicator height="12" color="#335CFF" :modelValue="label.percentage" /></div>
                                <span class="paragraph-xs !text-end c-sub-600 whitespace-nowrap flex-1">{{label.percentage}} %</span>
                            </div>
                        </td>
                        <td class=" ps-3"> <span class="paragraph-xs c-sub-600">{{label.earning}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <FinanceTopListsSongs :formattedDates="formattedDate" :choosenDates="choosenDates" v-if="isFinanceTopListsSongs" v-model="isFinanceTopListsSongs"></FinanceTopListsSongs>
    <FinanceTopListsArtists :formattedDates="formattedDate" :choosenDates="choosenDates" v-if="isFinanceTopListsArtists" v-model="isFinanceTopListsArtists"></FinanceTopListsArtists>
    <FinanceTopListsLabels :formattedDates="formattedDate" :choosenDates="choosenDates" v-if="isFinanceTopListsLabels" v-model="isFinanceTopListsLabels"></FinanceTopListsLabels>
    <FinanceTopListsProducts :formattedDates="formattedDate" :choosenDates="choosenDates" v-if="isFinanceTopListsProducts" v-model="isFinanceTopListsProducts"></FinanceTopListsProducts>
</template>

<style  scoped>

</style>
