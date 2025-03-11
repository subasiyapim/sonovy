<template>

  <div class="pb-6">
    <p class="label-xl c-strong-950">
      Şimdi, yayına dahil eklemek istediğiniz parçaları yükleyin 👍🏻
    </p>
  </div>
  <div class="flex items-center gap-3 mb-6">
    <RegularButton>Katalogdan Seç</RegularButton>
    <PrimaryButton @click="onSongAdd">
      <template #icon>
        <AddIcon color="var(--dark-green-500)"/>
      </template>
      Parça Ekle
    </PrimaryButton>

    <div v-if="choosenSongs.length > 0" class="flex items-center ms-auto gap-3">
      <button @click="deSelectAll" class="label-sm c-neutral-500">Seçimi Kaldır</button>
      <tippy :maxWidth="440" ref="myTippy" theme="light" :allowHtml="true" :sticky="true" trigger="click"
             :interactive="true" :appendTo="getBody">
        <button class="flex items-center gap-1">
          <TrashIcon color="var(--error-500)"/>
          <span class="c-error-500 label-sm">Seçili Olanları Sil</span>
        </button>
        <template #content>
          <ConfirmDeleteDialog @confirm="deleteChoosenSongs" @cancel="onCancel"
                               title="Parçayı silmek istediğinze emin misin"
                               description="Yüklediğiniz parçalardan albümden silenecektir. Daha sonra tekrar yayınlanma öncesi albüme parça ekleyebilirsiniz."/>
        </template>
      </tippy>

    </div>
  </div>
  <div class="flex gap-10">

    <div class="flex-1 flex flex-col overflow-scroll gap-6">

      <AppTable @dragenter="onDragEnter" :showEmptyOnDrag="isDraggingOn" ref="songsTable" :hasSelect="true"
                v-model="form.songs" :showEmptyImage="false"
                @selectionChange="onSelectChange" :isClient="true" :hasSearch="false" :showAddButton="false">
        <AppTableColumn label="#">
          <template #default="scope">
            <DraggableIcon color="var(--sub-600)"/>
          </template>
        </AppTableColumn>
        <AppTableColumn label="Parça Adı">
          <template #default="scope">

            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full flex items-center justify-center p-3 bg-dark-green-800">
                <img src="@/assets/images/mp3_active.png">
              </div>
              <div>
                <p class="label-sm c-solid-950"> {{ scope.row.name }} <template v-if="scope.row.version">({{ scope.row.version }})</template></p>
                <p class="paragraph-xs c-sub-600"> {{ (scope.row.size / (1024 * 1024)).toFixed(2) }} MB</p>
              </div>
            </div>

          </template>
        </AppTableColumn>
        <AppTableColumn label="Süre">
          <template #default="scope">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                <PlayCircleFillIcon color="var(--dark-green-500)"/>
              </div>
              <p class="label-sm c-strong-950">
                {{ scope.row.duration ?? '2.35' }}
              </p>
            </div>
          </template>
        </AppTableColumn>

        <AppTableColumn label="Durum">
          <template #default="scope" :showIcon="true">
            <StatusBadge v-if="scope.row.is_completed" type="success">
              <p v-if="scope.row.is_completed" class="c-sub-600"> Tamamlandı</p>
            </StatusBadge>
            <StatusBadge v-else type="pending">
              <p class="c-orange-700"> Bilgiler Eksik</p>
            </StatusBadge>
          </template>
        </AppTableColumn>

        <AppTableColumn label="Kalite Kontrolü">
          <template #default="scope">
            <div class="flex items-center gap-2">
              <!-- Analiz sonucu varsa -->
              <template v-if="hasQualityData(scope.row)">
                <!-- Kalite kontrolünü geçtiyse -->
                <div v-if="scope.row.details.quality_analysis.is_valid"
                     class="flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5">
                  <CheckIcon class="text-green-600" size="14"/>
                  <span class="text-[10px] text-green-600">Kontrolü Geçti</span>
                </div>

                <!-- Kalite sorunları varsa -->
                <div v-else
                     class="flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-0.5">
                  <WarningIcon class="text-yellow-600" size="14"/>
                  <span class="text-[10px] text-yellow-600">Sorunlar Var</span>
                </div>

                <!-- Her durumda Sonuçları Gör butonu göster -->
                <button
                    @click="openQualityModal(scope.row)"
                    class="label-xs flex items-center gap-1 text-dark-green-600 hover:text-dark-green-800 ml-1"
                >
                  <AnalyzeIcon color="currentColor" size="16"/>
                  Sonuçları Gör
                </button>
              </template>

              <!-- Analiz sonucu yoksa (bu durumla karşılaşılmamalı ama yine de gösterelim) -->
              <template v-else>
                <button
                    @click="runQualityAnalysis(scope.row)"
                    class="label-xs flex items-center gap-1 text-dark-green-600 hover:text-dark-green-800"
                >
                  <AnalyzeIcon color="currentColor" size="16"/>
                  Analiz Et
                </button>
              </template>
            </div>
          </template>
        </AppTableColumn>

        <AppTableColumn label="Aksiyonlar" align="right">
          <template #default="scope">
            <IconButton @click="favoriteSong(scope.row)">
              <StarFilledIcon v-if="scope.row.pivot?.is_favorite" color="#FF8447"/>
              <StarIcon v-else color="var(--sub-600)"/>


            </IconButton>
            <DeleteAction @onDeleteSong="onDeleteSong(scope.row)"></DeleteAction>

            <IconButton @click="openEditDialog(scope.row)">
              <EditIcon color="var(--sub-600)"/>
            </IconButton>
          </template>
        </AppTableColumn>
        <template #appends v-if="attemps.length > 0 && showAttempt">
          <div class="flex flex-col gap-2">

            <SongLoadingCard v-model="attemps[index]" @remove="attemps.splice(index,1)" :key="index"
                             v-for="(loadingCardAttempt,index) in attemps"/>
          </div>
        </template>
        <template #empty>
          <TusUploadInput @dragleave="onDragLeave" @drop="onDropElement" @error="onErrorOccured" :type="product.type"
                          :product_id="product.id" ref="tusUploadElement"
                          :acceptedAudioExtensions="acceptedFormats"
                          @start="onTusStart"
                          @progress="onTusProgress"
                          @complete="onTusComplete"></TusUploadInput>
        </template>
      </AppTable>
    </div>

  </div>
  <SongDialog v-if="isSongDialogOn" :product_id="product.id" @done="onComplete" v-model="isSongDialogOn"
              :genres="genres" :song="choosenSong"></SongDialog>
  <AudioQualityModal v-if="isQualityModalOpen && selectedSong" v-model="isQualityModalOpen" :song="selectedSong"/>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed, ref, useSlots, nextTick, onBeforeMount, watch,onMounted} from 'vue';
