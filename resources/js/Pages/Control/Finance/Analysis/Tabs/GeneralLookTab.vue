<script setup>
import {ref, computed, onMounted} from 'vue';
import {
  FileChartLineIcon,
  SpotifyIcon,
  YoutubeIcon,
  AppleMusicIcon,
  InfoFilledIcon,
  WorldIcon,
  EyeOnIcon,
  DownloadIcon,
  BookReadLineIcon
} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import {
  FinanceIncomePlatforms,
  FinanceIncomeCountries,
  FinanceIncomeSales,
  FinanceIncomeProducts
} from '@/Components/Dialog';
import {AppSwitchComponent} from '@/Components/Form'
import Vue3Apexcharts from 'vue3-apexcharts'
import VueTippy from 'vue-tippy';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';

const showYoutubeFremium = ref(false);
import {router} from '@inertiajs/vue3';
import moment from 'moment';
import  'moment/dist/locale/tr';
moment.locale('tr');

const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({
      monthly_net_earnings: {
        items: {},
        total: {}
      },
      earning_from_platforms: {},
      earning_from_countries: {},
      earning_from_sales_type: {},
      spotify_discovery_mode_earnings: {
        items: {},
        total: {}
      },
      earning_from_youtube: {}
    })
  },
  formattedDate: {
    type: String,
    required: true
  }
});

const isFinanceIncomePlatforms = ref(false);
const isFinanceIncomeCountries = ref(false);
const isFinanceIncomeSales = ref(false);
const isFinanceIncomeProducts = ref(false);

const monthlyData = computed(() => {
  if (!props.data || !props.data.monthly_net_earnings || !props.data.monthly_net_earnings.items) {
    console.log('Monthly data is missing:', props.data);
    return {};
  }
  return props.data.monthly_net_earnings.items;
});

const monthlyTotals = computed(() => {
  if (!props.data || !props.data.monthly_net_earnings || !props.data.monthly_net_earnings.total) {
    console.log('Monthly totals is missing:', props.data);
    return {};
  }
  return props.data.monthly_net_earnings.total;
});

const spotifyDiscoveryData = computed(() => {
  if (!props.data?.spotify_discovery_mode_earnings?.items) {
    console.log('Spotify Discovery Mode data is missing:', props.data);
    return {};
  }
  return props.data.spotify_discovery_mode_earnings.items;
});

const youtubeData = computed(() => {
  if (!props.data?.earning_from_youtube) {
    console.log('Youtube data is missing:', props.data);
    return {};
  }
  return props.data.earning_from_youtube;
});

const getDateRange = () => {
  const urlParams = new URLSearchParams(window.location.search);
  const startDate = urlParams.get('start_date');
  const endDate = urlParams.get('end_date');

  if (startDate && endDate) {
    return {
      startDate: moment(startDate).format('YYYY-MM-DD'),
      endDate: moment(endDate).format('YYYY-MM-DD')
    };
  }

  return {
    startDate: moment().subtract(1, 'month').format('YYYY-MM-DD'),
    endDate: moment().format('YYYY-MM-DD')
  };
};

const goToPlatformCSV = async () => {
  try {
    const { startDate, endDate } = getDateRange();

    const params = {
      slug: 'earning_from_platforms',
      request_type: 'download',
      start_date: startDate,
      end_date: endDate
    };

    window.location.href = route('control.finance.analysis.show', params);
  } catch (error) {
    console.error('Download error:', error);
  }
};

const goToCountriesCSV = async () => {
  try {
    const { startDate, endDate } = getDateRange();

    const params = {
      slug: 'earning_from_countries',
      request_type: 'download',
      start_date: startDate,
      end_date: endDate
    };

    window.location.href = route('control.finance.analysis.show', params);
  } catch (error) {
    console.error('Download error:', error);
  }
};

const goToSalesCSV = async () => {
  try {
    const { startDate, endDate } = getDateRange();

    const params = {
      slug: 'earning_from_sales_type',
      request_type: 'download',
      start_date: startDate,
      end_date: endDate
    };

    window.location.href = route('control.finance.analysis.show', params);
  } catch (error) {
    console.error('Download error:', error);
  }
};

const goToTrendingAlbumsCSV = async () => {
  try {
    const { startDate, endDate } = getDateRange();

    const params = {
      slug: 'trending_albums',
      request_type: 'download',
      start_date: startDate,
      end_date: endDate
    };

    window.location.href = route('control.finance.analysis.show', params);
  } catch (error) {
    console.error('Download error:', error);
  }
};

