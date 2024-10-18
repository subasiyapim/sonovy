<template>

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
</template>

<script setup>
import {computed} from 'vue';
import {FormElement} from '@/Components/Form';
import {AddIcon} from '@/Components/Icons'
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
