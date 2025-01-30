<script setup>
import {ref,reactive,onMounted, computed} from 'vue';
import {PersonIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon,LabelsIcon,AudioIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import {AppSwitchComponent} from '@/Components/Form'
import AppPagination from '@/Components/Navigation/AppPagination.vue';

const props = defineProps({
    data: {
        type: Object,
        required: true,
        default: () => ({
            platforms: [],
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

// Tüm platformlar için başlangıçta true olarak ayarla
const showPlatforms = ref({
    'spotify': true,
    'apple music': true,
    'youtube': true,
    'amazon': true,
    'deezer': true,
    'tidal': true,
    'pandora': true,
    'napster': true,
    'facebook': true,
    'instagram': true,
    'tiktok': true,
    'other': true,
    'others': true
});

const currentPage = ref(1);
const itemsPerPage = 10;

// Template kısmı için computed property'ler
const releases = computed(() => props.data?.releases ?? []);
const platforms = computed(() => props.data?.platforms ?? []);

// Filtrelenmiş ve sıralanmış releases computed property'si
const sortedReleases = computed(() => {
    return releases.value.map(album => {
        if (!album.platforms) return album;

        const visiblePlatforms = {};
        let totalEarning = 0;
        let totalQuantity = 0;
        let totalAllQuantity = 0;
        let totalPercentage = 0;

        // Tüm platformlar için toplam hesapla
        Object.keys(album.platforms).forEach(platform => {
            const platformData = album.platforms[platform];
            if (platformData) {
                const quantity = parseInt(platformData.quantity ?? "0");
                totalAllQuantity += isNaN(quantity) ? 0 : quantity;
            }
        });

        // Görünür platformlar için hesaplamalar
        Object.keys(album.platforms).forEach(platform => {
            // Tüm platformları ekle, görünürlük kontrolü yapmadan
            visiblePlatforms[platform] = album.platforms[platform];

            const platformData = album.platforms[platform];
            if (platformData) {
                // Earning hesapla
                const earningStr = platformData.earning?.replace(/[^0-9.]/g, '') ?? "0";
                const earning = parseFloat(earningStr);
                totalEarning += isNaN(earning) ? 0 : earning;

                // Stream sayısını topla
                const quantity = parseInt(platformData.quantity ?? "0");
                totalQuantity += isNaN(quantity) ? 0 : quantity;
            }
        });

        return {
            ...album,
            platforms: visiblePlatforms,
            total_earning: `$${totalEarning.toFixed(2)}`,
            total_quantity: totalQuantity.toLocaleString(),
            total_all_quantity: totalAllQuantity,
            totalPercentage
        };
    }).sort((a, b) => (b.total_all_quantity || 0) - (a.total_all_quantity || 0));
});

// Pagination için computed property
const paginatedReleases = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return sortedReleases.value.slice(startIndex, endIndex);
});

// Toplam sayfa sayısı
const totalPages = computed(() => {
    return Math.ceil(sortedReleases.value.length / itemsPerPage);
});

onMounted(() => {
    // Eğer data.platforms varsa, eksik olan platformları da true olarak ekle
    if (props.data?.platforms) {
        props.data.platforms.forEach(platform => {
            if (platform) {
                const key = platform.toLowerCase();
                if (!(key in showPlatforms.value)) {
                    showPlatforms.value[key] = true;
                }
            }
        });
    }
    console.log('PlatformsTab mounted:', {
        data: props.data,
        showPlatforms: showPlatforms.value
    });
});

const updateVisibility = (platform) => {
    const key = platform.toLowerCase();
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
                    <p class="label-medium c-strong-950">{{ __('control.finance.platforms') }}</p>
                    <p class="c-soft-400 label-sm">{{formattedDate}}</p>
                </div>
                <div class="flex gap-3">
                    <button><DownloadIcon color="var(--sub-600)" /></button>
                </div>
            </div>
            <hr>
            <div class="flex items-center gap-8">
                <div v-for="platform in platforms" class="flex items-center gap-1">
                    <div class="w-3 h-3 rounded-full" :class="{
                        'bg-spotify': platform.toLowerCase() === 'spotify',
                        'bg-apple': platform.toLowerCase() === 'apple music',
                        'bg-youtube': platform.toLowerCase() === 'youtube',
                        'bg-amazon': platform.toLowerCase() === 'amazon',
                        'bg-deezer': platform.toLowerCase() === 'deezer',
                        'bg-tidal': platform.toLowerCase() === 'tidal',
                        'bg-pandora': platform.toLowerCase() === 'pandora',
                        'bg-napster': platform.toLowerCase() === 'napster',
                        'bg-facebook': platform.toLowerCase() === 'facebook',
                        'bg-instagram': platform.toLowerCase() === 'instagram',
                        'bg-tiktok': platform.toLowerCase() === 'tiktok',
                        'bg-other': platform.toLowerCase() === 'other',
                        'bg-others': platform.toLowerCase() === 'others'
                    }"></div>
                    <span class="paragraph-xs c-strong-950">{{platform}}</span>
                    <AppSwitchComponent
                        v-model="showPlatforms[platform.toLowerCase()]"
                        @change="() => updateVisibility(platform)" />
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
                                    <span class="paragraph-xs c-sub-600">{{album.upc_code}}</span>
                                </div>
                            </div>
                        </td>
                        <td style="width:55%;">
                            <div class="flex items-center gap-4">
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{album.total_earning}}</span>
                                <span class="paragraph-xs c-sub-600 whitespace-nowrap">{{album.total_quantity}}</span>
                                <div class="w-[75%] ms-auto flex items-center gap-0.5 h-4 bg-gray-100 rounded-sm">
                                    <template v-if="album.platforms">
                                        <div v-for="platformKey in Object.keys(album.platforms)"
                                             :key="platformKey"
                                             v-show="showPlatforms[platformKey.toLowerCase()]"
                                             :class="['rounded-sm', 'h-full', 'bg-'+platformKey.toLowerCase().replace(/\s+/g, '-')]"
                                             :style="{width: (album.platforms[platformKey]?.percentage ?? 0)+'%'}">
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
.bg-spotify {
    background-color: #1DB954;
}
.bg-apple {
    background-color: #5856D6;
}
.bg-youtube {
    background-color: #FF0000;
}
.bg-amazon {
    background-color: #FF9900;
}
.bg-deezer {
    background-color: #00C7F2;
}
.bg-tidal {
    background-color: #000000;
}
.bg-pandora {
    background-color: #3668FF;
}
.bg-napster {
    background-color: #EE3333;
}
.bg-facebook {
    background-color:rgb(72, 152, 255);
}
.bg-instagram {
    background-color: #E4405F;
}
.bg-tiktok {
    background-color: #FF6B00;
}
.bg-other {
    background-color: #335CFF;
}
.bg-others {
    background-color: #335CFF;
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