const platformsData = computed(() => {
  const data = props.data?.earning_from_platforms || [];
  if (Array.isArray(data)) {
    return data.reduce((acc, item) => {
      if (item && typeof item === 'object') {
        const platformName = item.platform || item.name || 'Diğer';
        acc[platformName] = {
          earning: Number(item.earning || 0),
          percentage: Number(item.percentage || 0)
        };
      }
      return acc;
    }, {});
  }
  return {};
});

const countriesData = computed(() => {
  const data = props.data?.earning_from_countries || [];
  if (Array.isArray(data)) {
    return data.reduce((acc, item) => {
      if (item && typeof item === 'object') {
        const countryName = item.country || item.name || 'Diğer';
        acc[countryName] = {
          earning: Number(item.earning || 0),
          percentage: Number(item.percentage || 0)
        };
      }
      return acc;
    }, {});
  }
  return {};
});

const formatCurrency = (value) => {
  if (typeof value === 'number') {
    return new Intl.NumberFormat('tr-TR', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(value);
  }
  return '$0.00';
};

const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: Object.keys(platformsData.value),
  colors: ['#5BCF82', '#F9C74F', '#F94144', '#577590', '#4C956C', '#2D6A4F'],
  legend: {
    show: true,
    position: 'right',
    fontSize: '13px',
    formatter: function(seriesName, opts) {
      const value = opts.w.globals.series[opts.seriesIndex];
      return `${seriesName}: ${formatCurrency(value)}`;
    }
  },
  dataLabels: {
    enabled: false,
    formatter: function(val, opts) {
      return opts.w.config.labels[opts.seriesIndex];
    },
    style: {
      fontSize: '13px',
      colors: ['#fff'],
    },
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%'
      }
    }
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff'],
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function (val) {
        return formatCurrency(val);
      },
    },
  },
}));

const chartSeries = computed(() => {
  const values = Object.values(platformsData.value).map(p => Number(p.earning || 0));
  console.log('Platform values:', values);
  return values;
});

const countriesChartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: Object.keys(countriesData.value),
  colors: ['#5BCF82', '#F9C74F', '#F94144', '#577590', '#4C956C', '#2D6A4F'],
  legend: {
    show: true,
    position: 'right',
    fontSize: '13px',
    formatter: function(seriesName, opts) {
      const value = opts.w.globals.series[opts.seriesIndex];
      return `${seriesName}: ${formatCurrency(value)}`;
    }
  },
  dataLabels: {
    enabled: false,
    formatter: function(val, opts) {
      return opts.w.config.labels[opts.seriesIndex];
    },
    style: {
      fontSize: '13px',
      colors: ['#fff'],
    },
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%'
      }
    }
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff'],
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function (val) {
        return formatCurrency(val);
      },
    },
  },
}));

const countriesChartSeries = computed(() => {
  const values = Object.values(countriesData.value).map(c => Number(c.earning || 0));
  console.log('Country values:', values);
  return values;
});

const salesChartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: props.data?.earning_from_sales_type ? Object.keys(props.data.earning_from_sales_type) : [],
  colors: ['#5BCF82', '#F9C74F', '#F94144', '#577590'],
  legend: {
    show: true,
    position: 'right',
  },
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '13px',
      colors: ['#fff'],
    },
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff'],
  },
  tooltip: {
    enabled: true,
    y: {
      formatter: function (val) {
        return formatCurrency(val);
      },
    },
  },
}));

const salesChartSeries = computed(() =>
  props.data?.earning_from_sales_type ?
    Object.values(props.data.earning_from_sales_type).map((e) => e.earning) :
    []
);

