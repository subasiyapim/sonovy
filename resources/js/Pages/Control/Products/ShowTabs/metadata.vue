<script setup>
const props = defineProps({
    product:{}
});
import {
  DocumentIcon,
  PlayFilledIcon,
} from '@/Components/Icons'
</script>

<template>
     <div class="flex items-start gap-8 h-full">

      <div class="px-8 flex-1 flex flex-col gap-12">

        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.product.show.metadata')"/>
          <div class="flex items-start gap-4">
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.album_name')"/>
                  <span class="label-sm c-strong-950" v-text="product.album_name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.version')"/>
                  <span class="label-sm c-strong-950" v-text="product.version"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.genres')"/>
                  <span class="label-sm c-strong-950" v-text="product.genres"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.sub_genres')"/>
                  <span class="label-sm c-strong-950" v-text="product.sub_genres"/>
                </div>
              </div>

              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.format')"/>
                  <span class="label-sm c-strong-950" v-text="product.format"/>
                </div>
              </div>
                <div class="flex gap-3.5 items-center">
                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                        <DocumentIcon color="var(--sub-600)"/>
                    </div>
                    <div>
                        <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.type')"/>
                        <span class="label-sm c-strong-950" v-text="product.type"/>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.p_line')"/>
                  <span class="label-sm c-strong-950" v-text="product.p_line"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.c_line')"/>
                  <span class="label-sm c-strong-950" v-text="product.c_line"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.upc/ean_code')"/>
                  <span class="label-sm c-strong-950" v-text="product.upc_code"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.catalog_number')"/>
                  <span class="label-sm c-strong-950" v-text="product.catalog_number"/>
                </div>
              </div>

               <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.language_id')"/>
                  <span class="label-sm c-strong-950" v-text="product.language_id"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.iswc')"/>
                  <span class="label-sm c-strong-950" v-text="product.iswc"/>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="hidden">
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.product.fields.artist_branches')"/>
          <div class="flex flex-wrap gap-3">
            <div v-for="(branch, index) in product.artist_branches"
                 class="border border-soft-200 bg-white px-3 flex items-center py-1 rounded">
              <span class="label-xs c-sub-600">
                {{ index + 1 + ' ' + branch.name }}
              </span>
            </div>
          </div>
        </div>
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.product.show.tags')"/>
          <div class="flex flex-col flex-wrap gap-2 max-h-56 w-min">
            <BasicBadge v-for="(tag,i) in product.tags" class="flex gap-2 items-center w-48" :key="i">

              <span class="c-strong-950 label-sm" v-text="tag"/>

            </BasicBadge>
          </div>
        </div>

      </div>
      <div class="h-full bg-soft-200" style="width:1px;">
      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular" v-text="__('control.product.artist_albums')"/>
        <template v-if="false">
          <div v-for="product in product.products" class="flex p-4">
            <div class="flex-1 flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg overflow-hidden">
                <img src="https://placehold.co/400x400"/>
              </div>
              <div>
                <p class="text-sm c-strong-950">Parça Adı</p>
                <span class="paragraph-xs c-blue-500">Albüm Adı</span>
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
          <p v-text="__('control.product.album_notfound')"/>
        </template>
      </div>

    </div>
</template>

<style scoped>

</style>
