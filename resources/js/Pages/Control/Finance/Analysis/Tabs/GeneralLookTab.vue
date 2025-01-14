<script setup>
import {ref} from 'vue';
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

const showYoutubeFremium = ref(false);
import {router} from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
  data: {},
  formattedDate: {},
  choosenDates: {},
})
const isFinanceIncomePlatforms = ref(false);
const isFinanceIncomeCountries = ref(false);
const isFinanceIncomeSales = ref(false);
const isFinanceIncomeProducts = ref(false);


const goToPlatformCSV = () => {
  const params = {
    slug: 'earning_from_platforms',
    request_type: 'download',
    start_date: moment(
        props.choosenDates ? props.choosenDates[0] : moment().subtract(1, 'year')
    ).format("YYYY-MM-DD"),
    end_date: moment(
        props.choosenDates ? props.choosenDates[1] : moment()
    ).format("YYYY-MM-DD"),
  };

  // Parametreleri sorgu dizgesi (query string) olarak oluştur
  const queryString = new URLSearchParams(params).toString();
  const url = `${route('control.finance.analysis.show')}?${queryString}`;

  // Fetch API ile indirme işlemini başlat
  fetch(url, {
    method: 'GET',
    headers: {
      'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    },
  })
      .then((response) => response.blob())
      .then((blob) => {
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', `${params.slug}`); // İndirilecek dosyanın adı
        document.body.appendChild(link);
        link.click();
        link.remove();
      })
      .catch((error) => {
        console.error('Excel dosyası indirilirken hata oluştu:', error);
      });
};

const goToCountriesCSV = () => {
  const params = {
    slug: 'earning_from_countries',
    request_type: 'download',
    start_date: moment(
        props.choosenDates ? props.choosenDates[0] : moment().subtract(1, 'year')
    ).format("YYYY-MM-DD"),
    end_date: moment(
        props.choosenDates ? props.choosenDates[1] : moment()
    ).format("YYYY-MM-DD"),
  };

  // Parametreleri sorgu dizgesi (query string) olarak oluştur
  const queryString = new URLSearchParams(params).toString();
  const url = `${route('control.finance.analysis.show')}?${queryString}`;

  // Fetch API ile indirme işlemini başlat
  fetch(url, {
    method: 'GET',
    headers: {
      'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    },
  })
      .then((response) => response.blob())
      .then((blob) => {
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', `${params.slug}`); // İndirilecek dosyanın adı
        document.body.appendChild(link);
        link.click();
        link.remove();
      })
      .catch((error) => {
        console.error('Excel dosyası indirilirken hata oluştu:', error);
      });
};

const goToSalesCSV = () => {
  const params = {
    slug: 'earning_from_sales_type',
    request_type: 'download',
    start_date: moment(
        props.choosenDates ? props.choosenDates[0] : moment().subtract(1, 'year')
    ).format("YYYY-MM-DD"),
    end_date: moment(
        props.choosenDates ? props.choosenDates[1] : moment()
    ).format("YYYY-MM-DD"),
  };

  // Parametreleri sorgu dizgesi (query string) olarak oluştur
  const queryString = new URLSearchParams(params).toString();
  const url = `${route('control.finance.analysis.show')}?${queryString}`;

  // Fetch API ile indirme işlemini başlat
  fetch(url, {
    method: 'GET',
    headers: {
      'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    },
  })
      .then((response) => response.blob())
      .then((blob) => {
        const downloadUrl = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', `${params.slug}`); // İndirilecek dosyanın adı
        document.body.appendChild(link);
        link.click();
        link.remove();
      })
      .catch((error) => {
        console.error('Excel dosyası indirilirken hata oluştu:', error);
      });
};


const options = ref({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: Object.keys(props.data.earning_from_platforms),
  colors: ['#5BCF82', '#F9C74F', '#F94144', '#577590'], // Dilim renkleri
  legend: {
    show: true, // Göstergeyi etkinleştir
    position: 'right', // Gösterge pozisyonu
  },
  dataLabels: {
    enabled: true, // Dilim üzerindeki değerleri göster
    style: {
      fontSize: '13px',
      colors: ['#fff'], // Yazı rengi
    },
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff'], // Dilim kenarlık rengi
  },
  tooltip: {
    enabled: true, // Tooltip etkinleştir
    y: {
      formatter: function (val) {
        return `${val}`; // Tooltip içeriği
      },
    },
  },

});

const series = ref(Object.values(props.data.earning_from_platforms).map((e) => e.earning)); // Donut dilim verileri


const optionsCountries = ref({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: Object.keys(props.data.earning_from_countries),
  colors: ['#5BCF82', '#F9C74F', '#F94144', '#577590'], // Dilim renkleri
  legend: {
    show: true, // Göstergeyi etkinleştir
    position: 'right', // Gösterge pozisyonu
  },
  dataLabels: {
    enabled: true, // Dilim üzerindeki değerleri göster
    style: {
      fontSize: '13px',
      colors: ['#fff'], // Yazı rengi
    },
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff'], // Dilim kenarlık rengi
  },
  tooltip: {
    enabled: true, // Tooltip etkinleştir
    y: {
      formatter: function (val) {
        return `${val}`; // Tooltip içeriği
      },
    },
  },

});

const seriesCountries = ref(Object.values(props.data.earning_from_countries).map((e) => e.earning)); // Donut dilim verileri
const optionsSales = ref({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: Object.keys(props.data.earning_from_sales_type),
  colors: ['#5BCF82', '#F9C74F', '#F94144', '#577590'], // Dilim renkleri
  legend: {
    show: true, // Göstergeyi etkinleştir
    position: 'right', // Gösterge pozisyonu
  },
  dataLabels: {
    enabled: true, // Dilim üzerindeki değerleri göster
    style: {
      fontSize: '13px',
      colors: ['#fff'], // Yazı rengi
    },
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff'], // Dilim kenarlık rengi
  },
  tooltip: {
    enabled: true, // Tooltip etkinleştir
    y: {
      formatter: function (val) {
        return `${val}`; // Tooltip içeriği
      },
    },
  },

});

const seriesSales = ref(Object.values(props.data.earning_from_sales_type).map((e) => e.earning)); // Donut dilim verileri

</script>

<template>


  <div class="flex flex-col gap-6">
    <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
      <div class="flex items-center">
        <div class="flex items-center gap-2 flex-1">
          <FileChartLineIcon color="var(--sub-600)"></FileChartLineIcon>
          <p class="label-medium c-strong-950">Aylık Net Gelir</p>
          <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="paragraph-xs c-strong-950">Spotify</span>
          <span class="paragraph-xs c-strong-950">Apple Music</span>
          <span class="paragraph-xs c-strong-950">Youtube</span>
          <span class="paragraph-xs c-strong-950">Diğer</span>
        </div>
      </div>
      <hr>
      <div class="flex justify-between">

        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <SpotifyIcon/>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">SPOTIFY</span>
            <span class="label-medium c-strong-950">{{ data.monthly_net_earnings?.total?.Spotify?.earning }}</span>
          </div>
        </div>
        <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">

            <AppleMusicIcon/>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">APPLE MUSIC</span>
            <span class="label-medium c-strong-950">{{ data.monthly_net_earnings?.total?.Amazon?.earning }}</span>
          </div>

        </div>
        <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">

            <YoutubeIcon/>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">YOUTUBE</span>
            <span class="label-medium c-strong-950">{{ data.monthly_net_earnings?.total?.Youtube?.earning }}</span>
          </div>

        </div>
        <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
        <div class="flex gap-3">
          <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
            <span class="bg-[#717784] w-4 h-4 rounded-full"></span>
          </div>
          <div class="flex flex-col items-start gap-0.5">
            <span class="subheading-2xs c-soft-400">DİĞER</span>
            <span class="label-medium c-strong-950">{{ data.monthly_net_earnings?.total?.other?.earning }}</span>
          </div>

        </div>
      </div>
      <hr>
      <div class="flex gap-3">
        <div class="flex flex-col gap-5">
          <span class="paragraph-xs c-sub-600">20K</span>
          <span class="paragraph-xs c-sub-600">15K</span>
          <span class="paragraph-xs c-sub-600">10K</span>
          <span class="paragraph-xs c-sub-600">0</span>
        </div>
        <div class="flex gap-4 flex-1">
          <div v-for="key in Object.keys(data.monthly_net_earnings.items)"
               class="h-80 flex-1 flex flex-col items-center justify-between">

            <tippy :allowHtml="true" :interactiveBorder="30" theme="light" followCursor :sticky="true"
                   :interactive="false">
              <div class="h-72 flex flex-col justify-end w-full gap-0.5 w-full">
                <div class="bg-weak-50  flex items-end justify-center h-10 min-w-10">
                    <span class="c-sub-600 label-sm !text-[10px] ">
                        {{ Object.values(data.monthly_net_earnings.total)
                        .map(item => item.earning_num) // Extract the earning_num values
                        .reduce((sum, value) => sum + value, 0) // Sum up the values
                        .toFixed(2)}}
                    </span>
                </div>
                <div v-for="p in Object.keys(data.monthly_net_earnings.items[key])"
                    class="w-full"
                     :style="{height:data.monthly_net_earnings.items[key][p].percentage+'%'}"
                     :class="'bg-'+p.toLowerCase()"></div>
                <!-- <div class="bg-spotify h-[10%]"></div>
                <div class="bg-apple-music h-[25%]"></div>
                <div class="bg-other-platforms h-[5%]"></div> -->
              </div>
              <template #content>
                <div class="flex flex-col gap-2 w-64 p-1">
                  <p class="label-sm c-strong-950">{{key}}</p>
                  <div v-for="platform in Object.keys(data.monthly_net_earnings.items[key])"
                       class="flex items-center gap-2">
                    <SpotifyIcon v-if="platform=='Spotify'"/>
                    <YoutubeIcon v-else-if="platform == 'Youtube'"/>
                    <AppleMusicIcon v-else-if="platform == 'Apple'"/>

                    <span v-else-if="platform == 'other'" class="bg-[#717784] w-4 h-4 rounded-full"></span>

                    <p class="paragraph-sm c-strong-950 flex-1">{{ platform }}</p>
                    <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">
                      {{ data.monthly_net_earnings.items[key][platform].earning }}</p></div>
                  </div>
                </div>
              </template>
            </tippy>
            <span class="paragraph-xs c-sub-600 !text-center">{{ key }}</span>
          </div>
        </div>
      </div>
    </div>


    <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
      <div class="flex items-center">
        <div class="flex items-center gap-2 flex-1">
          <SpotifyIcon color="var(--sub-600)"></SpotifyIcon>
          <p class="label-medium c-strong-950">Katalog Optimizasyon ile Spotify Discovery Mode</p>
          <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
        </div>

      </div>
      <hr>
      <div class="flex gap-4 items-center">

        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-spotify"></div>
          <span class="paragraph-xs c-strong-950">Tahmini kazanılan gelir MD Spotify Keşif Modunda katalog optimizasyonu ile</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-[#BDECCD]"></div>
          <span class="paragraph-xs c-strong-950">Regular Spotify Revenue</span>
        </div>
      </div>
      <hr>
      <div class="flex gap-3">
        <div class="flex flex-col gap-5">
          <span class="paragraph-xs c-sub-600">20K</span>
          <span class="paragraph-xs c-sub-600">15K</span>
          <span class="paragraph-xs c-sub-600">10K</span>
          <span class="paragraph-xs c-sub-600">0</span>
        </div>
        <div class="flex gap-4 flex-1">


          <div v-for="key in Object.keys(data.spotify_discovery_mode_earnings.items)"
               class="h-80 flex-1 flex flex-col items-center justify-between">

            <tippy :allowHtml="true" :maxWidth="600" theme="light" followCursor :sticky="true" :interactive="false">
              <div class="h-72 flex flex-col justify-end w-full gap-0.5">
                <div class="bg-weak-50 h-[40%] flex items-end justify-center">
                  <span class="c-sub-600 label-sm !text-[10px] ">$96,000.00</span>
                </div>
                <div class="bg-spotify h-[40%]"></div>
                <div class="bg-[#BDECCD] h-[20%]"></div>
              </div>
              <template #content>
                <div class="flex flex-col gap-2 w-96 p-1">
                  <div class="flex items-center">

                    <p class="label-sm c-strong-950 flex-1">{{ key }}</p>
                    <p class="label-sm c-strong-950 ">{{ data.spotify_discovery_mode_earnings.items[key].total }}</p>

                  </div>
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-spotify"></div>
                    <p class="paragraph-sm c-strong-950 flex-1">Tahmini kazanılan gelir MD Spotify Keşif Modunda katalog
                      optimizasyonu ile</p>
                    <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">
                      {{ data.spotify_discovery_mode_earnings.items[key].promotion }}</p></div>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-[#BDECCD]"></div>
                    <p class="paragraph-sm c-strong-950 flex-1">Regular Spotify Revenue</p>
                    <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">
                      {{ data.spotify_discovery_mode_earnings.items[key].earning }}</p></div>
                  </div>
                </div>
              </template>
            </tippy>

            <span class="paragraph-xs c-sub-600">{{ key }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-6">
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <BookReadLineIcon color="var(--sub-600)"/>
            <p class="label-medium c-strong-950">Mağazaya Göre Gelir</p>
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
        <Vue3Apexcharts height="250" :options="options" :series="series"></Vue3Apexcharts>

      </div>
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <WorldIcon color="var(--sub-600)"/>
            <p class="label-medium c-strong-950">Ülkelere Göre Gelir</p>
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
        <Vue3Apexcharts height="250" :options="optionsCountries" :series="seriesCountries"></Vue3Apexcharts>

      </div>
    </div>

    <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
      <div class="flex items-center">
        <div class="flex items-center gap-2 flex-1">
          <YoutubeIcon/>
          <p class="label-medium c-strong-950 ms-2">Youtube toplam gelirinde öne çıkan noktalar</p>
          <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
        </div>
        <span class="label-sm c-strong-950">$23.758,00</span>

      </div>
      <hr>
      <div class="flex gap-4 items-center">
        <span class="paragraph-xs c-strong-950">Prim/Fremium gelirini göster</span>
        <AppSwitchComponent v-model="showYoutubeFremium"/>
      </div>
      <hr>
      <div class="flex flex-col gap-3" v-if="!showYoutubeFremium">
        <div v-for="key in Object.keys(data.earning_from_youtube)">
          <span class="label-xs">{{ key }}</span>

          <tippy :allowHtml="true" :maxWidth="600" theme="light" followCursor :sticky="true" :interactive="false">
            <AppProgressIndicator :height="12" :modelValue="data.earning_from_youtube[key].percentage" color="#D02533"/>
            <template #content>
              <div class="flex flex-col gap-2 w-96 p-1">
                <div class="flex items-center">
                  <div>
                    <YoutubeIcon/>
                  </div>
                  <p class="label-sm c-strong-950 flex-1 ms-2">Ocak 2024</p>
                  <p class="label-sm c-strong-950 ">{{ data.earning_from_youtube[key].earning }}</p>

                </div>
                <div class="flex items-start gap-2 bg-[#F2F5F8] py-2 px-4 mt-2 rounded-lg">
                  <InfoFilledIcon width="24" class="mt-1" color="#717784"/>
                  <span class="c-strong-950 c-sub-600">Reklam destekli videolar, Youtube müzikleri ve Youtube Premium ücretli abonelik gelirleri </span>
                </div>

              </div>
            </template>
          </tippy>

        </div>

      </div>

      <div v-else>
        <div class="flex items-center gap-3">
          <div class="flex gap-2 items-center c-strong-950 paragraph-xs">
            <div class="w-2 h-2 rounded-full bg-youtube-premium"></div>
            Premium
          </div>
          <div class="flex gap-2 items-center c-strong-950 paragraph-xs">
            <div class="w-2 h-2 rounded-full bg-youtube"></div>
            Freemium
          </div>
          <div class="flex gap-2 items-center c-strong-950 paragraph-xs">
            <div class="w-2 h-2 rounded-full bg-other"></div>
            Diğer
          </div>
        </div>


        <tippy v-for="i in 3" :allowHtml="true" :maxWidth="600" theme="light" followCursor :sticky="true"
               :interactive="false">
          <div class="mt-3">
            <p class="label-sm c-strong-950 mb-1">Youtube Official Content</p>
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
                <p class="label-sm c-strong-950 flex-1 ms-2">Youtube Official Content</p>
                <p class="label-sm c-strong-950 ">$500</p>

              </div>
              <div class="flex flex-col items-start gap-3">
                <div class="flex gap-2 items-center w-full">
                  <div class="w-2 h-2 rounded-full bg-youtube-premium"></div>
                  <div class="flex-1"><p class=" paragraph-xs c-strong-950">Premium</p></div>
                  <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">$500,57</p>
                  </div>

                </div>
                <div class="flex gap-2 items-center w-full">
                  <div class="w-2 h-2 rounded-full bg-youtube"></div>
                  <div class="flex-1"><p class="flex-1 paragraph-xs c-strong-950">Freemium</p></div>
                  <div class="border border-soft-200 rounded px-2 py-1"><p class="paragraph-xs c-sub-600">$500,57</p>
                  </div>

                </div>
                <div class="flex gap-2 items-center w-full">
                  <div class="w-2 h-2 rounded-full bg-other"></div>
                  <div class="flex-1"><p class="flex-1 paragraph-xs c-strong-950">Diğer</p></div>
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
            <p class="label-medium c-strong-950">Satış Tipi Gelir</p>
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

        <Vue3Apexcharts height="250" :options="optionsSales" :series="seriesSales"></Vue3Apexcharts>


      </div>
      <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
        <div class="flex items-center">
          <div class="flex items-center gap-2 flex-1">
            <WorldIcon color="var(--sub-600)"/>
            <p class="label-medium c-strong-950">Trendler-Albüm</p>
            <p class="c-soft-400 label-sm">{{ formattedDate }}</p>
          </div>
          <div class="flex gap-3">
            <button @click="isFinanceIncomeProducts = true">
              <EyeOnIcon color="var(--sub-600)"/>
            </button>
            <button>
              <DownloadIcon color="var(--sub-600)"/>
            </button>
          </div>

        </div>
        <hr>
        <table>
          <tbody>
          <tr v-for="album in data.trending_albums" class="">
            <td class="paragraph-xs c-sub-600 py-1.5">{{ album.product_name }}</td>
            <td class="paragraph-xs text-[#377C4E]">{{ album.earning }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <FinanceIncomePlatforms :choosenDates="choosenDates" v-model="isFinanceIncomePlatforms"
                          v-if="isFinanceIncomePlatforms"></FinanceIncomePlatforms>
  <FinanceIncomeCountries :choosenDates="choosenDates" v-model="isFinanceIncomeCountries"
                          v-if="isFinanceIncomeCountries"></FinanceIncomeCountries>
  <FinanceIncomeSales :choosenDates="choosenDates" v-model="isFinanceIncomeSales"
                      v-if="isFinanceIncomeSales"></FinanceIncomeSales>
  <FinanceIncomeProducts :choosenDates="choosenDates" v-model="isFinanceIncomeProducts"
                         v-if="isFinanceIncomeProducts"></FinanceIncomeProducts>
</template>

<style scoped>

</style>
