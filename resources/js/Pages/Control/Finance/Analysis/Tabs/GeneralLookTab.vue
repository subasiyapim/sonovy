

<script setup>
import {ref} from 'vue';
import {FileChartLineIcon,SpotifyIcon,YoutubeIcon,AppleMusicIcon,WorldIcon,EyeOnIcon,DownloadIcon,BookReadLineIcon} from '@/Components/Icons';
import {AppProgressIndicator} from '@/Components/Widgets';
import Vue3Apexcharts from 'vue3-apexcharts'


const options = ref({
  chart: {
    type: 'donut',
    height: 200,
  },
  labels: ['Kategori 1', 'Kategori 2', 'Kategori 3', 'Kategori 4'], // Donut dilim etiketleri
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
//   plotOptions: {
//     pie: {
//       donut: {
//         size: '70%', // Donut iç çap boyutu
//         labels: {
//           show: true,
//           name: {
//             show: true,
//             fontSize: '16px',
//             fontWeight: 600,
//           },
//           value: {
//             show: true,
//             fontSize: '14px',
//             fontWeight: 400,
//             formatter: (val) => `${val}`, // Değeri formatla
//           },
//           total: {
//             show: true,
//             label: 'Toplam',
//             fontSize: '16px',
//             fontWeight: 600,
//             formatter: function (w) {
//               return w.globals.seriesTotals.reduce((a, b) => a + b, 0); // Toplam değer
//             },
//           },
//         },
//       },
//     },
//   },
});

const series = ref([44, 55, 13, 33]); // Donut dilim verileri

