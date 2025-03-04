<script setup>
import { ref, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import { AppLoadingIcon, CheckIcon } from "@/Components/Icons";

// Define the number of columns
const COLUMN_COUNT = 26;
const MIN_SQUARES = 5;
const MAX_SQUARES = 12;
const SQUARE_SIZE = 30; // Each square is 30px in height

const state = ref()
// Store the bars (columns with squares)
const bars = ref([]);

// Function to get a random number of squares
function randomNumberOfSquares() {
  return Math.floor(Math.random() * (MAX_SQUARES - MIN_SQUARES + 1) + MIN_SQUARES);
}

// Function to initialize the bars
function initializeBars() {
  bars.value = Array.from({ length: COLUMN_COUNT }, () =>
    Array.from({ length: randomNumberOfSquares() }, () => SQUARE_SIZE)
  );
}

// Function to update bars smoothly
function updateBars() {
  bars.value.forEach((column, index) => {
    const newLength = randomNumberOfSquares();
    const currentLength = column.length;

    if (newLength > currentLength) {
      // Add new squares at the top gradually
      for (let i = currentLength; i < newLength; i++) {
        setTimeout(() => {
          bars.value[index].unshift(SQUARE_SIZE); // Add to the top
        }, (i - currentLength) * 100);
      }
    } else if (newLength < currentLength) {
      // Remove squares from the top gradually
      for (let i = currentLength; i > newLength; i--) {
        setTimeout(() => {
          bars.value[index].shift(); // Remove from the top
        }, (currentLength - i) * 100);
      }
    }
  });

  // Schedule the next update
  setTimeout(() => requestAnimationFrame(updateBars), 100); // Slower, 1.5s cycle
}

// Initialize on mount
onMounted(() => {
  initializeBars();
  updateBars();
});
</script>

<template>
  <div class="flex min-h-screen flex-col items-center pt-10 sm:justify-center sm:pt-0 bg-dark-green-800">
    <div v-if="!state" class="z-10 w-full overflow-hidden bg-white px-10 py-8 sm:max-w-md rounded-2xl shadow-md">
      <div class="mx-auto bg-white-600 w-16 h-16 rounded-full flex items-center justify-center my-6">
        <Link href="/">
          <ApplicationLogo class="h-10 w-10 fill-current text-gray-500" />
        </Link>
      </div>
      <slot />
    </div>

    <div v-if="state == 'loading'" class="z-10 w-full overflow-hidden bg-white px-10 py-20 flex flex-col gap-2 items-center justify-center sm:max-w-md rounded-2xl shadow-md">
      <div class="mx-auto bg-white-600 w-16 h-16 rounded-full flex items-center justify-center my-6">
        <AppLoadingIcon width="32" color="var(--dark-green-600)" />
      </div>
      <slot name="loading" />
    </div>

    <div v-if="state == 'completed'" class="z-10 w-full overflow-hidden bg-white px-10 py-20 flex flex-col gap-2 items-center justify-center sm:max-w-md rounded-2xl shadow-md">
      <div class="mx-auto bg-dark-green-800 w-16 h-16 rounded-full flex items-center justify-center my-6">
        <CheckIcon class="w-6 h-6" color="var(--dark-green-500)" />
      </div>
      <slot name="completed" />
    </div>

    <!-- Optimized Animated Sound Display -->
    <div class="flex h-full z-1 absolute bottom-0 right-0 left-0 gap-2">
      <div v-for="(column, i) in bars" :key="i" class="h-full flex-1 flex opacity-50 flex-col gap-2 justify-end">
        <transition-group name="fade" tag="div">
          <div v-for="(height, j) in column" :key="j"
            class="bg-dark-green-600 w-full my-2 rounded  transition-all duration-100"
            :style="{ height: `${height}px` }"
          ></div>
        </transition-group>
      </div>
    </div>
  </div>
</template>

<style>

</style>
