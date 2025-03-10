<template>
  <div class="image-upload" >
    <!-- Drag-and-Drop Area -->

    <div v-if="showImage && images.length" class="image-preview mb-3">
        <div class="preview-grid" :class="uploadType == 'image' ? 'flex-col' : 'flex-row border border-soft-200 p-3 rounded-lg' ">
            <div v-for="(image, index) in images" :key="index" class="preview-item flex-1">
                <template v-if="uploadType === 'image'">
                    <img :src="image.url" :alt="'Image Preview ' + (index + 1)" />
                </template>
                <template v-else>
                    <div class="file-preview ">
                        <PdfIcon v-if="image.type === 'excel'" />
                        <div class="flex flex-col">
                            <p class="label-sm">{{ image.file.name }}</p>
                            <p class="paragraph-xs c-sub-600">{{ (image.file.size / 1024).toFixed(2) }} KB</p>
                        </div>
                    </div>
                </template>
            </div>

            <button @click="removeImage(index)" class="label-xs c-neutral-500 text-center ">
                <p v-if="uploadType === 'image'"> Seçilen resmi kaldır</p>
                <TrashIcon v-else color="var(--sub-600)" />
            </button>
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
                <CloudIcon color="var(--green-500)" />
               <div class="">
                    <p class="c-strong-950 label-sm !text-center">{{label}}</p>
                    <p class="paragraph-xs c-sub-400 !text-center">{{note}}</p>
               </div>
                <div>
                    <RegularButton>
                        Göz At
                    </RegularButton>
                </div>
        </div>
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        multiple
        @change="handleFileInput"
        hidden
      />
    </div>

     <div v-if="images.length && imageProgress" class="imageProgressBar w-full" >
        <div class="innerProgress" :style="{ width: imageProgress + '%' }"></div>
    </div>

    <!-- Image Preview -->

  </div>
</template>

<script setup>
import { ref, reactive,onBeforeMount } from 'vue';
import {CloudIcon,PdfIcon,TrashIcon} from '@/Components/Icons'
import {RegularButton} from '@/Components/Buttons'
const emits = defineEmits(['change','onImageDelete'])
const props = defineProps({
    label:{},
    note:{},
    image:{},
    uploadType:{
        default:'image'
    },
    accept:{
        default:'image/*'
    }
})
const fileInput = ref(null);
const isDragging = ref(false);
const images = reactive([]);

const handleDragOver = () => {
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
};
const imageProgress = ref(0)
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
const showImage = ref(true);
const handleFiles = (files) => {
  showImage.value = false;
  files.forEach((file) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        images.push({ file, url: e.target.result, type: 'image' });
        simulateUpload(file);
      };
      reader.readAsDataURL(file);
    }
    else if (file.type === 'application/pdf') {
      const url = URL.createObjectURL(file);
      images.push({ file, url, type: 'pdf' });
      simulateUpload(file);
    }
    else if (file.type == "text/csv" || file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || file.type === 'application/vnd.ms-excel' || file.type==="application/x-iwork-numbers-sffnumbers") {
      const reader = new FileReader();
      reader.onload = (e) => {
        images.push({ file, url: '', type: 'excel' });
        simulateUpload(file);
      };
      reader.readAsArrayBuffer(file);
    }
    console.log("TYPEE",file.type);

  });
};


const simulateUpload = (file) => {
  const interval = setInterval(() => {
    if (imageProgress.value < 100) {
      imageProgress.value += 10;
    } else {
      clearInterval(interval);
      emits('change',file)
      imageProgress.value = null; // Hide progress bar when complete
    }
  }, 300); // Simulates upload every 300ms
};

const removeImage = (index) => {
  images.splice(index, 1);
  emits('onImageDelete')
};
onBeforeMount(() => {
    if(props.image){
        images.push({
            url:props.image,
        })
    }

});

defineExpose({
    showImage,
    removeImage
})
</script>

<style scoped>
.image-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.drop-area {
  width: 100%;
  max-width: 400px;
  padding: 40px;
  border: 1px dashed var(--soft-300);
  border-radius: 10px;
  text-align: center;
  background:var(--white-500);
  margin-bottom: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
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
<style scoped>
    .imageProgressBar {
        bottom: 5px;
        left: 0;
        right: 0;
        height: 5px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 3px;
        overflow: hidden;
    }

    .innerProgress {
        height: 100%;
        background: var(--dark-green-500);
        transition: width 0.3s ease;
    }

    .file-preview {
    display: flex;
    align-items: center;
    gap: 10px;
    }

    .file-preview img {
    width: 40px;
    height: 40px;
    }
</style>