import {FormElement} from '@/Components/Form';
import {useCrudStore} from '@/Stores/useCrudStore';
import {AddIcon} from '@/Components/Icons'
import {TusUploadInput} from '@/Components/Form'
import {SongLoadingCard} from '@/Components/Cards';
import {StatusBadge} from '@/Components/Badges';
import DeleteAction from './Components/DeleteAction.vue';
import {SongDialog, ConfirmDeleteDialog, AudioQualityModal} from '@/Components/Dialog';
import {RegularButton, PrimaryButton, IconButton} from '@/Components/Buttons'
import {usePage} from '@inertiajs/vue3';
import {
  StarIcon,
  StarFilledIcon,
  TrashIcon,
  EditIcon,
  DraggableIcon,
  MusicVideoIcon,
  PlayCircleFillIcon,
  AnalyzeIcon,
  CheckIcon,
  WarningIcon
} from '@/Components/Icons';
import {toast} from 'vue3-toastify';

const attemps = ref([], {deep: true});
const props = defineProps({
  product: {},
  genres: {},
  modelValue: {},
})

const emits = defineEmits(['update:modelValue']);

const onDragEnter = (e) => {
  isDraggingOn.value = true;


}

const onDragLeave = (e) => {
  console.log("ÇIKI");

  isDraggingOn.value = false;

}

const onDropElement = () => {
  isDraggingOn.value = false;
}
const crudStore = useCrudStore();
const choosenSongs = ref([]);
const songsTable = ref();
const form = ref({
  songs: []
});

// Props değişikliklerini izle
watch(() => props.modelValue, (newValue) => {
  if (newValue && newValue.songs) {
    form.value = {
      ...newValue,
      songs: Array.isArray(newValue.songs) ? [...newValue.songs] : []
    };
  }
}, {immediate: true, deep: true});

// Props değişikliklerini izle - product
watch(() => props.product, (newValue) => {
  if (newValue && newValue.songs) {
    form.value = {
      ...form.value,
      songs: Array.isArray(newValue.songs) ? [...newValue.songs] : []
    };
  }
}, {immediate: true, deep: true});

// Sayfa yüklendiğinde şarkıları yükle
onBeforeMount(() => {
  if (props.product && props.product.songs) {
    form.value = {
      ...form.value,
      songs: Array.isArray(props.product.songs) ? [...props.product.songs] : []
    };
  }
});

