<template>

  <div class="w-full h-full">
    <!-- Drag-and-Drop Area -->

    <div
        @click="triggerFileInput"
        class="drop-area"
        @dragover.prevent="handleDragOver"
        @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop"
        :class="{ 'dragging': isDragging }"
    >
      <div class="flex flex-col items-center justify-center gap-5">
        <div class="w-16 h-16 rounded-full bg-white-600 flex items-center justify-center">
          <SongFileIcon color="var(--dark-green-800)"/>
        </div>
        <div class="">
          <p class="c-strong-950 label-medium !text-center">
            Yayın eklemek istediğiniz parçaları seçin veya sürükleyip bırakın.
          </p>
          <p class="paragraph-medium c-neutral-500 !text-center">
            Format: MP3, AAC, WAV, FLAC, MP4, AVI...
          </p>
        </div>
        <div class="flex items-center gap-3">
          <PrimaryButton>
            <template #icon>
              <AddIcon color="var(--dark-green-500)"/>
            </template>
            Parça Ekle
          </PrimaryButton>
          <RegularButton>
            Katalogdan Parça Seç
          </RegularButton>
        </div>
      </div>
      <input
          ref="fileInput"
          type="file"
          :accept="acceptTypes"
          multiple
          @change="onChangeInput"
          hidden
      />
    </div>

    <!-- Image Preview -->

  </div>
</template>

