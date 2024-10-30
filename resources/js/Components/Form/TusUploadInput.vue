<template>
  <div class="w-full h-full" >
    <!-- Drag-and-Drop Area -->

    <div v-if="images.length" class="image-preview mb-3">
      <h3>Preview:</h3>
      <div class="preview-grid">
        <div v-for="(image, index) in images" :key="index" class="preview-item">
          <img :src="image.url" :alt="'Image Preview ' + (index + 1)" />

        </div>
        <button @click="removeImage" class="label-xs c-neutral-500 text-center mx-auto">Yayın Görselini Sil</button>
      </div>
    </div>
    <div
        v-else
        @click="triggerFileInput"
        class="drop-area"
        @dragover.prevent="handleDragOver"
        @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop"
        :class="{ 'dragging': isDragging }"
        >
        <div class="flex flex-col items-center justify-center gap-5">
                <div class="w-16 h-16 rounded-full bg-white-600 flex items-center justify-center">
                        <SongFileIcon color="var(--dark-green-800)" />
                </div>
               <div class="">
                    <p class="c-strong-950 label-medium !text-center">
                        Yayın eklemek istediğiniz parçaları seçin veya sürükleyip bırakın.
                    </p>
                    <p class="paragraph-medium c-neutral-500 !text-center">
                        Format: MP3, AAC, VMA...
                    </p>
               </div>
                <div class="flex items-center gap-3">
                    <PrimaryButton>
                        <template #icon>
                                <AddIcon color="var(--dark-green-500)" />
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
        accept="image/*"
        multiple
        @change="handleFileInput"
        hidden
      />
    </div>

    <!-- Image Preview -->

  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import {SongFileIcon,AddIcon} from '@/Components/Icons'
import {RegularButton,PrimaryButton} from '@/Components/Buttons'


const fileInput = ref(null);
const isDragging = ref(false);
const images = reactive([]);

const handleDragOver = () => {
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
};

const handleDrop = (event) => {
  isDragging.value = false;
  const files = Array.from(event.dataTransfer.files);
  handleFiles(files);
};

const triggerFileInput = () => {

    console.log(fileInput.value);
  fileInput.value.click();
};

const handleFileInput = (event) => {
  const files = Array.from(event.target.files);
  handleFiles(files);
};

const handleFiles = (files) => {
  files.forEach((file) => {

    const reader = new FileReader();
    reader.onload = (e) => {
    images.push({ file, url: e.target.result });
    };
    reader.readAsDataURL(file);

  });
};

const removeImage = (index) => {
  images.splice(index, 1);
};
</script>

<style scoped>
.image-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.drop-area {
  width: 100%;
  height:100%;
  padding: 40px;
  border-radius: 10px;
  text-align: center;
  margin-bottom: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display:flex;
  align-items:center;
  justify-content:center;
}

.drop-area.dragging {
  background-color: #f0f0f0;
}

.image-preview {
  width: 100%;
}

.preview-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
}



.preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 8px;
}


</style>
