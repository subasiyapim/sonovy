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
        <AddIcon color="var(--dark-green-600)"/>
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

      <AppTable ref="songsTable" :hasSelect="true" v-model="form.songs" :showEmptyImage="false"
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
                <p class="label-sm c-solid-950"> {{ scope.row.name }}</p>
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

        <AppTableColumn label="Aksiyonlar" align="right">
          <template #default="scope">
            <IconButton @click="favoriteSong(scope.row)">
              <StarFilledIcon v-if="scope.row.pivot?.is_favorite" color="#FF8447"/>
              <StarIcon v-else color="var(--sub-600)"/>


            </IconButton>
            <tippy :maxWidth="440" ref="myTippy" theme="light" :allowHtml="true" :sticky="true" trigger="click"
                   :interactive="true" :appendTo="getBody">
              <IconButton>
                <TrashIcon color="var(--sub-600)"/>
              </IconButton>
              <template #content>
                <ConfirmDeleteDialog @confirm="onDeleteSong(scope.row)" @cancel="onCancel"
                                     title="Parçayı silmek istediğinze emin misin"
                                     description="Yüklediğiniz parçalardan albümden silenecektir. Daha sonra tekrar yayınlanma öncesi albüme parça ekleyebilirsiniz."/>
              </template>
            </tippy>

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
          <TusUploadInput @error="onErrorOccured" :type="product.type" :product_id="product.id" ref="tusUploadElement"
                          @start="onTusStart"
                          @progress="onTusProgress"
                          @complete="onTusComplete"></TusUploadInput>
        </template>
      </AppTable>
    </div>

  </div>
  <SongDialog v-if="isSongDialogOn" :product_id="product.id" @done="onComplete" v-model="isSongDialogOn"
              :genres="genres" :song="choosenSong"></SongDialog>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed, ref, useSlots, nextTick, onBeforeMount} from 'vue';
import {FormElement} from '@/Components/Form';
import {useCrudStore} from '@/Stores/useCrudStore';
import {AddIcon} from '@/Components/Icons'
import {TusUploadInput} from '@/Components/Form'
import {SongLoadingCard} from '@/Components/Cards';
import {StatusBadge} from '@/Components/Badges';
import {SongDialog, ConfirmDeleteDialog} from '@/Components/Dialog';
import {RegularButton, PrimaryButton, IconButton} from '@/Components/Buttons'
import {
  StarIcon,
  StarFilledIcon,
  TrashIcon,
  EditIcon,
  DraggableIcon,
  MusicVideoIcon,
  PlayCircleFillIcon
} from '@/Components/Icons';
import {toast} from 'vue3-toastify';

const attemps = ref([], {deep: true});
const props = defineProps({
  product: {},
  genres: {},
  modelValue: {},
})

const crudStore = useCrudStore();
const choosenSongs = ref([]);
const songsTable = ref();
const form = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const myTippy = ref();
const tusUploadElement = ref();
const showAttempt = ref(false);
const isSongDialogOn = ref(false);
const onTusStart = (e) => {
  showAttempt.value = true;
  console.log("STARTT", e);
  attemps.value.push(e);
}

const deleteSong = async (songs) => {

  const response = await crudStore.post(route('control.catalog.songs.songsDelete'), {
    ids: songs,
    product_id: props.product.id
  })

  songs.forEach(element => {
    const findedIndex = form.value.songs.findIndex((e) => e.id == element);
    if (findedIndex >= 0) {
      form.value.songs.splice(findedIndex, 1);
    }
  });

  toast.success("İşlem başarılı");

//   console.log("RESPONSEE", response);

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
const onTusComplete = (e) => {

  const findedIndex = attemps.value.findIndex((el) => el.originalName == e.name);
  // console.log("EEE", e);
//  console.log("ATTEMPTS", attemps.value);

  //console.log("FINDED INDEX", findedIndex);

  if (findedIndex >= 0) {
    attemps.value.splice(findedIndex, 1);
    form.value.songs.push(e);
  }


}

const openEditDialog = (song) => {
  isSongDialogOn.value = true
  choosenSong.value = song
}

const onComplete = (e) => {
  choosenSong.value = JSON.parse(JSON.stringify(e));
  const findedIndex = form.value.songs.findIndex((el) => el.id == e.id);
  isSongDialogOn.value = false;

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
  deleteSong([row.id])
    console.log("GELDİİİ");

  onCancel();

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

  deleteSong(tempIds)
  onCancel();
}
const favoriteSong = async (song) => {
  const response = await crudStore.post(route('control.catalog.song.toggleFavorite', song.id), {
    product_id: props.product.id
  })

  console.log("SONG", response);
  form.value.songs.forEach(element => {
    if (song.id == element.id) {
      element.pivot.is_favorite = response.pivot.is_favorite;
    } else {
      if (element.pivot) {
        element.pivot.is_favorite = 0
      }
    }

  });
  toast.success("Şarkının favori durumu başarıyla değiştirildi");
//   song.pivot?.is_favorite = !song.pivot.is_favorite;


}
const deSelectAll = () => {
  choosenSongs.value = [];
  songsTable.value.deSelect();
}
onBeforeMount(() => {
  form.value.songs = props.product.songs;
});
</script>

<style lang="scss" scoped>

</style>