<script setup>
import {ref, reactive, computed, onMounted, onBeforeMount} from 'vue';
import {SongFileIcon, AddIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';
import Echo from 'laravel-echo';
import axios from 'axios';

// Yükleme tamamlandı mesajları
const createSuccessMessage = (fileName) => {
  const displayName = fileName ? (fileName.length > 30 ? fileName.substring(0, 27) + '...' : fileName) : 'Dosya';
  return `${displayName} başarıyla yüklendi!`;
};

const createProcessingMessage = (fileName) => {
  const displayName = fileName ? (fileName.length > 30 ? fileName.substring(0, 27) + '...' : fileName) : 'Dosya';
  return `${displayName} yüklendi, işleniyor...`;
};

// crypto.randomUUID polyfill - basit ve güvenli yaklaşım
const getRandomId = () => {
  // Basit ve her tarayıcıda çalışacak bir UUID benzeri string
  const timestamp = Date.now().toString(36);
  const randomStr = Math.random().toString(36).substring(2, 10);
  return timestamp + '-' + randomStr;
};

// Optimum chunk size (5MB) - büyük dosyalar için performans iyileştirmesi
const OPTIMAL_CHUNK_SIZE = 5 * 1024 * 1024;
// Yeniden deneme mekanizması
const MAX_RETRIES = 3;
// Yeniden deneme gecikmeleri (ms)
const RETRY_DELAYS = [0, 1000, 3000, 5000]; // 0ms, 1s, 3s, 5s

const props = defineProps({
  modelValue: {
    default: {}
  },
  pageData: {},
  errorObject: {},
  type: {
    default: 1
  },
  product_id: {},
  enableRealTimeStatus: {
    type: Boolean,
    default: true
  }
})

const emits = defineEmits(['start', 'progress', 'complete', 'error', 'drop', 'dragleave'])

const csrf_token = ref('');
const acceptedAudioExtensions = ['mp3', 'wav', 'aac', 'flac', 'ogg'];
const acceptedVideoExtensions = ['mp4', 'avi', 'mkv', 'mov', 'webm'];

// Kabul edilen dosya tiplerini birleştir
const acceptTypes = computed(() => {
  const allTypes = [...acceptedAudioExtensions, ...acceptedVideoExtensions];
  return '.' + allTypes.join(',.');
});

const crudStore = useCrudStore();
const fileInput = ref(null);
const isDragging = ref(false);
const images = reactive([]);
const uploading = ref(false);
const uploadTasks = ref([]); // Aktif yükleme görevlerini takip et

// Echo kanalına abone ol (realtime durum için)
onMounted(() => {
  // CSRF token'ı meta etiketinden al
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    csrf_token.value = token.content;
    // Axios için CSRF token ayarı
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    // Cookie gönderimi için izin
    axios.defaults.withCredentials = true;
  } else {
    console.error('CSRF token meta etiketi bulunamadı. Yükleme işlemi başarısız olabilir.');
  }

  if (props.enableRealTimeStatus && props.product_id) {
    try {
      // Echo konfigürasyonunu kontrol et - sadece debug için
      console.debug('Echo config check:', Echo && window.Echo ? 'Echo configured' : 'Echo not configured');

      // Kullanıcı oturumunu kontrol et
      console.debug('User auth check:', window.Laravel && window.Laravel.user ? 'User is logged in' : 'User might not be logged in');

      // Broadcasting kanalına abone olmayı dene - echo bağlantısı olmasa bile devam et
      try {
        // İlk olarak tenant id'yi almaya çalışalım (tenant id varsa gerekli olabilir)
        const tenantId = window.tenantId || document.querySelector('meta[name="tenant-id"]')?.content;

        // Kanal adını doğru şekilde oluştur
        // Farklı kanal formatlarını dene
        let channelName = `song.processing.${props.product_id}`;
        if (tenantId) {
          // Tenant bazlı kanal adı kullanmayı dene
          channelName = `tenant.${tenantId}.song.processing.${props.product_id}`;
        }

        console.debug('Trying to subscribe to channel:', channelName);

        window.Echo.private(channelName)
          .listen('SongProcessingStarted', (e) => {
            console.log('Processing started', e);
            // İşlem başladığında UI güncellemesi
            toast.info(`${e.fileName || 'Dosya'} işleniyor...`);
          })
          .listen('SongProcessingComplete', async (e) => {
            console.log('Processing complete', e);
            if (e.success) {
              // Başarılı işlem durumunda
              try {
                let response = await crudStore.post(route('control.find.songs'), {
                  id: e.result
                });

                response.percentage = 100;
                emits('complete', response);

                toast.success('Dosya başarıyla işlendi!');
              } catch (fetchError) {
                console.error('Song fetch error:', fetchError);
                toast('Dosya işlendi, ancak bilgileri alınamadı. Sayfayı yenileyebilirsiniz.',{
                    type:"warning",
                });
              }
            } else {
              // Hata durumunda
              emits('error', { message: e.message || 'İşlem sırasında bir hata oluştu' });
              toast.error('Dosya işlenirken bir hata oluştu: ' + (e.message || 'Bilinmeyen hata'));
            }
          })
          .error((error) => {
            console.error('Echo error:', error);
            // Yetkilendirme hatası - ama yüklemeyi etkilemesin
            console.warn('Gerçek zamanlı bildirimler alınamıyor, polling mekanizması kullanılacak.');
          });
      } catch (channelError) {
        console.warn('Echo channel subscription failed:', channelError);
        // Echo kanal aboneliği başarısız oldu, ama bu yüklemeyi durdurmayacak
      }
    } catch (error) {
      console.error('Echo bağlantısı kurulamadı', error);
      // Echo bağlantı hatası kullanıcı deneyimini etkilemeyecek
      // Bu durumda, kullanıcı WebSocket bildirimleri alamayabilir, ancak yükleme işlemi yine de çalışabilir
    }
  }
});

const handleDragOver = () => {
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
  emits('dragleave');
};

const handleDrop = (event) => {
  isDragging.value = false;
  emits('drop');

  const files = Array.from(event.dataTransfer.files);
  handleFiles(files);
};