// Debug için onMounted hook'u güncelleme
onMounted(() => {
  console.log('Component mounted with data:', {
    data: props.data,
    monthlyData: monthlyData.value,
    monthlyTotals: monthlyTotals.value,
    platformsData: platformsData.value,
    countriesData: countriesData.value,
    chartSeries: chartSeries.value,
    countriesChartSeries: countriesChartSeries.value,
    salesChartSeries: salesChartSeries.value
  });

  console.log('Raw Platform Data:', props.data?.earning_from_platforms);
  console.log('Raw Countries Data:', props.data?.earning_from_countries);

  if (props.data?.earning_from_platforms) {
    const samplePlatform = props.data.earning_from_platforms[0];
    console.log('Sample Platform Item:', {
      platform: samplePlatform.platform,
      name: samplePlatform.name,
      earning: samplePlatform.earning,
      rawEarning: typeof samplePlatform.earning,
      parsedEarning: Number(samplePlatform.earning)
    });
  }

  if (props.data?.earning_from_countries) {
    const sampleCountry = props.data.earning_from_countries[0];
    console.log('Sample Country Item:', {
      country: sampleCountry.country,
      name: sampleCountry.name,
      earning: sampleCountry.earning,
      rawEarning: typeof sampleCountry.earning,
      parsedEarning: Number(sampleCountry.earning)
    });
  }

  console.log('Processed Platform Data:', platformsData.value);
  console.log('Processed Countries Data:', countriesData.value);
  console.log('Platform Chart Series:', chartSeries.value);
  console.log('Countries Chart Series:', countriesChartSeries.value);
});

</script>

