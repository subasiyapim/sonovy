<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link} from '@inertiajs/vue3';
import {useSlots} from 'vue';
import {AppLoadingIcon, CheckIcon} from '@/Components/Icons';

interface Props {
  state?: 'loading' | 'completed' | null;
}

type SlotNames = 'icon' | 'default' | 'loading' | 'completed';

const props = defineProps<Props>();
const slots = useSlots();

const hasSlot = (name: SlotNames): boolean => {
  return !!slots[name];
};

function randomIntFromInterval(min: number, max: number): number {
  return Math.floor(Math.random() * (max - min + 1) + min);
}
</script>

<template>
  <div class="flex min-h-screen flex-col items-center  pt-10 sm:justify-center sm:pt-0 bg-dark-green-800">


    <div v-if="!props.state"
         class="z-10  w-full overflow-hidden bg-white px-10 py-8 sm:max-w-md rounded-2xl bg-white shadow-md">


      <div class="mx-auto bg-white-600 w-16 h-16 rounded-full flex items-center justify-center my-6">
        <Link href="/">
          <slot v-if="hasSlot('icon')" name="icon"/>
          <ApplicationLogo v-else class="h-10 w-10 fill-current text-gray-500"/>
        </Link>
      </div>

      <slot/>
    </div>

    <div v-if="props.state == 'loading'"
         class="z-10  w-full overflow-hidden bg-white px-10 py-20 flex flex-col gap-2 items-center justify-center sm:max-w-md rounded-2xl shadow-md">
      <div class="mx-auto bg-white-600 w-16 h-16 rounded-full flex items-center justify-center my-6">
        <AppLoadingIcon width="32" color="var(--dark-green-600)"/>
      </div>
      <slot name="loading"/>
    </div>
    <div v-if="props.state == 'completed'"
         class="z-10  w-full overflow-hidden bg-white px-10 py-20 flex flex-col gap-2 items-center justify-center sm:max-w-md rounded-2xl shadow-md">
      <div class="mx-auto bg-dark-green-800 w-16 h-16 rounded-full flex items-center justify-center my-6">
        <CheckIcon class="w-6 h-6" color="var(--dark-green-500)"/>
      </div>
      <slot name="completed"/>
    </div>
    <div class="flex h-full z-1 absolute bottom-0 right-0 left-0 gap-2">
      <div v-for="i in 26" class=" h-full flex-1 flex flex-col gap-2 justify-end">
        <div v-for="j in randomIntFromInterval(8,12)" class="bg-dark-green-600 w-full h-6 rounded opacity-50">
        </div>
      </div>
    </div>
  </div>
</template>
