<template>

    <div class="flex h-screen overflow-hidden">
            <div class="flex-1 flex flex-col">
                <div class="flex-1 relative   ">
                    <div class="flex items-center staticTopInfo h-20">
                        <div class="flex items-center gap-3.5 flex-1 ">

                            <IconButton hasBorder size="medium">
                                    <ArrowLeftIcon color="var(--sub-600)" />
                            </IconButton>
                            <div class="flex flex-col flex-1">
                                    <p class="label-lg c-strong-950">Tüm Yayınlar</p>
                                    <div class="flex items-center gap-2">
                                        <span class="label-xs c-soft-400">Katalog</span>
                                        <span class="label-xs c-soft-400">.</span>
                                        <span class="label-xs c-soft-400">Tüm Yayınlar</span>
                                    </div>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="w-full h-full bg-white-500 flex flex-col gap-10 p-8 overflow-hidden">
                    <div class="w-full flex items-center gap-4">
                        <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-dark-green-500 flex items-center justify-center label-xs text-white"> 1</div>
                                <span class="paragpraph-sm c-strong-950">Yayın bilgileri</span>
                        </div>
                        <ChevronRightIcon color="var(--soft-400)" />
                          <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center label-xs c-sub-600 border border-soft-200"> 2</div>
                                <span class="paragpraph-sm c-sub-600">Şarkı Detay</span>
                        </div>
                        <ChevronRightIcon color="var(--soft-400)" />
                          <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center label-sm c-sub-600 border border-soft-200"> 3</div>
                                <span class="paragpraph-sm c-sub-600">Yayınlama Detayları</span>
                        </div>
                        <ChevronRightIcon color="var(--soft-400)" />
                          <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center label-sm c-sub-600 border border-soft-200"> 4</div>
                                <span class="paragpraph-sm c-sub-600">Pazarlama ve Onay</span>
                        </div>
                        <ChevronRightIcon color="var(--soft-400)" />
                          <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center label-sm c-sub-600 border border-soft-200"> 5</div>
                                <span class="paragpraph-sm c-sub-600">Özet</span>
                        </div>

                    </div>
                    <div class="h-full bg-white w-full shadow rounded-xl px-8 py-8 overflow-scroll">
                         <div class="pb-6">
                                <p class="label-xl c-strong-950">
                                    İlk adım, yayın bilgilerinizi dolduralım.✍🏻
                                </p>
                         </div>
                         <div class="flex gap-10">
                            <div class="flex-1 flex flex-col overflow-scroll gap-6">
                                <FormElement label-width="190px"  label="Albüm Adı" ></FormElement>
                                <FormElement label-width="190px" label="Sürüm" placeholder="Lütfen giriniz"></FormElement>
                                <FormElement label-width="190px" type="select" label="Sanatçı" placeholder="Sanatçı Seçiniz" :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                     <template #description>
                                       <label class="flex items-center gap-2 mt-2 px-1" for="isProductDerleme">
                                        <input type="checkbox" id="isProductDerleme" class="focus:ring-0 rounded"></input>
                                        <span class="paragraph-xs c-strong-950">Derleme Albüm</span>
                                        <span class="paragraph-xs c-sub-600">(birden fazla sanatçı varsa seçin)</span>
                                       </label>
                                    </template>

                                </FormElement>
                                <FormElement label-width="190px" type="select" label="Düet Sanatçı" placeholder="Seçiniz"  :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>
                                <FormElement label-width="190px" label="Tarz" placeholder="Seçiniz"  type="select" :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>
                                <FormElement label-width="190px" label="Alt Tarz" placeholder="Seçiniz"  type="select" :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>
                                <FormElement label-width="190px" label="Biçim" placeholder="Seçiniz"  type="select" :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>

                            </div>

                            <div class="flex-1 flex flex-col overflow-scroll gap-6">
                                 <FormElement type="select" label-width="190px"  label="Plak şirketi" placeholder="Şirket Seçiniz" :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>
                                <FormElement label-width="190px"  label="p Satırı" placeholder="Lütfen giriniz">
                                    <template #tooltip>
                                            Besteler, şarkı sözleri ve diğer müzikal öğeler, eser sahiplerine eserlerini kullanma, kopyalama, dağıtma, düzenleme ve ticari olarak değerlendirme yetkisi verir.
                                    </template>
                                </FormElement>
                                <FormElement label-width="190px"  label="C Satırı" placeholder="Lütfen giriniz"></FormElement>
                                <FormElement label-width="190px"  label="UPC/EAN Kodu" placeholder="Lütfen giriniz"></FormElement>
                                <FormElement label-width="190px"  label="Katalog No" placeholder="Lütfen giriniz"></FormElement>
                                <FormElement type="select" label-width="190px"  label="Albüm Dili" placeholder="Lütfen giriniz"> :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>

                                <FormElement type="select" label-width="190px" label="Ana Fiyat" placeholder="Lütfen giriniz" :config="selectConfig">
                                    <template #first_child>
                                       <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                    <template #empty>
                                       <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                                    </template>
                                </FormElement>
                            </div>

                         </div>
                    </div>
                </div>
            </div>
            <div class="w-80 border-l border-soft-200 h-full flex flex-col">
                <div class="flex items-center gap-3.5 px-5 py-4 h-20 border-b  border-soft-200">
                    <div class="w-12 h-12 bg-dark-green-800 border border-soft-200 rounded-full flex items-center justify-center">
                            <StickyNoteFilledIcon color="var(--dark-green-500)" />
                    </div>
                        <h1 class="label-lg c-strong-950">Yayın özeti</h1>


                </div>
                <div class="p-6 flex flex-col h-full gap-5 overflow-scroll">
                    <AppUploadInput label="Albüm Kapağı" note="JPEG, PNG, PDF, and MP4 formats, up to 50 MB."></AppUploadInput>
                    <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <BroadcastTitleIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Albüm Adı</p>
                            <span class="label-sm c-strong-950">Geleceğe Doğru</span>
                       </div>
                    </div>

                     <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <PersonIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Sanatçılar</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>
                    </div>
                     <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <PersonIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Düet</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>
                    </div>

                     <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <GenreIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Tarz</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>
                    </div>



                    <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <CalendarIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Yayın Tarihi</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>


                    </div>



                    <div class="flex gap-3.5 items-center">
                       <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                            <DurationIcon color="var(--sub-600)" />
                       </div>
                       <div>
                            <p class="paragraph-xs c-sub-600">Toplam Süre</p>
                            <span class="label-sm c-strong-950">-</span>
                       </div>


                    </div>

                </div>
            </div>
    </div>


</template>

<script setup>
import {computed} from 'vue';
import {IconButton} from '@/Components/Buttons'
import {FormElement} from '@/Components/Form';
import {AppUploadInput} from '@/Components/Form';
import {ArrowLeftIcon,SearchIcon,ChevronRightIcon,StickyNoteFilledIcon,AddIcon,BroadcastTitleIcon,PersonIcon,GenreIcon,CalendarIcon, DurationIcon} from '@/Components/Icons';


const selectConfig = computed(() => {
    return {
        hasSearch:true,
        data: [
            {value:1,label:"MERHABA"},
            {value:2,label:"MERHABA"},
        ]
    }
})
</script>

<style lang="scss" scoped>

</style>