const isUploadShown = ref(true);
const myTippy = ref();
const tusUploadElement = ref();
const showAttempt = ref(false);
const isSongDialogOn = ref(false);
const isQualityModalOpen = ref(false);
const selectedSong = ref(null);
const onTusStart = (e) => {
  showAttempt.value = true;
  attemps.value.push(e);
}

const deleteSong = async (songs) => {
  try {
    await crudStore.post(route('control.catalog.songs.songsDelete'), {
      ids: songs,
      product_id: props.product.id
    });

    // Mevcut şarkıları al ve silinenleri çıkar
    const currentSongs = Array.isArray(form.value.songs)
        ? form.value.songs.filter(song => !songs.includes(song.id))
        : [];

    // Form değerini güncelle
    form.value = {
      ...form.value,
      songs: currentSongs
    };

    // Üst bileşene değişikliği bildir
    emits('update:modelValue', form.value);


    // Seçili şarkıları temizle
    choosenSongs.value = [];

    // Tabloyu yeniden render et
    nextTick(() => {
      if (songsTable.value) {
        songsTable.value.deSelect();

        songs.forEach(s => {
            songsTable.value.removeRowByIndex(form.value.songs.findIndex((e) => e == s));
        });

        songsTable.value.$forceUpdate();
      }
    });

    toast.success("Şarkı(lar) başarıyla silindi");
  } catch (error) {
    console.error("Şarkı silme hatası:", error);
    toast.error("Şarkı(lar) silinirken bir hata oluştu");
  }
}

const choosenSong = ref();

const onSongAdd = () => {
  tusUploadElement.value.triggerFileInput();
}
const onTusProgress = (e) => {
  showAttempt.value = false;
  const findedIndex = attemps.value.findIndex((el) => el.filename == e.filename);
  if (findedIndex >= 0) {
    attemps.value[findedIndex].percentage = e.percentage;
  }
  nextTick(() => {
    showAttempt.value = true;
  })
}
const onTusComplete = async (e) => {
  console.log("TUS Upload complete:", e);

  // Şarkı verilerini kontrol et
  if (!e || !e.name) {
    console.error("Geçersiz şarkı verisi:", e);
    return;
  }

  try {
    // Şarkı bilgilerini tekrar al
    const songResponse = await crudStore.post(route('control.find.songs'), {
      id: e.id
    });

    console.log("Fetched song data:", songResponse);

    if (!songResponse) {
      console.error("Şarkı bilgileri alınamadı");
      return;
    }

    // Kalite analizi yap
    try {
      const qualityAnalysis = await crudStore.post(route('control.catalog.songs.quality-analysis'), {
        song_id: e.id
      });

      console.log("Kalite analizi tamamlandı:", qualityAnalysis);

      // Kalite analizi bilgilerini songResponse'a ekle
      if (qualityAnalysis && qualityAnalysis.data) {
        songResponse.details = songResponse.details || {};
        songResponse.details.quality_analysis = qualityAnalysis.data;
      }
    } catch (qualityError) {
      console.error("Kalite analizi yapılırken hata oluştu:", qualityError);
      // Kalite analizi hata verse bile işleme devam et
    }

    // Yükleme bilgilerini kaldır
    const findedIndex = attemps.value.findIndex((el) => el.originalName == e.name);
    if (findedIndex >= 0) {
      attemps.value.splice(findedIndex, 1);
    }

    // Şarkı nesnesini hazırla
    const songData = {
      ...songResponse,
      is_completed: true,
      pivot: {
        is_favorite: 0,
        product_id: props.product.id,
        ...(songResponse.pivot || {})
      }
    };

    // Mevcut şarkıları al ve yeni şarkıyı ekle
    const currentSongs = Array.isArray(form.value.songs) ? [...form.value.songs] : [];
    currentSongs.push(songData);

    // Form değerini güncelle
    form.value = {
      ...form.value,
      songs: currentSongs
    };

    // Üst bileşene değişikliği bildir
    emits('update:modelValue', form.value);

    // Tabloyu yeniden render et
    nextTick(() => {
      if (songsTable.value) {
        songsTable.value.$forceUpdate();
      }
    });

    console.log("Updated form value:", form.value);

    // Kalite analizi tamamlandıysa bilgilendirme mesajı göster
    if (songResponse.details?.quality_analysis) {
      const isValid = songResponse.details.quality_analysis.is_valid;
      const message = isValid
          ? 'Parça başarıyla yüklendi ve kalite kontrolünden geçti.'
          : 'Parça yüklendi fakat kalite kontrolünde sorunlar tespit edildi. Detaylar için "Sonuçları Gör" butonuna tıklayın.';

      toast.success(message);
    } else {
      toast.success("Parça başarıyla yüklendi.");
    }

  } catch (error) {
    console.error("Şarkı ekleme hatası:", error);
    toast.error("Şarkı eklenirken bir hata oluştu");
  }
}
const isDraggingOn = ref(false);
const openEditDialog = (song) => {


  choosenSong.value = song


  if (song.main_artists?.length == 0) {
    if (props.product.main_artists.length > 0) {


      choosenSong.value.main_artists = props.product.main_artists;

    }
  }


  isSongDialogOn.value = true
}

