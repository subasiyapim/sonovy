<template>

  <AdminLayout :showDatePicker="false" :title="__('control.label.show_header')"
               :parentTitle="__('control.label.title_singular')"
               :hasPadding="false">

    <div class="bg-white-400 h-44 p-5 relative">
      <div class="">
        <h1 class="label-xl c-strong-950" v-text="label.name"/>
        <span class="c-sub-600 paragraph-medium" v-text="label.id"/>
      </div>

      <div
          class="absolute rounded-full w-32 h-32 bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="label.name"
             :src="label.image ? label.image.thumb : defaultStore.profileImage(label.name)">
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <PrimaryButton @click="remove">
          <template #icon>
            <TrashIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
        <PrimaryButton @click="isModalOn = true">
          <template #icon>
            <EditIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
      </div>
    </div>

    <div class="flex items-center gap-2 mt-32 mb-5 ms-6">
        <button @click="changeActiveTab('label_info')" class="py-1.5 px-3 rounded-full label-xs" :class="activeTab == 'label_info' ? 'bg-dark-green-800 text-white' : 'bg-weak-50 c-sub-600' ">Plak Şirket Bilgileri</button>
        <button @click="changeActiveTab('products')" class="py-1.5 px-3 rounded-full label-xs" :class="activeTab == 'products' ? 'bg-dark-green-800 text-white' : 'bg-weak-50 c-sub-600' ">Yayınlar</button>
        <button @click="changeActiveTab('dsp')" class="py-1.5 px-3 rounded-full label-xs" :class="activeTab == 'dsp' ? 'bg-dark-green-800 text-white' : 'bg-weak-50 c-sub-600' "> DSP Yetkileri</button>
    </div>
    <div v-if="activeTab == 'label_info'" class=" flex items-start gap-8 h-full">

      <div class="px-8 flex-1 flex flex-col gap-12">
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.label.label_info')"/>
          <div class="flex items-start gap-4">
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PersonCardIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.title_singular')"/>
                  <span class="label-sm c-strong-950" v-text="label.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <WorldIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.country_id')"/>
                  <span class="label-sm c-strong-950" v-text="label.country.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PhoneIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.address')"/>
                  <span class="label-sm c-strong-950" v-text="label.address"/>
                </div>
              </div>

            </div>
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PhoneIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.phone')"/>
                  <span class="label-sm c-strong-950" v-text="label.phone"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <LinkIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.web')"/>
                  <span class="label-sm c-strong-950" v-text="label.web"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <MessageIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.email')"/>
                  <span class="label-sm c-strong-950" v-text="label.email"/>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
      <div class="h-full bg-soft-200" style="width:1px;">
      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular" v-text="__('control.label.label_albums')"/>

        <template v-if="label.products.length > 0">
          <div v-for="product in label.products" class="flex p-4">
            <div class="flex-1 flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg overflow-hidden">
                <img src="https://placehold.co/400x400"/>
              </div>
              <div>
                <p class="text-sm c-strong-950">{{ __('control.label.song_name') }}</p>
                <span class="paragraph-xs c-blue-500">{{ __('control.label.album_name') }}</span>
              </div>
            </div>
            <div class="flex items-end gap-2">
              <div class="h-3.5">
                <PlayFilledIcon color="var(--strong-950)"/>
              </div>
              <span class="paragraph-xs c-neutral-500">02:35</span>
            </div>
          </div>
        </template>
        <template v-else>
          <p v-text="__('control.label.album_notfound')"/>
        </template>
      </div>
    </div>
    <div v-else-if="activeTab == 'products'" class="px-6">
        <AppTable v-model="tab.products"  :isClient="true" :hasSearch="false" :showAddButton="false">
            <AppTableColumn label="Tür" sortable="type">
                <template #default="scope">
                <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">

                    <tippy :interactive="true" theme="dark" :appendTo="getBody">
                            <AudioIcon v-if="scope.row.type == 1" color="var(--sub-600)"/>
                        <MusicVideoIcon v-if="scope.row.type == 2" color="var(--sub-600)"/>
                            <MusicVideoIcon v-if="scope.row.type == 4" color="var(--sub-600)"/>
                            <RingtoneIcon v-if="scope.row.type == 3" color="var(--sub-600)"/>
                    <template #content>
                        <p v-if="scope.row.type == 1">
                            {{ __('control.song.audio') }}
                        </p>
                        <p v-if="scope.row.type == 2">
                            {{ __('control.song.music_video') }}
                        </p>
                        <p v-if="scope.row.type == 3">
                            {{ __('control.song.ringtone') }}
                        </p>
                        <p v-if="scope.row.type == 4">
                            {{ __('control.song.apple_video') }}
                        </p>

                    </template>
                    </tippy>


                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Yayın Bilgisi" width="250">
                <template #default="scope">
                <div class="flex gap-x-2 items-start">
                    <div class="w-8 h-8 rounded overflow-hidden">
                    <img class="w-10 h-10" alt=""
                        :src="scope.row.image ? scope.row.image.thumb : 'https://loremflickr.com/400/400'">

                    <img :alt="scope.row.album_name"
                        :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.album_name)"
                    >

                    </div>
                    <div class="flex flex-col flex-1 items-start justisy-start">
                    <a :href="route('control.catalog.products.show',scope.row.id)" class="paragraph-xs c-blue-500">
                        {{ scope.row.album_name }}
                    </a>

                    <div class=" paragraph-xs c-strong-950 ">
                        <p>
                        <template v-for="(artist,artistIndex) in scope.row.main_artists">
                            {{ artist.name }}
                            <template v-if="artistIndex != scope.row.main_artists.length-1">,&nbsp;</template>
                        </template>
                        </p>

                    </div>
                    </div>
                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Yayın Tarih">
                <template #default="scope">
                <div v-if="scope.row.physical_release_date" class="flex items-center gap-3">
                    <p class="paragraph-xs c-sub-600 whitespace-nowrap">
                    {{ scope.row.physical_release_date }}
                    </p>
                </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="UPC/Katalog" width="240">
                <template #default="scope">
                    <div class="flex flex-col justify-start ">
                        <span class="paragraph-xs c-sub-600">UPC:{{ scope.row.upc_code ?? 'Boş' }}</span>
                        <span class="paragraph-xs c-sub-600">Katalog Numarası: {{ scope.row.catalog_number ?? 'Boş' }}</span>
                    </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Tür">
                <template #default="scope">
                    <span class="paragraph-xs c-sub-600">

                     <template v-if="scope.row.type == 1">
                        {{ __('control.song.audio') }}
                    </template>
                    <template v-if="scope.row.type == 2">
                        {{ __('control.song.music_video') }}
                    </template>
                    <template v-if="scope.row.type == 3">
                        {{ __('control.song.ringtone') }}
                    </template>
                    <template v-if="scope.row.type == 4">
                        {{ __('control.song.apple_video') }}
                    </template>
                    </span>

                </template>
            </AppTableColumn>
            <AppTableColumn label="Format">
                <template #default="scope">
                    <span class="paragraph-xs c-sub-600">{{ scope.row.format }}</span>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Parçalar">
                <template #default="scope">
                    <span class="paragraph-xs c-sub-600">{{ scope.row.songs?.length }} Parça</span>
                </template>
            </AppTableColumn>
        </AppTable>
    </div>
    <div v-else-if="activeTab == 'dsp'" class="px-6">

        <template v-if="checkIfUserHasAdminRole">
            <AppTable :hasSelect="true" @selectionChange="onDspSelect" v-model="tab.dsp"  :isClient="true" :hasSearch="false" :showAddButton="false">
                <template #tableHeader>
                    <div v-if="choosenDspIds.length > 0" class="w-full flex justify-end gap-2">
                        <RegularButton @click="onStatusChange(2,choosenDspIds)" >
                            <template #icon>
                                <CheckIcon :color="'var(--sub-600)'" />
                            </template>
                            Tümünü Onayla
                        </RegularButton>
                        <RegularButton @click="onStatusChange(3,choosenDspIds)">
                            <template #icon>
                                <CloseIcon color="var(--sub-600)" />
                            </template>
                            Tümünü Reddet
                        </RegularButton>
                    </div>
                </template>
                <AppTableColumn label="Kanal Bilgisi">
                    <template #default="scope">
                    <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                                <Icon :icon="scope.row.platform.icon" />
                            </div>
                        <p class="label-sm c-strong-950"> {{scope.row.platform.name}}</p>
                    </div>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Talep">
                    <template #default="scope">
                    <p class="paragraph-xs c-strong-950"> {{moment(scope.row.created_at).fromNow()}}</p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Durum">
                    <template #default="scope">
                    <div class="border border-soft-200 rounded-lg flex items-center gap-2 px-2 py-1">
                        <CheckFilledIcon color="var(--blue-500)"  v-if="scope.row.status == 2" />
                        <PendingIcon  color="#FF8447" v-if="scope.row.status == 1" />
                        <WarningIcon  color="var(--error-500)" v-if="scope.row.status == 3" />
                        <span class="subheading-xs c-sub-600">{{scope.row.status_text}}</span>
                    </div>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Askiyon" align="right">
                    <template #default="scope">
                        <div class="flex items-center gap-2">
                            <RegularButton @click="onStatusChange(2,[scope.row.id])" :disabled="scope.row.status == 2">
                                <template #icon>
                                    <CheckIcon :color="scope.row.status == 2 ? '#CACFD8':'   var(--sub-600)'" />
                                </template>
                                Onayla
                            </RegularButton>
                            <RegularButton @click="onStatusChange(3,[scope.row.id])" :disabled="scope.row.status == 3">
                                <template #icon>
                                    <CloseIcon :color="scope.row.status == 3 ? '#CACFD8':'   var(--sub-600)'" />
                                </template>
                                Reddet
                            </RegularButton>
                        </div>

                    </template>
                </AppTableColumn>

            </AppTable>
        </template>
        <template v-if="checkIfUserHasUserRole">
            <AppTable  @selectionChange="onDspSelect" v-model="tab.platforms"  :isClient="true" :hasSearch="false" :showAddButton="false">

                <AppTableColumn label="Kanal Bilgisi">
                    <template #default="scope">

                        <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                                    <Icon :icon="scope.row.icon" />
                                </div>
                            <p class="label-sm c-strong-950"> {{scope.row.name}}</p>
                        </div>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Talep">
                    <template #default="scope">
                    <p v-if="tab.dsp.find((el) => el.platform_id == scope.row.id)" class="paragraph-xs c-strong-950"> {{moment(tab.dsp.find((el) => el.platform_id == scope.row.id)?.created_at).fromNow()}}</p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Durum">
                    <template #default="scope">

                        <div v-if="tab.dsp.find((el) => el.platform_id == scope.row.id)" class="border border-soft-200 rounded-lg flex items-center gap-2 px-2 py-1">
                            <CheckFilledIcon color="var(--blue-500)"  v-if="tab.dsp.find((el) => el.platform_id == scope.row.id)?.status == 2" />
                            <PendingIcon  color="#FF8447" v-if="tab.dsp.find((el) => el.platform_id == scope.row.id)?.status == 1" />
                            <WarningIcon  color="var(--error-500)" v-if="tab.dsp.find((el) => el.platform_id == scope.row.id)?.status == 3" />
                            <span class="subheading-xs c-sub-600">{{tab.dsp.find((el) => el.platform_id == scope.row.id)?.status_text}}</span>
                        </div>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Askiyon" align="right">
                    <template #default="scope">
                        <div class="flex items-center gap-2">
                            <RegularButton :disabled="tab.dsp.find((el) => el.platform_id == scope.row.id)" @click="createDemandForDsp([scope.row.id])" >
                                <template #icon>
                                    <CheckIcon :color="tab.dsp.find((el) => el.platform_id == scope.row.id) ? '#CACFD8':'   var(--sub-600)'" />
                                </template>
                                Talep Oluştur
                            </RegularButton>

                        </div>

                    </template>
                </AppTableColumn>

            </AppTable>
        </template>

    </div>
    <LabelDialog :label="label" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {useDefaultStore} from '@/Stores/default';
