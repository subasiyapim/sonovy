<template>
  <Transition name="modal-fade">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black bg-opacity-50" @click="close"></div>
      <div class="relative max-w-2xl w-full bg-white rounded-lg shadow-xl max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
          <h2 class="text-lg font-semibold">Ses Kalite Analizi</h2>
          <button @click="close" class="p-2 text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-4 overflow-y-auto max-h-[70vh]">
          <!-- Loading State -->
          <div v-if="loading" class="flex flex-col items-center justify-center py-8">
            <div class="w-12 h-12 border-t-2 border-b-2 border-dark-green-500 rounded-full animate-spin"></div>
            <p class="mt-4 text-sm text-gray-500">Ses dosyası analiz ediliyor...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="p-4 bg-red-50 text-red-700 rounded-md">
            <h3 class="font-medium">Hata oluştu</h3>
            <p class="text-sm mt-1">{{ error }}</p>
            <button
              @click="runAnalysis"
              class="mt-4 px-4 py-2 bg-red-100 text-red-800 rounded-md hover:bg-red-200"
            >
              Tekrar Dene
            </button>
          </div>

          <!-- Content State -->
          <div v-else-if="analysisData">
            <!-- Song Info -->
            <div class="mb-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center p-3 bg-dark-green-800">
                  <img src="@/assets/images/mp3_active.png" alt="Audio icon">
                </div>
                <div>
                  <h3 class="font-medium">{{ song.name }} {{ song.version ? `(${song.version})` : '' }}</h3>
                  <p class="text-sm text-gray-500">
                    {{ formatFileSize(song.size) }} • {{ song.duration || '0:00' }}
                  </p>
                </div>
              </div>

              <!-- Analysis Date -->
              <div class="text-sm text-gray-500 mb-4">
                <span>Analiz tarihi: {{ formatDate(analysisData.analyzed_at) }}</span>
              </div>

              <!-- Quality Badge -->
              <div
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  analysisData.is_valid
                    ? 'bg-green-100 text-green-800'
                    : 'bg-yellow-100 text-yellow-800'
                ]"
              >
                <div class="flex items-center gap-1">
                  <CheckIcon v-if="analysisData.is_valid" size="14" class="text-green-600" />
                  <WarningIcon v-else size="14" class="text-yellow-600" />
                  {{ analysisData.is_valid ? 'Kalite kontrolünü geçti' : 'Kalite sorunları var' }}
                </div>
              </div>
            </div>

            <!-- Specs -->
            <div class="mb-6">
              <h3 class="font-medium mb-2">Ses Özellikleri</h3>
              <div class="grid grid-cols-2 gap-4">
                <div v-if="analysisData.specs?.channels" class="bg-gray-50 p-3 rounded-md">
                  <p class="text-xs text-gray-500">Kanallar</p>
                  <p class="font-medium">{{ analysisData.specs.channels }} Kanal {{ analysisData.specs.channels === 2 ? '(Stereo)' : '(Mono)' }}</p>
                </div>
                <div v-if="analysisData.specs?.sample_rate" class="bg-gray-50 p-3 rounded-md">
                  <p class="text-xs text-gray-500">Sample Rate</p>
                  <p class="font-medium">{{ formatSampleRate(analysisData.specs.sample_rate) }}</p>
                </div>
                <div v-if="analysisData.specs?.bit_rate" class="bg-gray-50 p-3 rounded-md">
                  <p class="text-xs text-gray-500">Bit Rate</p>
                  <p class="font-medium">{{ formatBitRate(analysisData.specs.bit_rate) }}</p>
                </div>
                <div v-if="analysisData.specs?.codec_name" class="bg-gray-50 p-3 rounded-md">
                  <p class="text-xs text-gray-500">Codec</p>
                  <p class="font-medium">{{ analysisData.specs.codec_name.toUpperCase() }}</p>
                </div>
              </div>
            </div>

            <!-- Issues -->
            <div v-if="analysisData.issues && analysisData.issues.length > 0" class="mb-6">
              <h3 class="font-medium mb-2">Tespit Edilen Sorunlar</h3>
              <ul class="bg-yellow-50 p-3 rounded-md">
                <li
                  v-for="(issue, index) in analysisData.issues"
                  :key="index"
                  class="flex items-start mb-2 last:mb-0"
                >
                  <div class="mr-2 mt-0.5 text-yellow-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <span class="text-sm">{{ issue }}</span>
                </li>
              </ul>
            </div>

            <!-- Audio Levels -->
            <div v-if="analysisData.level_analysis" class="mb-6">
              <h3 class="font-medium mb-2">Ses Seviyeleri</h3>
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded-md">
                  <p class="text-xs text-gray-500">Ortalama Ses Seviyesi</p>
                  <p class="font-medium">{{ analysisData.level_analysis.mean_volume }} dB</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-md">
                  <p class="text-xs text-gray-500">Maximum Ses Seviyesi</p>
                  <p
                    :class="[
                      'font-medium',
                      parseFloat(analysisData.level_analysis.max_volume) > -1 ? 'text-red-600' : ''
                    ]"
                  >
                    {{ analysisData.level_analysis.max_volume }} dB
                    <span v-if="parseFloat(analysisData.level_analysis.max_volume) > -1" class="text-xs ml-1">(Clipping risk)</span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Silence Analysis -->
            <div v-if="analysisData.silence_analysis && analysisData.silence_analysis.silence_periods?.length > 0" class="mb-6">
              <h3 class="font-medium mb-2">Sessizlik Analizi</h3>
              <div v-if="analysisData.silence_analysis.has_long_silences" class="mb-3 bg-yellow-50 p-3 rounded-md text-sm">
                Dosyada uzun sessizlik periyotları tespit edildi.
              </div>

              <div class="bg-gray-50 p-3 rounded-md overflow-x-auto">
                <table class="min-w-full text-sm">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                      <th class="px-3 py-2 text-left">#</th>
                      <th class="px-3 py-2 text-left">Başlangıç (s)</th>
                      <th class="px-3 py-2 text-left">Bitiş (s)</th>
                      <th class="px-3 py-2 text-left">Süre (s)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(period, index) in analysisData.silence_analysis.silence_periods"
                      :key="index"
                      :class="period.duration > 2 ? 'text-yellow-700' : ''"
                    >
                      <td class="px-3 py-2">{{ index + 1 }}</td>
                      <td class="px-3 py-2">{{ period.start.toFixed(2) }}</td>
                      <td class="px-3 py-2">{{ period.end.toFixed(2) }}</td>
                      <td class="px-3 py-2 font-medium">
                        {{ period.duration.toFixed(2) }}
                        <span v-if="period.duration > 2" class="text-xs">*</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <p v-if="analysisData.silence_analysis.has_long_silences" class="text-xs mt-2 text-gray-500">
                * 2 saniyeden uzun sessizlik periyotları
              </p>
            </div>
          </div>

          <!-- Empty State (No Analysis Yet) -->
          <div v-else class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-4 text-gray-700 font-medium">Ses kalite analizi yapılmamış</h3>
            <p class="mt-2 text-sm text-gray-500">Bu şarkı için kalite analizi henüz yapılmamış. Şimdi analiz etmek ister misiniz?</p>
            <button
              @click="runAnalysis"
              class="mt-4 px-4 py-2 bg-dark-green-600 text-white rounded-md hover:bg-dark-green-700"
              :disabled="loading"
            >
              Analiz Et
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t flex justify-end">
          <button
            @click="close"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"
          >
            Kapat
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { useCrudStore } from '@/Stores/useCrudStore';
import { toast } from 'vue3-toastify';
import { CheckIcon, WarningIcon } from '@/Components/Icons';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  song: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['update:modelValue']);
const crudStore = useCrudStore();
const loading = ref(false);
const error = ref(null);
const analysisData = ref(null);

