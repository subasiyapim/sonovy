<template>

     <AdminLayout :title="__('control.artist.header')" parentTitle="Tüm Plaklar" :hasPadding="false">

        <div class="bg-white-400 h-44 p-5 relative">
            <div class="">
                <h1 class="label-xl c-strong-950">ABC Plak Şirketi</h1>
                <span class="c-sub-600 paragraph-medium">4123123</span>
            </div>

            <div class="absolute rounded-full w-32 h-32 bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
                <img  class="max-w-max h-full " src="https://placehold.co/600x400" />
            </div>
            <div class="flex items-center gap-2 absolute top-5 right-5">
                <PrimaryButton>
                       <template #icon>
                                <TrashIcon color="var(--dark-green-500)" />
                        </template>
                </PrimaryButton>
                <PrimaryButton>
                       <template #icon>
                                <EditIcon color="var(--dark-green-500)" />
                        </template>
                </PrimaryButton>
            </div>
             <div class="flex items-center gap-4 absolute bottom-12 right-5">
                <AppIncrementer  :config="appIncrementerConfig" v-model="hakedis" />
            </div>
        </div>

        <div class="mt-32 flex items-start gap-8 h-full">
            <div class="px-8 flex-1 flex flex-col gap-12">
                <div>
                    <h1 class="mb-6 subheading-regular text-start">Plak Şirketi Bilgileri</h1>
                    <div class="flex items-center gap-4">
                          <div class="flex flex-col gap-8 flex-1">
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <PersonCardIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Plak Şirketi</p>
                                            <span class="label-sm c-strong-950">ABC Plak Şirketi</span>
                                    </div>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <WorldIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Ülke</p>
                                            <span class="label-sm c-strong-950">Amerika Birleşik Devletleri</span>
                                    </div>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <PhoneIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Adres</p>
                                            <span class="label-sm c-strong-950">97346 Tanner Mountain, Port Hilma 60253</span>
                                    </div>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <PercantageIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Hakediş Oranı</p>
                                            <span class="label-sm c-strong-950">%10</span>
                                    </div>
                                </div>

                          </div>
                          <div  class="flex flex-col gap-8 flex-1">
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <WorldIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Ülke</p>
                                            <span class="label-sm c-strong-950">Amerika</span>
                                    </div>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <PhoneIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Telefon</p>
                                            <span class="label-sm c-strong-950">52123123</span>
                                    </div>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <LinkIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">Websitesi</p>
                                            <span class="label-sm c-strong-950">www.muzikdagitim.com</span>
                                    </div>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                                            <MessageIcon color="var(--sub-600)" />
                                    </div>
                                    <div>
                                            <p class="paragraph-xs c-sub-600">E-mail Adresi</p>
                                            <span class="label-sm c-strong-950">info.muzikdagitim.com</span>
                                    </div>
                                </div>

                          </div>
                    </div>
                </div>

                 <div>
                    <h1 class="mb-6 subheading-regular text-start">Hakediş</h1>
                    <p class="c-sub-600 text-sm mb-5">Pellentesque felis velit molestie etiam. Arcu ac maecenas turpis venenatis. Sem aliquam velit at egestas iaculis varius at purus semper. Facilisi consequat volutpat aliquam enim ut nec. Id enim faucibus non sagittis pharetra sed. Dignissim integer proin dolor viverra elementum sagittis proin vitae curabitur. Cursus nunc feugiat libero suspendisse a odio.</p>
                     <div>
                             <AppIncrementer  :config="appIncrementerConfig" v-model="hakedis" />
                     </div>
                </div>
            </div>
            <div class="h-full bg-soft-200" style="width:1px;">
            </div>
            <div class="w-96 pr-8">
                 <h1 class="mb-6 subheading-regular">Şirketin Albümleri</h1>

                 <div v-for="i in 10" class="flex p-4">
                    <div class="flex-1 flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg overflow-hidden">
                            <img src="https://placehold.co/400x400"  />
                        </div>
                        <div>
                            <p class="text-sm c-strong-950">Parça Adı</p>
                            <span class="paragraph-xs c-blue-500">Albüm Adı</span>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                       <div class="h-3.5">
                         <PlayFilledIcon color="var(--strong-950)" />
                       </div>
                        <span class="paragraph-xs c-neutral-500">
                            02:35
                        </span>
                    </div>
                 </div>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {PersonCardIcon,PercantageIcon,MessageIcon,PhoneIcon,WorldIcon,TrashIcon,EditIcon,SpotifyIcon,GenreIcon,PlayFilledIcon,LinkIcon} from '@/Components/Icons'



import {PrimaryButton} from '@/Components/Buttons'
import {AppIncrementer} from '@/Components/Form'
import {ref} from 'vue';

const hakedis = ref(0);

const appIncrementerConfig = {

    formatter: (value) => {
        return '%'+value;
    }
};
</script>

<style lang="scss" scoped>

</style>