import {useCrudStore} from '@/Stores/useCrudStore';

import AdminLayout from '@/Layouts/AdminLayout.vue';
const defaultStore = useDefaultStore();
import moment from 'moment';
import {router} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import { usePage} from '@inertiajs/vue3';


import {
    PersonCardIcon,
    PercantageIcon,
    MessageIcon,
    PhoneIcon,
    CheckFilledIcon,
    PendingIcon,
    WarningIcon,
    WorldIcon,
    TrashIcon,
    EditIcon,
    SpotifyIcon,
    GenreIcon,
    PlayFilledIcon,
    LinkIcon,
    CheckIcon,
    CloseIcon,
    Icon,
    AudioIcon,
    MusicVideoIcon,
    RingtoneIcon
} from '@/Components/Icons'

const crudStore = useCrudStore();
const isModalOn = ref(false);
import {PrimaryButton,RegularButton} from '@/Components/Buttons'
import {AppIncrementer} from '@/Components/Form'
import {ref,computed} from 'vue';
import {LabelDialog} from '@/Components/Dialog';

const props = defineProps({
  label: {
    type: Object,
    required: true
  },
  tab:{

  }
})

let params = new URLSearchParams(window.location.search)
const choosenDspIds = ref([])
const activeTab = ref(params.get('slug') ?? 'label_info')
const onDone = () => {
  isModalOn.value = false;
}