</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <FileChartLineIcon color="var(--sub-600)"> </FileChartLineIcon>
                    <p class="label-medium c-strong-950">Aylık Net Gelir</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
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
                        <SpotifyIcon />
                    </div>
                    <div class="flex flex-col items-start gap-0.5">
                        <span class="subheading-2xs c-soft-400">SPOTIFY</span>
                        <span class="label-medium c-strong-950">$96.000,00</span>
                    </div>
                </div>
                <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
                <div class="flex gap-3">
                    <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">

                        <AppleMusicIcon />
                    </div>
                    <div class="flex flex-col items-start gap-0.5">
                        <span class="subheading-2xs c-soft-400">APPLE MUSIC</span>
                        <span class="label-medium c-strong-950">$24.000,00</span>
                    </div>

                </div>
                <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
                <div class="flex gap-3">
                    <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">

                        <YoutubeIcon />
                    </div>
                    <div class="flex flex-col items-start gap-0.5">
                        <span class="subheading-2xs c-soft-400">YOUTUBE</span>
                        <span class="label-medium c-strong-950">$24.000,00</span>
                    </div>

                </div>
                <span class="w-[1px] bg-[#E1E4EA] h-10"></span>
                <div class="flex gap-3">
                    <div class="w-10 h-10 border border-soft-200 rounded-full flex items-center justify-center">
                        <span class="bg-[#717784] w-4 h-4 rounded-full"></span>
                    </div>
                    <div class="flex flex-col items-start gap-0.5">
                        <span class="subheading-2xs c-soft-400">DİĞER</span>
                        <span class="label-medium c-strong-950">$14.000,00</span>
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
                    <div v-for="i in 12" class="h-80 flex-1 flex flex-col items-center justify-between">
                        <div class="h-72 flex flex-col justify-end w-full gap-0.5">
                            <div class="bg-weak-50 h-[40%] flex items-end justify-center">
                                <span class="c-sub-600 label-sm !text-[10px] ">$96,000.00</span>
                            </div>
                            <div class="bg-youtube h-[20%]"></div>
                            <div class="bg-spotify h-[10%]"></div>
                            <div class="bg-apple-music h-[25%]"></div>
                            <div class="bg-other-platforms h-[5%]"></div>
                        </div>
                        <span class="paragraph-xs c-sub-600">Ocak</span>
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <SpotifyIcon color="var(--sub-600)"> </SpotifyIcon>
                    <p class="label-medium c-strong-950">Katalog Optimizasyon ile Spotify Discovery Mode</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
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
                    <div v-for="i in 12" class="h-80 flex-1 flex flex-col items-center justify-between">
                        <div class="h-72 flex flex-col justify-end w-full gap-0.5">
                            <div class="bg-weak-50 h-[40%] flex items-end justify-center">
                                <span class="c-sub-600 label-sm !text-[10px] ">$96,000.00</span>
                            </div>
                            <div class="bg-spotify h-[40%]"></div>
                            <div class="bg-[#BDECCD] h-[20%]"></div>
                        </div>
                        <span class="paragraph-xs c-sub-600">Ocak</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-6">
            <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
                <div class="flex items-center">
                    <div class="flex items-center gap-2 flex-1">
                        <BookReadLineIcon color="var(--sub-600)" />
                        <p class="label-medium c-strong-950">Mağazaya Göre Gelir</p>
                        <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                    </div>
                    <div class="flex gap-3">
                        <button><EyeOnIcon color="var(--sub-600)" /></button>
                        <button><DownloadIcon color="var(--sub-600)" /></button>
                    </div>

                </div>
                <hr>
            <Vue3Apexcharts height="250" :options="options" :series="series"></Vue3Apexcharts>

            </div>
            <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
                <div class="flex items-center">
                    <div class="flex items-center gap-2 flex-1">
                        <WorldIcon color="var(--sub-600)" />
                        <p class="label-medium c-strong-950">Ülkelere Göre Gelir</p>
                        <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                    </div>
                    <div class="flex gap-3">
                        <button><EyeOnIcon color="var(--sub-600)" /></button>
                        <button><DownloadIcon color="var(--sub-600)" /></button>
                    </div>

                </div>
                <hr>
            <Vue3Apexcharts height="250" :options="options" :series="series"></Vue3Apexcharts>

            </div>
        </div>

        <div class="bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
            <div class="flex items-center">
                <div class="flex items-center gap-2 flex-1">
                    <YoutubeIcon />
                    <p class="label-medium c-strong-950">Youtube toplam gelirinde öne çıkan noktalar</p>
                    <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                </div>
                <span class="label-sm c-strong-950">$23.758,00</span>

            </div>
            <hr>
            <div class="flex gap-4 items-center">
                <span class="paragraph-xs c-strong-950">Prim/Fremium gelirini göster</span>
            </div>
            <hr>
            <div class="flex flex-col gap-3">
                <div>
                    <span class="label-xs">Youtube Official Content</span>
                    <AppProgressIndicator :height="8" :modelValue="12" color="#D02533" />
                </div>
                <div>
                    <span class="label-xs">Youtube UGC</span>
                    <AppProgressIndicator :height="8" :modelValue="2" color="#D02533" />
                </div>
                <div>
                    <span class="label-xs">Youtube Shorts</span>
                    <AppProgressIndicator :height="8" :modelValue="45" color="#D02533" />
                </div>
                <div>
                    <span class="label-xs">Youtube Audio Tier</span>
                    <AppProgressIndicator :height="8" :modelValue="24" color="#D02533" />
                </div>
                <div>
                    <span class="label-xs">MD Right Services(Youtube)</span>
                    <AppProgressIndicator :height="8" :modelValue="5" color="#D02533" />
                </div>
            </div>
        </div>

        <div class="flex gap-6">
            <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
                <div class="flex items-center">
                    <div class="flex items-center gap-2 flex-1">
                        <BookReadLineIcon color="var(--sub-600)" />
                        <p class="label-medium c-strong-950">Satış Tipi Gelir</p>
                        <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                    </div>
                    <div class="flex gap-3">
                        <button><EyeOnIcon color="var(--sub-600)" /></button>
                        <button><DownloadIcon color="var(--sub-600)" /></button>
                    </div>

                </div>
                <hr>

            <Vue3Apexcharts height="250" :options="options" :series="series"></Vue3Apexcharts>


            </div>
            <div class="flex-1 bg-white rounded-xl border border-soft-200 p-4 flex flex-col gap-4">
                <div class="flex items-center">
                    <div class="flex items-center gap-2 flex-1">
                        <WorldIcon color="var(--sub-600)" />
                        <p class="label-medium c-strong-950">Trendler-Albüm</p>
                        <p class="c-soft-400 label-sm">Ocak 2024 - Haziran 2024</p>
                    </div>
                    <div class="flex gap-3">
                        <button><EyeOnIcon color="var(--sub-600)" /></button>
                        <button><DownloadIcon color="var(--sub-600)" /></button>
                    </div>

                </div>
                <hr>
                <table>
                    <tbody>
                        <tr v-for="i in 6" class="">
                            <td class="paragraph-xs c-sub-600 py-1.5">Albüm adı gelecek</td>
                            <td class="paragraph-xs text-[#377C4E]">+201.77%</td>
                            <td class="paragraph-xs c-sub-600">Albüm adı gelecek</td>
                            <td class="paragraph-xs text-[#D02533]">-201.77%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style  scoped>

</style>
