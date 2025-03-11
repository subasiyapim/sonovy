<template>
  <Head :title="title"/>
  <div class="flex h-screen w-full flex-1 flex-col md:flex-row ">

    <Sidebar>
    </Sidebar>
    <div class="flex-1 relative custom-scrollbar overflow-auto">
      <div class="flex items-center staticTopInfo">
        <div class="flex items-center gap-3.5 flex-1">
          <IconButton v-if="showGoBack" @click="goBack" hasBorder size="medium">
            <ArrowLeftIcon color="var(--sub-600)"/>
          </IconButton>
          <div class="flex flex-col flex-1">
            <p class="label-lg c-strong-950">{{ title }}</p>
            <template v-if="showBreadCrumb">
              <div v-if="!hasSlot('breadcrumb')" class="flex items-center gap-2">
                <span v-if="parentTitle" class="label-xs c-soft-400">{{ parentTitle }}</span>
                <span v-if="parentTitle" class="label-xs c-soft-400">•</span>

                <span v-if="subParent" class="label-xs c-soft-400">{{ subParent }}</span>
                <span v-if="subParent" class="label-xs c-soft-400">•</span>
                <span class="label-xs c-soft-400">{{ title }}</span>
              </div>
              <div v-else class="flex items-center gap-2">
                <slot name="breadcrumb"/>
              </div>
            </template>

          </div>

          <IconButton>
            <SearchIcon color="var(--sub-600)"/>
          </IconButton>
          <IconButton>
            <NotificationIcon color="var(--sub-600)"/>
          </IconButton>
          <div class="w-40" v-if="showDatePicker">


            <VueDatePicker @cleared="onDateCleared" @range-end="onDateChaned" range v-model="choosenDate"
                           class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
              <template #input-icon>
                <div class="p-3">
                  <CalendarIcon color="var(--sub-600)"/>
                </div>
              </template>
            </VueDatePicker>
          </div>
          <slot name="toolbar"/>

          <RegularButton v-if="isInViewMode" @click="switchUsers">
            <template #icon>
              <ExitIcon color="var(--sub-600)"/>
            </template>
            Admin'e geri dön

          </RegularButton>

        </div>

      </div>
      <div class="" :class="hasPadding ? 'px-8 pt-6 pb-10' :'' ">
        <slot/>
      </div>


    </div>
  </div>


</template>

<style>
.searchResultWrapper .el-card__body {
  padding: 10px !important;
}

.searchResultWrapper .el-card {
  border-radius: 8px;
}


.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  position: absolute !important;
  opacity: 0; /* Initially hidden */
  transition: opacity 0.3s ease-in-out;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.4);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background-color: transparent;
}

.custom-scrollbar.scrolling::-webkit-scrollbar {
  opacity: 1; /* Show scrollbar when scrolling */
}

</style>


<script setup>


import {computed, onMounted, onUnmounted, ref, nextTick, useSlots, onBeforeMount, onBeforeUnmount} from 'vue'
import Sidebar from '@/Layouts/Partials/Sidebar.vue';
import {SecondaryButton, IconButton, RegularButton} from '@/Components/Buttons'
import {ArrowLeftIcon, SearchIcon, NotificationIcon, CalendarIcon, ExitIcon} from '@/Components/Icons';
import AppTextInput from '@/Components/Form/AppTextInput.vue';
import {router, usePage} from '@inertiajs/vue3';
import {useUiStore} from '@/Stores/useUiStore';
import {Head} from '@inertiajs/vue3';

const choosenDate = ref();
const uiStore = useUiStore();
const emits = defineEmits(['dateChoosen'])

