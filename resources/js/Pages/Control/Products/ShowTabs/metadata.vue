<script setup>
import {BasicBadge} from "@/Components/Badges/index.js";

const props = defineProps({
  product: {}
});
import {
  DocumentIcon,
  PlayFilledIcon,
  CopyIcon,
} from '@/Components/Icons'
import {
  IconButton
} from '@/Components/Buttons'

import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();
</script>

<template>
  <div class="flex items-start gap-8 h-full">

    <div class="flex-1 flex flex-col gap-12">

      <div>
        <div class="flex items-center gap-2 mb-6">
          <h1 class=" subheading-regular text-start" v-text="__('control.product.show.metadata')"/>

          <IconButton>
            <CopyIcon color="var(--sub-600)"/>
          </IconButton>
        </div>
        <div class="flex items-start gap-4">
          <div class="flex flex-col gap-8 flex-1">
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.album_name')"/>
                <span class="label-sm c-strong-950">
                    {{product.metadata.album_name}}
                    <template v-if="product.metadata">({{product.metadata.version ?? '-'}})</template>
                </span>
              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.version')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.version"/>
              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.genres')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.genres"/>
              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.sub_genres')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.sub_genres"/>

              </div>
            </div>

            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.format')"/>
                <span class="label-sm c-strong-950"
                      v-text="product.metadata.format"/>

              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.type')"/>
                <span class="label-sm c-strong-950">
                    {{product.metadata?.type}}
                </span>
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
                <span class="label-sm c-strong-950" v-text="product.metadata.p_line"/>
              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.c_line')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.c_line"/>
              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.upc/ean_code')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.upc_code"/>
              </div>
            </div>
            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.catalog_number')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.catalog_number"/>
              </div>
            </div>

            <div class="flex gap-3.5 items-center">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.language_id')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.language"/>
              </div>
            </div>
            <div class="flex gap-3.5 items-center hidden">
              <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                <DocumentIcon color="var(--sub-600)"/>
              </div>
              <div>
                <p class="paragraph-xs c-sub-600" v-text="__('control.product.show.iswc')"/>
                <span class="label-sm c-strong-950" v-text="product.metadata.iswc"/>
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
      <h1 class="mb-6 subheading-regular" v-text="__('control.product.show.artist_albums')"/>
      <template v-if="product.metadata.artists">
        <div v-for="artist in product.metadata.artists" class="flex p-1">
          <div class="flex-1 flex items-center gap-4">
            <div class="w-8 h-8 rounded-lg overflow-hidden">
              <img :alt="artist.name"
                   :src="artist.image ? artist.image.thumb : defaultStore.profileImage(artist.name)"
              >
            </div>
            <div>
              <span class="paragraph-xs">{{ artist.pivot.is_main ? 'Ana sanatçı' : 'Düet' }}</span>
              <p class="text-sm c-strong-950">{{ artist.name }}</p>
            </div>
          </div>
        </div>
      </template>
      <template v-else>
        <p v-text="__('control.product.show.artist_albums_not_fount')"/>
      </template>
      <h1 class="my-6 subheading-regular" v-text="__('control.product.show.musicians')"/>
      <template v-if="product.metadata.participants">
        <div v-for="participant in product.metadata.participants" class="flex p-1">
          <div class="flex-1 flex items-center gap-4">
            <div class="w-8 h-8 rounded-lg overflow-hidden">
              <img src="https://placehold.co/400x400"/>
            </div>
            <div>
              <span class="paragraph-xs">{{ participant.branch_names }}</span>
              <p class="text-sm c-strong-950">{{ participant.user.name }}</p>
            </div>
          </div>
          <div class="flex items-end gap-2">
            <span class="paragraph-xs c-neutral-500">% {{ participant.rate }}</span>
          </div>
        </div>
      </template>
      <template v-else>
        <p v-text="__('control.product.show.artist_albums_not_fount')"/>
      </template>
    </div>

  </div>
</template>

<style scoped>

</style>
