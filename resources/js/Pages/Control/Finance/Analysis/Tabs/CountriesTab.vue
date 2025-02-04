<script setup>
import {ref, computed, onMounted} from 'vue';
import {PersonIcon, EyeOnIcon, DownloadIcon, BookReadLineIcon, AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import {AppSwitchComponent} from '@/Components/Form';
import AppPagination from '@/Components/Navigation/AppPagination.vue';

const props = defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({
            countries: [],
            releases: []
        })
    },
    formattedDate: {
        type: String,
        required: true
    },
    choosenDates: {
        type: Object,
        default: () => ({})
    }
});

const showPlatforms = ref({
    'turkey': true,
    'united-states': true,
    'united-kingdom': true,
    'portugal': true,
    'spain': true,
    'others': true
});
const currentPage = ref(1);
const itemsPerPage = 20;

// Template kısmı için computed property'ler
const releases = computed(() => props.data?.releases ?? []);
const countries = computed(() => props.data?.countries ?? []);

// Filtrelenmiş ve sıralanmış releases computed property'si
const sortedReleases = computed(() => {
    return releases.value.map(album => {
        if (!album.countries) return album;

        const visibleCountries = {};
        let totalEarning = 0;
        let totalQuantity = 0;
        let totalAllQuantity = 0;
        let totalPercentage = 0;

        // Tüm ülkeler için toplam hesapla
        Object.keys(album.countries).forEach(country => {
            const countryData = album.countries[country];
            if (countryData) {
                const quantity = parseInt(countryData.quantity ?? "0");
                totalAllQuantity += isNaN(quantity) ? 0 : quantity;
            }
        });

        // Görünür ülkeler için hesaplamalar
        Object.keys(album.countries).forEach(country => {
            const countryKey = country.toLowerCase().replace(/\s+/g, '-');
            if (showPlatforms.value[countryKey]) {
                visibleCountries[country] = album.countries[country];

                const countryData = album.countries[country];
                if (countryData) {
                    // Earning hesapla
                    const earningStr = countryData.earning?.replace(/[^0-9.]/g, '') ?? "0";
                    const earning = parseFloat(earningStr);
                    totalEarning += isNaN(earning) ? 0 : earning;

                    // Stream sayısını topla
                    const quantity = parseInt(countryData.quantity ?? "0");
                    totalQuantity += isNaN(quantity) ? 0 : quantity;
                }
            }
        });

        // Ülkeleri yüzdeye göre sırala
        const sortedCountries = {};
        Object.entries(visibleCountries)
            .sort((a, b) => {
                const percentageA = parseFloat(a[1].percentage ?? 0);
                const percentageB = parseFloat(b[1].percentage ?? 0);
                return percentageB - percentageA;
            })
            .forEach(([country, data]) => {
                sortedCountries[country] = data;
            });

        return {
            ...album,
            countries: sortedCountries,
            total_earning: `$${totalEarning.toFixed(2)}`,
            total_quantity: totalQuantity.toLocaleString(),
            total_all_quantity: totalAllQuantity,
            totalPercentage
        };
    }).sort((a, b) => (b.total_all_quantity || 0) - (a.total_all_quantity || 0));
});

// Pagination için computed property
const paginatedReleases = computed(() => {
    console.log("asdasd",sortedReleases.value[0]);

    const startIndex = (currentPage.value - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return sortedReleases.value.sort((a,b) => parseInt(b.total_earning.substring(1)) - parseInt(a.total_earning.substring(1))).slice(startIndex, endIndex);
});

// Toplam sayfa sayısı
const totalPages = computed(() => {
    return Math.ceil(sortedReleases.value.length / itemsPerPage);
});

onMounted(() => {
    // Mevcut ülkeleri kontrol et ve eksik olanları ekle
    if (props.data?.countries) {
        props.data.countries.forEach(country => {
            if (country) {
                const key = country.toLowerCase().replace(/\s+/g, '-');
                if (!(key in showPlatforms.value)) {
                    showPlatforms.value[key] = true;
                }
            }
        });
    }

    console.log('CountriesTab mounted:', {
        countries: countries.value,
        showPlatforms: showPlatforms.value,
        releases: releases.value
    });
});

const updateVisibility = (country) => {
    const key = country.toLowerCase().replace(/\s+/g, '-');
    showPlatforms.value[key] = !showPlatforms.value[key];
};

const changePage = (page) => {
    currentPage.value = page;
};
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
            <div v-if="countries.length > 0" class="flex items-center gap-8 flex-wrap">
                <div v-for="country in countries"
                     :key="country"
                     class="flex items-center gap-1">
                    <div class="w-3 h-3 rounded-full"
                         :class="'bg-'+country.toLowerCase().replace(/\s+/g, '-')">
                    </div>
                    <span class="paragraph-xs c-strong-950">{{country}}</span>
                    <AppSwitchComponent
                        v-model="showPlatforms[country.toLowerCase().replace(/\s+/g, '-')]"
                        @change="() => updateVisibility(country)" />
                </div>
            </div>
            <hr>
            <table class="w-full">
                <thead>
                    <tr>
                        <td class="label-sm c-strong-950 !font-semibold">{{ __('control.finance.analysis.album_name') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold pe-3 ">{{ __('control.finance.analysis.selected_stores_earnings_streams') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.total_earnings') }}</td>
                        <td class="label-sm c-strong-950 !font-semibold ps-3">{{ __('control.finance.analysis.total_streams') }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="album in paginatedReleases">
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="bg-gray-200 w-8 h-8 rounded overflow-hidden">
                                    <img v-if="album.product?.image?.thumb"
                                         :src="album.product.image.thumb"
                                         :alt="album.release_name"
                                         class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <AudioIcon color="var(--sub-400)" />
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="label-sm c-strong-950">{{album.release_name}}</span>
                                    <span class="paragraph-xs c-sub-600">UPC:{{album.upc_code}}</span>
                                </div>
                            </div>
                        </td>
                        <td style="width:55%;">
                            <div class="flex items-center gap-4">
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap" style="min-width: 60px;">{{album.total_earning}}</span>
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap" style="min-width: 40px;">{{album.total_quantity}}</span>
                                <div class="w-[75%] ms-auto flex items-center gap-0.5 h-4 bg-gray-100 rounded-sm">
                                    <template v-if="album.countries">
                                        <div v-for="countryKey in Object.keys(album.countries)"
                                             :key="countryKey"
                                             v-show="showPlatforms[countryKey.toLowerCase().replace(/\s+/g, '-')]"
                                             :class="['rounded-sm', 'h-full', 'bg-'+countryKey.toLowerCase().replace(/\s+/g, '-')]"
                                             :style="{width: (album.countries[countryKey]?.percentage ?? 0)+'%'}">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </td>
                        <td class="ps-3">
                            <span class="paragraph-xs c-sub-600">{{album.total_earning ?? 0}}</span>
                        </td>
                        <td class="ps-3">
                            <span class="paragraph-xs c-sub-600">{{album.total_quantity ?? 0}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="flex justify-center mt-4">
                <AppPagination
                    :total-pages="totalPages"
                    :current-page="currentPage"
                    @page-change="changePage"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-turkey {
    background-color: #E30A17;
}
.bg-united-states {
    background-color: #0052B4;
}
.bg-united-kingdom {
    background-color: #012169;
}
.bg-portugal {
    background-color: #006600;
}
.bg-spain {
    background-color: #F1BF00;
}


/* Progress bar container */
.bg-gray-100 {
    background-color: #F3F4F6;
}

/* Transition effects */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
.duration-300 {
    transition-duration: 300ms;
}
</style>