const products = ref([]);
const dspData = ref([]);
const changeActiveTab = (e) => {
      router.visit(route(route().current(),props.label.id), {
        data: {
            slug:e,
        },
        preserveScroll: true,
    });
}


const appIncrementerConfig = {
  formatter: (value) => {
    return '%' + value;
  }
};

const checkIfUserHasAdminRole = computed(() => {
    return usePage().props.auth.roles.find((el) => el.code == 'admin')
})
const checkIfUserHasUserRole = computed(() => {
    return usePage().props.auth.roles.find((el) => el.code == 'user')
})

const createDemandForDsp = async (platformArray) => {

    try {
        const response = await crudStore.post( route('control.catalog.label.dsp.create',props.label.id) , {platforms:platformArray});
        toast.success("İşlem başarılı");
        props.tab.dsp = response.dsp;
    } catch (error) {

    }
}

const remove = () => {
  router.delete(route('control.catalog.labels.destroy', props.label.id), {});
}

    const getBody = computed(() => {
        return document.querySelector('body');
    });


   const onStatusChange = async (newStatus,ids) => {
        // Make the request payload
        const payload = {
            status: newStatus,
            dsp_ids: ids,
        };

        try {
            // Call your store's post method to change the status of dsp labels
            const response = await crudStore.post( route('control.catalog.label.dsp.status',props.label.id) , payload);
            console.log('Status changed successfully:', response);

            // Optionally, you can update the local status after the API call
            // props.scope.row.status = newStatus;
            toast.success("İşlem başarılı");
            props.tab.dsp.forEach(element => {
                 if(ids.includes(element.id) ){
                    element.status = newStatus
                    if(newStatus == 2){
                        element.status_text = 'Onaylandı';
                    }else if(newStatus == 3){
                        element.status_text = 'Reddedildi';

                    }
                 }
            });
            // You can also show a success message here
        } catch (error) {
            console.error('Error changing status:', error);
            // Handle error, possibly show an error message to the user
        }
    };

    const onDspSelect = (e) => {
        console.log("EEE",e);
    choosenDspIds.value = e.map((el) => el.id);
    }
</script>

<style lang="scss" scoped>

</style>