const onChangeInput = (e) => {
  const allowedExtensions = [...acceptedAudioExtensions, ...acceptedVideoExtensions];
  const files = e.target.files;

  if (!files || files.length === 0) {
    return;
  }

  let isValid = true;
  const invalidFiles = [];

  // Tüm dosyaları kontrol et
  for (let file of files) {
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!allowedExtensions.includes(fileExtension)) {
      isValid = false;
      invalidFiles.push(file.name);
    }
  }

  if (!isValid) {
    toast.error(`Geçersiz dosya format(lar)ı: ${invalidFiles.join(', ')}`);
    if (fileInput.value) {
      fileInput.value.value = ''; // Input'u sıfırla
    }
    return;
  }

  // Dosyaları işle
  const f = Array.from(e.target.files);
  handleFiles(f);
}

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFiles = (files) => {
  // Dosya sayısı kontrolü - çok fazla dosya yüklenmesini engelle
  if (files.length > 10) {
    toast.warning('En fazla 10 dosya seçebilirsiniz.');
    return;
  }

  // Toplam dosya boyutu kontrolü
  const totalSize = files.reduce((acc, file) => acc + file.size, 0);
  const maxSize = 1024 * 1024 * 1024; // 1GB

  if (totalSize > maxSize) {
    toast.warning('Toplam dosya boyutu 1GB\'ı geçemez.');
    return;
  }

  files.forEach((file) => {
    // Dosyaları bir FileReader ile okuma - bu, dosya hakkında ön bilgi almak için
    const reader = new FileReader();
    reader.onload = (e) => {
      handleFileInput(file);
    };
    reader.readAsDataURL(file);
  });
};

const handleFileInput = (file) => {
  if (uploading.value) {
    toast.warning('Lütfen mevcut yüklemenin tamamlanmasını bekleyin.');
    return;
  }

  // Dosya türü kontrolü
  const fileExtension = file.name.split('.').pop().toLowerCase();
  const isAudio = acceptedAudioExtensions.includes(fileExtension);
  const isVideo = acceptedVideoExtensions.includes(fileExtension);

  if (props.type === 1 && !isAudio) {
    toast.error('Bu yayın tipi için sadece ses dosyaları yükleyebilirsiniz.');
    return;
  } else if (props.type === 2 && !isVideo) {
    toast.error('Bu yayın tipi için sadece video dosyaları yükleyebilirsiniz.');
    return;
  }

  // CSRF token kontrolü
  if (!csrf_token.value) {
    // Tekrar almayı dene
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
      csrf_token.value = token.content;
    } else {
      toast.error('CSRF token bulunamadı. Sayfayı yenileyip tekrar deneyin.');
      return;
    }
  }

  // Yükleme görevi oluştur
  const upload = createUploadTask(
    file,
    {},
    // Success callback
    (response) => {
      console.log('Upload task completed', response);
    },
    // Progress callback
    (percentage) => {
      console.log(`Upload progress: ${percentage}%`);
    },
    // Error callback
    (error) => {
      console.error('Upload task failed:', error);
    }
  );

  // Önceki yüklemeleri kontrol et
  upload.findPreviousUploads().then(function (previousUploads) {
    if (previousUploads.length) {
      upload.resumeFromPreviousUpload(previousUploads[0]);
    }

    // Yüklemeyi başlat
    upload.start();

    // Aktif görevlere ekle
    uploadTasks.value.push(upload);
  });
};