<template>


  <div class="flex flex-col gap-6">
    <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
      <div class="flex items-center">
        <div class="flex items-center gap-2 flex-1">
          <FileChartLineIcon color="var(--sub-600)"></FileChartLineIcon>
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.total_earnings') }}</p>
          <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
        </div>
        <div class="flex items-center gap-2">
                    <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.spotify') }}</span>
                    <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.apple_music') }}</span>
                    <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.youtube') }}</span>
                    <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.other') }}</span>
        </div>
      </div>
      <hr>
      <div class="flex justify-between">
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <SpotifyIcon/>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">{{ __('control.finance.analysis.spotify') }}</span>
            <span class="label-medium c-strong-950">{{ monthlyTotals?.Spotify?.earning ?? 0 }}</span>
          </div>
        </div>
        <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <AppleMusicIcon/>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">{{ __('control.finance.analysis.apple_music') }}</span>
            <span class="label-medium c-strong-950">{{ monthlyTotals?.Amazon?.earning ?? 0 }}</span>
          </div>
        </div>
        <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <YoutubeIcon/>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">{{ __('control.finance.analysis.youtube') }}</span>
            <span class="label-medium c-strong-950">{{ monthlyTotals?.Youtube?.earning ?? 0 }}</span>
          </div>
        </div>
        <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <span class="bg-[#717784] w-4 h-4 rounded-full"></span>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">{{ __('control.finance.analysis.other') }}</span>
            <span class="label-medium c-strong-950">{{ monthlyTotals?.other?.earning ?? 0 }}</span>
          </div>
        </div>
      </div>
      <hr>
      <div class="flex gap-3">
        <div class="flex flex-col gap-5">
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.20k') }}</span>
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.15k') }}</span>
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.10k') }}</span>
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.0') }}</span>
        </div>
        <div class="flex gap-4 flex-1">
          <template v-if="Object.keys(monthlyData).length > 0">
            <div v-for="key in Object.keys(monthlyData)"
                 :key="key"
                 class="h-80 flex-1 flex flex-col items-center justify-between">
              <tippy :allowHtml="true" :interactiveBorder="30" theme="light" followCursor :sticky="true"
                     :interactive="false">
                <div class="h-72 flex flex-col justify-end w-full gap-0.5 w-full">
                  <div class="bg-weak-50 flex items-end justify-center h-10 min-w-10">
                    <span class="c-sub-600 label-sm !text-[10px]">
                      {{ monthlyData[key]?.total ?? 0 }}
                    </span>
                  </div>
                  <template v-if="monthlyData[key]">
                    <div v-for="p in Object.keys(monthlyData[key])"
                         :key="p"
                         class="w-full"
                         :style="{height: (monthlyData[key][p]?.percentage ?? 0) + '%'}"
                         :class="'bg-'+p.toLowerCase()">
                    </div>
                  </template>
                </div>
                <template #content>
                  <div class="flex flex-col gap-2 w-64 p-1">
                    <p class="label-sm c-strong-950">{{key}}</p>
                    <template v-if="monthlyData[key]">
                      <div v-for="platform in Object.keys(monthlyData[key])"
                           :key="platform"
                           class="flex items-center gap-2">
                        <SpotifyIcon v-if="platform=='Spotify'"/>
                        <YoutubeIcon v-else-if="platform == 'Youtube'"/>
                        <AppleMusicIcon v-else-if="platform == 'Apple'"/>
                        <span v-else-if="platform == 'other'" class="bg-[#717784] w-4 h-4 rounded-full"></span>
                        <p class="paragraph-sm c-strong-950 flex-1">{{ platform }}</p>
                        <div class="border border-soft-200 rounded px-2 py-1">
                          <p class="paragraph-xs c-sub-600">{{ monthlyData[key][platform]?.earning ?? 0 }}</p>
                        </div>
                      </div>
                    </template>
                  </div>
                </template>
              </tippy>
              <span class="paragraph-xs c-sub-600 !text-center">{{ key }}</span>
            </div>
          </template>
          <div v-else class="w-full flex justify-center items-center">
            <p class="text-gray-500">Veri bulunamadı</p>
          </div>
        </div>
      </div>
    </div>


    <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
      <div class="flex items-center">
        <div class="flex items-center gap-2 flex-1">
          <SpotifyIcon color="var(--sub-600)"></SpotifyIcon>
                    <p class="label-medium c-strong-950">{{ __('control.finance.analysis.spotify_discovery_mode') }}</p>
          <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
        </div>

      </div>
      <hr>
      <div class="flex gap-4 items-center">

        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-spotify"></div>
                    <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.estimated_earnings_md_spotify_discovery_mode_with_catalog_optimization') }}</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-[#BDECCD]"></div>
                    <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.regular_spotify_revenue') }}</span>
        </div>
      </div>
      <hr>
      <div class="flex gap-3">
        <div class="flex flex-col gap-5">
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.20k') }}</span>
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.15k') }}</span>
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.10k') }}</span>
                    <span class="paragraph-xs c-sub-600">{{ __('control.finance.analysis.0') }}</span>
        </div>
        <div class="flex gap-4 flex-1">
          <template v-if="Object.keys(spotifyDiscoveryData).length > 0">
            <div v-for="key in Object.keys(spotifyDiscoveryData)"
                 :key="key"
                 class="h-80 flex-1 flex flex-col items-center justify-between">
              <tippy :allowHtml="true" :interactiveBorder="30" theme="light" followCursor :sticky="true"
                     :interactive="false">
                <div class="h-72 flex flex-col justify-end w-full gap-0.5 w-full">
                  <div class="bg-weak-50 flex items-end justify-center h-10 min-w-10">
                    <span class="c-sub-600 label-sm !text-[10px]">
                      {{ spotifyDiscoveryData[key]?.total ?? 0 }}
                    </span>
                  </div>
                  <template v-if="spotifyDiscoveryData[key]">
                    <div class="w-full bg-spotify"
                         :style="{height: (spotifyDiscoveryData[key].promotion ? 40 : 0) + '%'}">
                    </div>
                    <div class="w-full bg-[#BDECCD]"
                         :style="{height: (spotifyDiscoveryData[key].earning ? 40 : 0) + '%'}">
                    </div>
                  </template>
                </div>
                <template #content>
                  <div class="flex flex-col gap-2 w-64 p-1">
                    <p class="label-sm c-strong-950">{{key}}</p>
                    <template v-if="spotifyDiscoveryData[key]">
                      <div class="flex items-center gap-2">
                        <div class="bg-spotify w-3 h-3 rounded-full"></div>
                        <p class="paragraph-sm c-strong-950 flex-1">Discovery Mode</p>
                        <div class="border border-soft-200 rounded px-2 py-1">
                          <p class="paragraph-xs c-sub-600">{{ spotifyDiscoveryData[key].promotion ?? 0 }}</p>

                      </div>
                      <div class="flex items-center gap-2">
                        <div class="bg-[#BDECCD] w-3 h-3 rounded-full"></div>
                        <p class="paragraph-sm c-strong-950 flex-1">Regular</p>
                        <div class="border border-soft-200 rounded px-2 py-1">
                          <p class="paragraph-xs c-sub-600">{{ spotifyDiscoveryData[key].earning ?? 0 }}</p>
                        </div>
                      </div>
                    </template>
                  </div>
                </template>
              </tippy>
              <span class="paragraph-xs c-sub-600 !text-center">{{ key }}</span>
            </div>
          </template>
          <div v-else class="w-full flex justify-center items-center">
            <p class="text-gray-500">Veri bulunamadı</p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-6">
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <BookReadLineIcon color="var(--sub-600)"/>
                        <p class="label-medium c-strong-950">{{ __('control.finance.analysis.stores_earnings') }}</p>
            <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
          </div>
          <div class="flex gap-3">
            <button @click="isFinanceIncomePlatforms = !isFinanceIncomePlatforms">
              <EyeOnIcon color="var(--sub-600)"/>
            </button>
            <button @click="goToPlatformCSV">
              <DownloadIcon color="var(--sub-600)"/>
            </button>
          </div>

        </div>
        <hr>
        <Vue3Apexcharts height="250" :options="chartOptions" :series="chartSeries"></Vue3Apexcharts>

      </div>
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <WorldIcon color="var(--sub-600)"/>
                        <p class="label-medium c-strong-950">{{ __('control.finance.analysis.country_wise_earnings') }}</p>
            <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
          </div>
          <div class="flex gap-3">
            <button @click="isFinanceIncomeCountries = !isFinanceIncomeCountries">
              <EyeOnIcon color="var(--sub-600)"/>
            </button>
            <button @click="goToCountriesCSV">
              <DownloadIcon color="var(--sub-600)"/>
            </button>
          </div>

        </div>
        <hr>
        <Vue3Apexcharts height="250" :options="countriesChartOptions" :series="countriesChartSeries"></Vue3Apexcharts>

      </div>
    </div>

    <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
      <div class="flex items-center">
        <div class="flex items-center gap-2 flex-1">
          <YoutubeIcon/>
                    <p class="label-medium c-strong-950 ms-2">{{ __('control.finance.analysis.youtube_total_earnings_top_points') }}</p>
          <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
        </div>
        <span class="label-sm c-strong-950">$23.758,00</span>

      </div>
      <hr>
      <div class="flex gap-4 items-center">
                <span class="paragraph-xs c-strong-950">{{ __('control.finance.analysis.show_premium_fremium_earnings') }}</span>
        <AppSwitchComponent v-model="showYoutubeFremium"/>
      </div>
      <hr>
      <div class="flex flex-col gap-3" v-if="!showYoutubeFremium">
        <template v-if="Object.keys(youtubeData).length > 0">
          <div v-for="key in Object.keys(youtubeData)" :key="key">
            <span class="label-xs">{{ key }}</span>
            <tippy :allowHtml="true" :maxWidth="600" theme="light" followCursor :sticky="true" :interactive="false">
              <div class="flex items-center gap-3">
                <div class="flex-1">
                  <AppProgressIndicator :height="12" :modelValue="youtubeData[key]?.percentage ?? 0" color="#D02533"/>
                </div>
                <span class="paragraph-xs c-strong-950">{{ youtubeData[key]?.earning ?? 0 }}</span>
              </div>
              <template #content>
                <div class="flex flex-col gap-2 w-96 p-1">
                  <div class="flex items-center">
                                   <div> <YoutubeIcon /></div>
                                    <p class="label-sm c-strong-950 flex-1 ms-2">{{ __('control.finance.analysis.january_2024') }}</p>
                                    <p class="label-sm c-strong-950 ">{{data.earning_from_youtube[key].earning}}</p>

                  </div>
                  <div class="flex items-start gap-2 bg-[#F2F5F8] py-2 px-4 mt-2 rounded-lg">
                    <InfoFilledIcon width="24" class="mt-1" color="#717784"/>
                                    <span class="c-strong-950 c-sub-600">{{ __('control.finance.analysis.ad_supported_videos_youtube_music_and_youtube_premium_paid_subscription_earnings') }}</span>
                  </div>
                </div>
              </template>
            </tippy>
          </div>
        </template>
        <div v-else class="text-center py-4">
          <p class="text-gray-500">Youtube verisi bulunamadı</p>
        </div>
      </div>

      <div v-else>
        <div class="flex items-center gap-3">
          <div class="flex gap-2 items-center c-strong-950 paragraph-xs">
            <div class="w-2 h-2 rounded-full bg-youtube-premium"></div>
            {{ __('control.finance.analysis.premium') }}
          </div>
          <div class="flex gap-2 items-center c-strong-950 paragraph-xs">
            <div class="w-2 h-2 rounded-full bg-youtube"></div>
            {{ __('control.finance.analysis.freemium') }}
          </div>
          <div class="flex gap-2 items-center c-strong-950 paragraph-xs">
            <div class="w-2 h-2 rounded-full bg-other"></div>
            {{ __('control.finance.analysis.other') }}
          </div>
        </div>


        <tippy v-for="i in 3" :allowHtml="true" :maxWidth="600" theme="light" followCursor :sticky="true"
               :interactive="false">
          <div class="mt-3">
                        <p class="label-sm c-strong-950 mb-1">{{ __('control.finance.analysis.youtube_official_content') }}</p>
            <div class="flex items-center gap-1 h-4 w-full">
              <div class="h-full rounded bg-youtube-premium w-[12%]"></div>
              <div class="h-full rounded bg-youtube w-[68%]"></div>
              <div class="h-full rounded bg-other w-[20%]"></div>
            </div>

          </div>
          <template #content>
            <div class="flex flex-col gap-2 w-96 p-1">
              <div class="flex items-center">
                <YoutubeIcon/>
                                <p class="label-sm c-strong-950 flex-1 ms-2">{{ __('control.finance.analysis.youtube_official_content') }}</p>
                <p class="label-sm c-strong-950 ">$500</p>

              </div>
              <div class="flex flex-col items-start gap-3">
                <div class="flex gap-2 items-center w-full">
                  <div class="w-2 h-2 rounded-full bg-youtube-premium"></div>
                  <div class="flex-1"><p class=" paragraph-xs c-strong-950">{{ __('control.finance.analysis.premium') }}</p></div>
                  <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">$500,57</p>
                  </div>

                </div>
                <div class="flex gap-2 items-center w-full">
                  <div class="w-2 h-2 rounded-full bg-youtube"></div>
                  <div class="flex-1"><p class="flex-1 paragraph-xs c-strong-950">{{ __('control.finance.analysis.freemium') }}</p></div>
                  <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">$500,57</p>
                  </div>

                </div>
                <div class="flex gap-2 items-center w-full">
                  <div class="w-2 h-2 rounded-full bg-other"></div>
                  <div class="flex-1"><p class="flex-1 paragraph-xs c-strong-950">{{ __('control.finance.analysis.other') }}</p></div>
                  <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">$500,57</p>
                  </div>

                </div>

              </div>


            </div>
          </template>
        </tippy>

      </div>
    </div>


    <div class="flex gap-6">
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <BookReadLineIcon color="var(--sub-600)"/>
                        <p class="label-medium c-strong-950">{{ __('control.finance.analysis.sales_type_earnings') }}</p>
            <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
          </div>
          <div class="flex gap-3">
            <button @click="isFinanceIncomeSales = !isFinanceIncomeSales">
              <EyeOnIcon color="var(--sub-600)"/>
            </button>
            <button @click="goToSalesCSV">
              <DownloadIcon color="var(--sub-600)"/>
            </button>
          </div>

        </div>
        <hr>

        <Vue3Apexcharts height="250" :options="salesChartOptions" :series="salesChartSeries"></Vue3Apexcharts>


      </div>
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <WorldIcon color="var(--sub-600)"/>
                        <p class="label-medium c-strong-950">{{ __('control.finance.analysis.trends_albums') }}</p>
            <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
          </div>
          <div class="flex gap-3">
            <button @click="isFinanceIncomeProducts = true">
              <EyeOnIcon color="var(--sub-600)"/>
            </button>
            <button @click="goToTrendingAlbumsCSV">
              <DownloadIcon color="var(--sub-600)"/>
            </button>
          </div>

        </div>
        <hr>
        <table>
          <tbody>
          <tr v-for="album in data.trending_albums" class="">
            <td class="paragraph-xs c-sub-600 py-1.5">{{ album.release_name || album.product_name }}</td>
            <td class="paragraph-xs text-[#377C4E]">{{ formatCurrency(album.earning) }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <FinanceIncomePlatforms :formattedDates="formattedDate" v-model="isFinanceIncomePlatforms"
                          v-if="isFinanceIncomePlatforms"></FinanceIncomePlatforms>
  <FinanceIncomeCountries :formattedDates="formattedDate" v-model="isFinanceIncomeCountries"
                          v-if="isFinanceIncomeCountries"></FinanceIncomeCountries>
  <FinanceIncomeSales :formattedDates="formattedDate" v-model="isFinanceIncomeSales"
                      v-if="isFinanceIncomeSales"></FinanceIncomeSales>
  <FinanceIncomeProducts :formattedDates="formattedDate" v-model="isFinanceIncomeProducts"
                         v-if="isFinanceIncomeProducts"></FinanceIncomeProducts>
</template>

<style scoped>

</style>
