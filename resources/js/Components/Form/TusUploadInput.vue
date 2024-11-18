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
            Format: MP3, AAC, VMA...
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
          accept="mp3, wav, aac, mp4, avi, mkv"
          multiple
          @change="onChangeInput"
          hidden
      />
    </div>

    <!-- Image Preview -->

  </div>
</template>

<script setup>
import {ref, reactive, onBeforeMount} from 'vue';
import {SongFileIcon, AddIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {useCrudStore} from '@/Stores/useCrudStore';

const props = defineProps({
  modelValue: {
    default: {}
  },
  pageData: {},
  errorObject: {},
  type: {
    default: 1
  },
  product_id:{

  }
})
const emits = defineEmits(['start', 'progress', 'complete','error'])

const csrf_token = ref('');
const acceptedAudioExtensions = ['mp3', 'wav', 'aac'];
const acceptedVideoExtensions = ['mp4', 'avi', 'mkv'];

const crudStore = useCrudStore();
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

const onChangeInput = (e) => {


  const files = Array.from(e.target.files);
  handleFiles(files);

}
const triggerFileInput = () => {


  fileInput.value.click();
};


const handleFiles = (files) => {
  files.forEach((file) => {

    const reader = new FileReader();
    reader.onload = (e) => {

      handleFileInput(file);
    };
    reader.readAsDataURL(file);

  });
};


const handleFileInput = (e) => {


  let file = e;
  let loadingPercent = 0;

  // File type validation
  const file_extension = file.name.split('.').pop().toLowerCase();
  if ((props.type === 1 && !acceptedAudioExtensions.includes(file_extension)) ||
      (props.type === 2 && !acceptedVideoExtensions.includes(file_extension))) {
    // popUp('Bu yayın tipi için dosya türü desteklenmiyor', 'error', false);
    alert("Hata")
    return;
  }

  let currentIndex = 0;

  const extension = file.name.split('.').pop();
  const uniqueId = Date.now().toString(36);
  const tempFileName = `${uniqueId}_${Date.now()}.${extension}`;
  let metaData = {
    filename: tempFileName,
    mime_type: file.type,
    orignalName: file.name,
    type: props.type,
    size: file.size,
    percentage: 0,
    product_id:props.product_id,
  };

  emits('start', metaData)
  var upload = new tus.Upload(file, {
    headers: {
      'X-CSRF-TOKEN': csrf_token.value,
      '_method': 'POST'
    },
    endpoint: route('control.tus', {
      type: props.type,
      name: file.name,
      temp: tempFileName,

    }),
    metadata: metaData,

    uploadDataDuringCreation: true,
    onStart: function (e) {
      metaData.percentage = 0;
      console.log("BAŞLADIII");


    },
    // Callback for errors which cannot be fixed using retries
    onError: function (error) {
      console.log('Failed because: ' + error)
    },
    // Callback for reporting upload progress
    onProgress: function (bytesUploaded, bytesTotal) {
      var percentage = ((bytesUploaded / bytesTotal) * 100).toFixed(2)
      //console.log(bytesUploaded, bytesTotal, percentage + '%')
      //   form.value.songs[currentIndex].percent = parseInt(percentage);

      metaData.percentage = percentage;
      emits('progress', metaData)
    },
    // Callback for once the upload is completed
    onSuccess: async function (payload) {
      const {lastResponse} = payload
    let errorMessage = lastResponse.getHeader('error_message');
    if(errorMessage){
        emits('error', {...metaData,message:errorMessage})

    }else {
         let response = await crudStore.post(route('control.find.songs'), {

            id: lastResponse.getHeader('upload_info')
        });

        console.log("RESPONSEE",response);

        response.percentage = 100;
        emits('complete', response)
    }

    },
  })

  // Check if there are any previous uploads to continue.
  upload.findPreviousUploads().then(function (previousUploads) {
    // Found previous uploads so we select the first one.
    if (previousUploads.length) {
      upload.resumeFromPreviousUpload(previousUploads[0])
    }
    // Start the upload
    upload.start()
  })

};

const removeImage = (index) => {
  images.splice(index, 1);
};

onBeforeMount(() => {
  csrf_token.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
})

defineExpose({
  triggerFileInput,
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
  height: 100%;
  padding: 40px;
  border-radius: 10px;
  text-align: center;
  margin-bottom: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
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
