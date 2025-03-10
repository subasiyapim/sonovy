<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      d="M10 3H14V5H10V3ZM12 22C6.49 22 2 17.51 2 12C2 6.49 6.49 2 12 2C12.34 2 12.67 2.03 13 2.07V7.2C12.7 7.07 12.36 7 12 7C9.24 7 7 9.24 7 12C7 14.76 9.24 17 12 17C14.76 17 17 14.76 17 12C17 11.64 16.93 11.3 16.8 11H22C22.37 17.26 17.82 22 12 22ZM19 11H13V5H14.5V9.5H19V11Z"
      :fill="color"
    />
  </svg>
</template>

<script setup>
defineProps({
  size: {
    type: String,
    default: '24',
  },
  color: {
    type: String,
    default: 'currentColor',
  },
});
</script>