// Şarkıda kalite analiz verisi var mı kontrol et
const checkAnalysisData = () => {
  if (!props.song) return;

  // details objesi var mı ve içinde quality_analysis var mı kontrol et
  if (props.song.details && props.song.details.quality_analysis) {
    analysisData.value = props.song.details.quality_analysis;
  } else {
    analysisData.value = null;
  }
};

// Kapatma işlevi
const close = () => {
  emit('update:modelValue', false);
};

// Song değiştiğinde analiz verilerini kontrol et
watch(() => props.song, (newSong) => {
  if (newSong && props.modelValue) {
    checkAnalysisData();
  }
}, { immediate: true });

// Modal açıldığında analiz verilerini kontrol et
watch(() => props.modelValue, (isOpen) => {
  if (isOpen && props.song) {
    checkAnalysisData();
  }
}, { immediate: true });

// Analiz işlemini başlat
const runAnalysis = async () => {
  if (!props.song || !props.song.id) {
    error.value = 'Geçerli bir şarkı bulunamadı';
    return;
  }

  loading.value = true;
  error.value = null;

  try {
    const response = await crudStore.post(route('control.catalog.songs.quality-analysis'), {
      song_id: props.song.id
    });

    if (response && response.data) {
      analysisData.value = response.data;
      toast.success('Ses kalite analizi tamamlandı');
    } else {
      error.value = 'Analiz sonuçları alınamadı';
    }
  } catch (err) {
    console.error('Quality analysis error:', err);
    error.value = err.response?.data?.message || 'Analiz sırasında bir hata oluştu';
    toast.error(error.value);
  } finally {
    loading.value = false;
  }
};

// Yardımcı formatlama fonksiyonları
const formatFileSize = (bytes) => {
  if (!bytes) return '0 B';
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${sizes[i]}`;
};

const formatDate = (isoString) => {
  if (!isoString) return '-';
  const date = new Date(isoString);
  return date.toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatSampleRate = (sampleRate) => {
  if (!sampleRate) return '-';
  return `${sampleRate / 1000} kHz`;
};

const formatBitRate = (bitRate) => {
  if (!bitRate) return '-';
  return `${Math.round(bitRate / 1000)} kbps`;
};
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
