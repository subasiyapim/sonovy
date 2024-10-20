<template>
  <div class="singleUpload" >

    <div class="flex items-start gap-5 ">
        <div class="w-16 h-16 rounded-full overflow-hidden">
            <img v-if="!image?.url" src="@/assets/images/avatar.png">
            <img v-else :src="image?.url">
        </div>
        <div class="flex flex-col items-start justify-start gap-3">

               <div class="text-start">
                    <p class="c-strong-950 label-sm ">{{label}}</p>
                    <p class="paragraph-xs c-sub-400 ">{{note}}</p>
               </div>
                <div v-if="!image?.url">
                    <RegularButton @click="triggerFileInput">
                        Göz At
                    </RegularButton>
                </div>
                 <div v-else class="flex items-center gap-2">
                    <RegularButton type="error"  @click="removeImage">
                        Sil
                    </RegularButton>
                    <RegularButton @click="triggerFileInput">
                        Değiştir
                    </RegularButton>
                </div>

        </div>
      <input
        v-if="rendered"
        ref="fileInput"
        type="file"
        accept="image/*"
        @change="handleFileInput"
        hidden
      />
    </div>

    <!-- Image Preview -->

  </div>
</template>

<script setup>
import { ref, reactive ,computed,nextTick} from 'vue';
import {RegularButton} from '@/Components/Buttons'

const props = defineProps({
    modelValue:{},
    label:{},
    note:{}
})
const rendered = ref(true);
const emits = defineEmits(['update:modelValue']);
const fileInput = ref(null);
const isDragging = ref(false);
const image = computed({
    get:() => props.modelValue ,
    set:(value) => emits('update:modelValue',value)
})


const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFileInput = (event) => {
    console.log("GELDİİİ");
    const files = Array.from(event.target.files);
    handleFiles(files);
};

const handleFiles = (files) => {
  files.forEach((file) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        image.value = {file,url: e.target.result}

      };

      reader.readAsDataURL(file);
    }
  });
};

const removeImage = () => {
    rendered.value = false;


  image.value = {};
  console.log(fileInput.value.target);
  nextTick(() => {
    rendered.value = true;
  })
};
</script>

<style scoped>
.singleUpload {
  display: flex;
  flex-direction: column;
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