const props = defineProps({
  title: {type: String},
  parentTitle: {type: String},
  hasPadding: {
    default: true,
  },
  showDatePicker: {
    default: true
  },
  subParent: {
    default: null
  },
  showGoBack: {
    default: true,
  },
  showBreadCrumb: {
    default: true,
  }
})
const switchUsers = () => {
  localStorage.removeItem('account-to-switch-back');
  uiStore.isAdminViewOn = false;
  router.visit(route('control.user-management.users.switch-back-to-admin'), {
    method: 'post', data: {
      user_id: isInViewMode.value
    }
  });
}
const isInViewMode = computed(() => {
  return uiStore.isAdminViewOn;
})
const slots = useSlots()
const hasSlot = (name) => {
  return !!slots[name];
}
let params = new URLSearchParams(window.location.search)

if (params.get('e_date') && params.get('s_date')) {
  choosenDate.value = [params.get('s_date'), params.get('e_date')]
}
const onDateChaned = (e) => {
  nextTick(() => {
    emits('dateChoosen', choosenDate.value);
  })
}
const goBack = () => {
  window.history.back();
}
const onDateCleared = (e) => {

  emits('dateChoosen', null);
}

onMounted(() => {

  window.Echo.private('tenant.' + usePage().props.tenant_id + '.reportProcessed.' + usePage().props.auth.user.id)
      .listen('.reportProcessed', (e) => {
        console.log("REPORT PROCESSED Event Alındı:", e);
      })

  // SongProcessingComplete eventi için dinleyici eklemek için ürün ID'sine ihtiyacımız var
  // Eğer sayfada aktif bir ürün varsa veya ürün ID'si biliyorsak:

  // Örnek 1: Belirli bir ürüne ait dinleyici (aktif ürün ID'si varsa)
  const currentProductId = usePage().props.product?.id; // Eğer sayfada bir ürün objesi varsa
  if (currentProductId) {
    window.Echo.private('tenant.' + usePage().props.tenant_id + '.song.processing.' + currentProductId)
      .listen('.SongProcessingComplete', (e) => {
        console.log("Şarkı İşleme Tamamlandı Event Alındı:", e);
        // Burada eventın verilerine göre UI güncellemesi yapabilirsiniz
        // örn: e.success, e.file_name, e.message değerlerini kullanarak
      });
  }

  // Örnek 2: Birden fazla ürünü izlemek için (ürün listesi sayfasında)
  // const productIds = usePage().props.products?.map(product => product.id) || [];
  // productIds.forEach(productId => {
  //   window.Echo.private('tenant.' + usePage().props.tenant_id + '.song.processing.' + productId)
  //     .listen('.SongProcessingComplete', (e) => {
  //       console.log(`Ürün ${productId} için şarkı işleme tamamlandı:`, e);
  //       // Her ürün için ayrı işlemler yapabilirsiniz
  //     });
  // });

  // Örnek 3: Belirli, sabit ürün ID'lerini izlemek
  // Ürün detay sayfalarına özel bileşenler oluşturmak yerine, bu yaklaşımı kullanabilirsiniz
  // const specificProductIds = [1, 2, 3]; // İzlenmesi gereken ürün ID'leri
  // specificProductIds.forEach(productId => {
  //   window.Echo.private('tenant.' + usePage().props.tenant_id + '.song.processing.' + productId)
  //     .listen('.SongProcessingComplete', (e) => {
  //       console.log(`Ürün ${productId} için şarkı işleme tamamlandı:`, e);
  //     });
  // });
});


const isScrolling = ref(false);

const handleScroll = (event) => {
  isScrolling.value = true;
  event.target.classList.add('scrolling');

  clearTimeout(event.target.scrollTimeout);
  event.target.scrollTimeout = setTimeout(() => {
    isScrolling.value = false;
    event.target.classList.remove('scrolling');
  }, 1000); // Hide scrollbar after 1s of inactivity
};

onMounted(() => {
  const scrollableDiv = document.querySelector('.custom-scrollbar');
  if (scrollableDiv) {
    scrollableDiv.addEventListener('scroll', handleScroll);
  }
});

onUnmounted(() => {
  const scrollableDiv = document.querySelector('.custom-scrollbar');
  if (scrollableDiv) {
    scrollableDiv.removeEventListener('scroll', handleScroll);
  }
});


</script>