const createUploadTask = (file, metaData, onSuccess, onProgress, onError) => {
  // Yükleme görevi oluştur
  const extension = file.name.split('.').pop();
  // Benzersiz ID oluştur - crypto.randomUUID yerine kendi fonksiyonumuzu kullan
  const uniqueId = getRandomId();
  const tempFileName = `${uniqueId}_${Date.now()}.${extension}`;

  metaData = {
    filename: tempFileName,
    mime_type: file.type,
    originalName: file.name,
    type: props.type,
    size: file.size,
    percentage: 0,
    product_id: props.product_id,
    ...metaData
  };

  // Yükleme başladı event'ini tetikle
  emits('start', metaData);

  // CSRF token'ı son kez kontrol et
  const currentToken = csrf_token.value || document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Yükleme endpoint'ini oluştur
  const endpoint = route('control.tus', {
    type: props.type,
    name: file.name,
    temp: tempFileName,
  });

  console.debug('Upload endpoint:', endpoint);
  console.debug('CSRF token present:', !!currentToken);

  // Tus Upload nesnesi oluştur
  const upload = new tus.Upload(file, {
    headers: {
      'X-CSRF-TOKEN': currentToken,
      '_method': 'POST'
    },
    endpoint: endpoint,
    chunkSize: OPTIMAL_CHUNK_SIZE, // 5MB chunk size
    retryDelays: RETRY_DELAYS,
    metadata: metaData,
    uploadDataDuringCreation: true,
    withCredentials: true, // Cookie'lerin XHR istekleriyle gönderilmesini sağlar

    onStart: function (e) {
      console.log('Upload started', metaData);
      uploading.value = true;
      metaData.percentage = 0;
    },

    // Hata durumu callback'i
    onError: function (error) {
      console.error('Upload failed:', error);
      uploading.value = false;

      // CSRF token hatası kontrolü
      if (error.originalResponse && error.originalResponse.status === 419) {
        toast.error('Oturum süreniz dolmuş olabilir. Sayfayı yenileyip tekrar deneyin.');
        // Sayfayı yenilemek için öneri
        setTimeout(() => {
          if (confirm('Sayfayı yenilemek ister misiniz?')) {
            window.location.reload();
          }
        }, 2000);
      } else {
        if (typeof onError === 'function') {
          onError(error);
        }

        // Hata mesajını daha ayrıntılı hale getir
        let errorMessage = error.message || 'Bilinmeyen hata';
        if (error.originalResponse) {
          try {
            const responseText = error.originalResponse.getBody ? error.originalResponse.getBody() : null;
            if (responseText) {
              try {
                const jsonResponse = JSON.parse(responseText);
                if (jsonResponse.message) {
                  errorMessage = jsonResponse.message;
                }
              } catch (e) {
                errorMessage = `Sunucu yanıtı: ${responseText.substring(0, 100)}...`;
              }
            }
          } catch (parseError) {
            console.error('Error response parsing failed:', parseError);
          }
        }

        toast.error(`Yükleme hatası: ${errorMessage}`);
        emits('error', {...metaData, message: errorMessage});
      }
    },

    // İlerleme durumu callback'i
    onProgress: function (bytesUploaded, bytesTotal) {
      const percentage = ((bytesUploaded / bytesTotal) * 100).toFixed(2);
      metaData.percentage = percentage;

      if (typeof onProgress === 'function') {
        onProgress(percentage, bytesUploaded, bytesTotal);
      }

      emits('progress', metaData);
    },

    // Yükleme tamamlandı callback'i
    onSuccess: async function (payload) {
      console.log("Upload completed successfully", payload);
      uploading.value = false;

      try {
        const {lastResponse} = payload;
        const errorMessage = lastResponse.getHeader('error_message');

        if (errorMessage) {
          // Sunucu hatası durumu
          if (typeof onError === 'function') {
            onError(new Error(errorMessage));
          }

          emits('error', {...metaData, message: errorMessage});
          toast.error(`Sunucu hatası: ${errorMessage}`);
        } else {
          // Sunucudan gelen durum mesajını kontrol et
          const status = lastResponse.getHeader('status');
          const message = lastResponse.getHeader('message');
          const uploadInfo = lastResponse.getHeader('upload_info');
          const fileName = lastResponse.getHeader('file_name') || metaData.originalName;

          console.debug('Server response:', { status, message, uploadInfo, fileName });

          // Eğer işlem hemen tamamlandıysa
          if (status !== 'processing' && uploadInfo) {
            try {
              // Şarkı bilgilerini al
              console.log("Getting song info for ID:", uploadInfo);
              const response = await crudStore.post(route('control.find.songs'), {
                id: uploadInfo
              });

              console.log("Song info response:", response);

              // İşlemi tamamlanmış olarak işaretle
              response.percentage = 100;
              response.is_completed = true; // Kesinlikle true olmalı

              // Pivot bilgisi yoksa ekle
              if (!response.pivot && response.products && response.products.length > 0) {
                response.pivot = response.products[0].pivot;
              }

              // Pivot verisi hala yoksa varsayılan oluştur
              if (!response.pivot && props.product_id) {
                response.pivot = {
                  is_favorite: 0,
                  product_id: props.product_id
                };
              }

              if (typeof onSuccess === 'function') {
                onSuccess(response);
              }

              emits('complete', response);
              // Başarı mesajı
              toast.success(createSuccessMessage(fileName));
            } catch (err) {
              console.error('Song fetch error:', err);

              if (typeof onError === 'function') {
                onError(err);
              }

              emits('error', {...metaData, message: 'Şarkı bilgileri alınamadı'});
              toast.error('Şarkı bilgileri alınamadı');
            }
          } else if (status === 'processing') {
            console.log("STATUS PROCESSİNG ",status);

            // İşlem arka planda devam ediyor
            toast.info(createProcessingMessage(fileName));

            // Polling ile durumu kontrol edelim (broadcasting çalışmazsa)
            if (uploadInfo) {
              // İşleme başladığını hemen bildir
              emits('progress', {...metaData, percentage: 50, message: 'Dosya işleniyor...'});

              let checkCount = 0;
              const MAX_CHECK_COUNT = 36; // 3 dakika (36 * 5 saniye)

              const pollInterval = setInterval(async () => {
                try {
                  checkCount++;
                  console.log("Polling for status, attempt #" + checkCount);

                  const response = await crudStore.post(route('control.find.songs'), {
                    id: uploadInfo
                  });

                  if (response && response.id) {
                    clearInterval(pollInterval);

                    // Başarılı işlemi tamamla
                    response.percentage = 100;
                    response.is_completed = true; // Kesinlikle true olmalı

                    // Pivot bilgisi yoksa ekle
                    if (!response.pivot && response.products && response.products.length > 0) {
                      response.pivot = response.products[0].pivot;
                    }

                    // Pivot verisi hala yoksa varsayılan oluştur
                    if (!response.pivot && props.product_id) {
                      response.pivot = {
                        is_favorite: 0,
                        product_id: props.product_id
                      };
                    }

                    if (typeof onSuccess === 'function') {
                      onSuccess(response);
                    }

                    emits('complete', response);
                    toast.success(createSuccessMessage(response.name || fileName));
                  } else if (checkCount >= MAX_CHECK_COUNT) {
                    console.log("BURAYAA DÜŞTÜkk");

                    // Maksimum kontrol sayısına ulaşıldı
                    clearInterval(pollInterval);
                    toast.info('Dosya yüklendi, ancak işleme durumu belirlenemedi. Sayfayı yenileyip kontrol edebilirsiniz.');
                  }
                } catch (err) {

                  console.warn('Polling fetch error:', err);
                  // Hatayı görmezden gel ve polling'e devam et

                  if (checkCount >= MAX_CHECK_COUNT) {
                    // Maksimum kontrol sayısına ulaşıldı
                    clearInterval(pollInterval);
                    toast.info('Dosya yüklendi, ancak işleme durumu belirlenemedi. Sayfayı yenileyip kontrol edebilirsiniz.');
                  }
                }
              }, 5000); // 5 saniyede bir kontrol et
            }
          } else {
            console.log("STATUS MTATÜS YOK");
            console.log("STATUS",status);

            // Durum bilinmiyor
            toast.warning(`Dosya yüklendi, ancak durum belirlenemedi. Sayfayı yenileyip kontrol edebilirsiniz.`);
          }
        }
      } catch (responseParseError) {
        console.error('Error parsing server response:', responseParseError);
        toast.error('Sunucu yanıtı işlenirken hata oluştu. Lütfen daha sonra tekrar deneyin.');

        if (typeof onError === 'function') {
          onError(responseParseError);
        }
      }

      if (fileInput.value) {
        fileInput.value.value = null;
      }
    },
  });

  return upload;
};

const removeImage = (index) => {
  images.splice(index, 1);
};

// Tüm yüklemeleri iptal et
const cancelAllUploads = () => {
  uploadTasks.value.forEach(upload => {
    if (upload && typeof upload.abort === 'function') {
      upload.abort();
    }
  });

  uploadTasks.value = [];
  uploading.value = false;

  toast.info('Tüm yüklemeler iptal edildi');
};

// Komponenti dışa aç
defineExpose({
  triggerFileInput,
  cancelAllUploads
});
</script>

<style scoped>
.drop-area {
  height: 100%;
  border: 2px dashed var(--soft-200);
  border-radius: 0.5rem;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  min-height: 350px;
  transition: all 0.3s ease;
  background-color: var(--soft-50);
}

.drop-area:hover {
  border-color: var(--dark-green-500);
  background-color: var(--soft-100);
}

.dragging {
  border-color: var(--dark-green-500);
  background-color: var(--soft-100);
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>