const onComplete = (e) => {
  choosenSong.value = JSON.parse(JSON.stringify(e));
  const findedIndex = form.value.songs.findIndex((el) => el.id == e.id);
  isSongDialogOn.value = false;
  console.log("EEE", e);

  if (findedIndex >= 0)
    form.value.songs[findedIndex] = e;
  choosenSong.value = null;

}

const getBody = computed(() => {
  return document.querySelector('body');
})

const onCancel = () => {
  myTippy.value?.hide();
}
const onDeleteSong = (row) => {
  deleteSong([row.id]);
}
const onErrorOccured = (e) => {

  let findedIndex = attemps.value.findIndex((el) => el.filename == e.filename)

  if (findedIndex >= 0) {
    attemps.value[findedIndex].errorMessage = e.message;

  }

}
const onSelectChange = (e) => {

  choosenSongs.value = Object.values(e);

}
const deleteChoosenSongs = () => {
  const tempIds = choosenSongs.value.map((e) => e.id);
  deleteSong(tempIds);
  onCancel();
}
const favoriteSong = async (song) => {

  try {
    const response = await crudStore.post(route('control.catalog.song.toggleFavorite', song.id), {
      product_id: props.product.id
    });

    // Mevcut şarkıları al ve güncelle
    const findedIndex = form.value.songs.findIndex((e) => e.id == song.id);
    form.value.songs.forEach(element => {
        element.pivot.is_favorite = 0;
    });
    if(findedIndex >= 0){
        form.value.songs[findedIndex].pivot.is_favorite = response.pivot.is_favorite;
    }



    emits('update:modelValue', form.value);



    toast.success("Şarkının favori durumu başarıyla değiştirildi");
  } catch (error) {
    console.error("Favori durumu değiştirme hatası:", error);
    toast.error("Favori durumu değiştirilirken bir hata oluştu");
  }
}
const deSelectAll = () => {
  choosenSongs.value = [];
  songsTable.value.deSelect();
}

const openQualityModal = (song) => {
  selectedSong.value = song;
  isQualityModalOpen.value = true;
}

// Bir şarkının manuel olarak kalite analizini yapmak için kullanılır
const runQualityAnalysis = async (song) => {
  try {
    toast.info(`"${song.name}" için kalite analizi başlatılıyor...`);

    const qualityAnalysis = await crudStore.post(route('control.catalog.songs.quality-analysis'), {
      song_id: song.id
    });

    if (qualityAnalysis && qualityAnalysis.data) {
      // Analiz verilerini şarkı nesnesine ekle
      const songIndex = form.value.songs.findIndex(s => s.id === song.id);
      if (songIndex !== -1) {
        // details nesnesini oluştur veya mevcut olanı kullan
        form.value.songs[songIndex].details = form.value.songs[songIndex].details || {};
        // kalite analizi sonuçlarını ekle
        form.value.songs[songIndex].details.quality_analysis = qualityAnalysis.data;

        // Tabloyu yeniden render et
        nextTick(() => {
          if (songsTable.value) {
            songsTable.value.$forceUpdate();
          }
        });

        // Sonuç hakkında bilgilendirme mesajı göster
        const isValid = qualityAnalysis.data.is_valid;
        const message = isValid
            ? `"${song.name}" kalite kontrolünden geçti.`
            : `"${song.name}" kalite kontrolünden sorunlar tespit edildi. Detaylar için "Sonuçları Gör" butonuna tıklayın.`;

        toast.success(message);

        // Analiz sonuçlarını hemen göster
        selectedSong.value = form.value.songs[songIndex];
        isQualityModalOpen.value = true;
      }
    }
  } catch (error) {
    console.error("Kalite analizi yapılırken hata oluştu:", error);
    toast.error(`"${song.name}" için kalite analizi yapılırken bir hata oluştu.`);
  }
}

const hasQualityData = (song) => {
  return song &&
      song.details &&
      song.details.quality_analysis;
}

// Kabul edilen dosya formatlarını site ayarlarından al
const acceptedFormats = computed(() => {
  const settings = usePage().props.site_settings;
  const productType = props.product?.type;

  switch (productType) {
    case 1: // Ses dosyası
      return settings.allowed_song_formats?.split(',') || [];
    case 2: // Video
      return settings.allowed_video_formats?.split(',') || [];
    case 3: // Ringtone
      return settings.allowed_ringtone_formats?.split(',') || [];
    default:
      return [];
  }
});

onMounted(() => {
  const currentProductId = props.product?.id;
  console.log("Ürün ID:", currentProductId);
  console.log("Tenant ID:", usePage().props.tenant_id);

  if (currentProductId) {
    const channel = 'tenant.' + usePage().props.tenant_id + '.song.processing.' + currentProductId;
    console.log("Dinlenen kanal:", channel);

    window.Echo.private(channel)
      .listen('.App\\Events\\SongProcessingComplete', (e) => {
        console.log("Şarkı İşleme Tamamlandı Event Alındı:", e);

        if (e.success) {
          // Şarkı işleme başarılı oldu
          toast.success(e.message || "Şarkı işleme tamamlandı");

          // Eğer bu şarkı zaten listemizde varsa, güncelle
          const songIndex = form.value.songs.findIndex(song => song.id === e.product_id);

          if (songIndex !== -1) {
            // Şarkı zaten listemizde varsa, güncelle
            crudStore.post(route('control.find.songs'), {
              id: e.product_id
            }).then(response => {
              if (response) {
                // Event'ten gelen details bilgisini response'a ekle
                if (e.details) {
                  response.details = e.details;
                }

                // Şarkıyı güncelle
                form.value.songs[songIndex] = {
                  ...response,
                  is_completed: true,
                  details: response.details || e.details, // Event veya API'den gelen details bilgisini kullan
                  pivot: {
                    is_favorite: form.value.songs[songIndex].pivot?.is_favorite || 0,
                    product_id: currentProductId,
                    ...(response.pivot || {})
                  }
                };

                // Kalite analizi sonuçları varsa bildirim göster
                if (e.details?.quality_analysis) {
                  const isValid = e.details.quality_analysis.is_valid;
                  const message = isValid
                    ? `"${response.name}" kalite kontrolünden geçti.`
                    : `"${response.name}" kalite kontrolünde sorunlar tespit edildi. Detaylar için "Sonuçları Gör" butonuna tıklayın.`;

                  toast.success(message);
                }

                // Üst bileşene değişikliği bildir
                emits('update:modelValue', form.value);

                // Tabloyu yeniden render et
                nextTick(() => {
                  if (songsTable.value) {
                    songsTable.value.$forceUpdate();
                  }
                });
              }
            });
          } else {
            // Bu yeni işlenmiş bir şarkı olabilir, yeniden şarkı listesini yükleyelim
            crudStore.get(route('control.catalog.products.show', currentProductId))
              .then(response => {
                if (response && response.songs) {
                  // Event'ten gelen details bilgisini ilgili şarkıya ekle
                  if (e.details) {
                    const songToUpdate = response.songs.find(song => song.id === e.product_id);
                    if (songToUpdate) {
                      songToUpdate.details = e.details;
                    }
                  }

                  form.value = {
                    ...form.value,
                    songs: Array.isArray(response.songs) ? [...response.songs] : []
                  };

                  // Kalite analizi sonuçları varsa bildirim göster
                  if (e.details?.quality_analysis) {
                    const song = response.songs.find(s => s.id === e.product_id);
                    if (song) {
                      const isValid = e.details.quality_analysis.is_valid;
                      const message = isValid
                        ? `"${song.name}" kalite kontrolünden geçti.`
                        : `"${song.name}" kalite kontrolünde sorunlar tespit edildi. Detaylar için "Sonuçları Gör" butonuna tıklayın.`;

                      toast.success(message);
                    }
                  }

                  // Üst bileşene değişikliği bildir
                  emits('update:modelValue', form.value);

                  // Tabloyu yeniden render et
                  nextTick(() => {
                    if (songsTable.value) {
                      songsTable.value.$forceUpdate();
                    }
                  });
                }
              });
          }
        } else {
          // Şarkı işleme başarısız oldu
          toast.error(e.message || "Şarkı işleme başarısız oldu");
        }
      })
      .subscribed(() => {
        console.log('Kanala başarıyla abone olundu:', channel);
      })
      .error((error) => {
        console.error('Kanal bağlantı hatası:', error);
      });
  }
});
</script>

<style lang="scss" scoped>

</style>
