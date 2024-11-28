<template>
  <div class="border border-soft-200 rounded-lg flex w-30 overflow-hidden w-min">
    <button class="bg-white  w-10 flex items-center justify-center" @click="onDecrement">
      <MinusIcon color="var(--sub-600)"/>
    </button>
    <input
      @input="onInput"
      type="text"
      :class="config?.isKeyboardOn ? 'pointer-events-auto' : 'pointer-events-none'"
      class="p-0 w-10 appIncrementerButton bg-white text-center paragragraph-sm c-strong-950 border-none focus:ring-0"
      v-model="formattedValue"
    />
    <button class="bg-white  w-10  flex items-center justify-center" @click="onIncrement">
      <AddIcon color="var(--sub-600)"/>
    </button>
  </div>
</template>

<script setup>
import {ref, computed} from 'vue'
import {MinusIcon, AddIcon} from '@/Components/Icons'

const props = defineProps({
  type: {type: String},
  modelValue: {
    type: Number,
    default: 0
  },
  config: {

  },
})

const emits = defineEmits(['update:modelValue', 'change', 'input']);

const element = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

// Format the value to display with a `%` and allow only numeric input
const formattedValue = computed({
  get: () => `%${element.value}`,
  set: (value) => {
    const numericValue = parseInt(value.replace('%', '').trim()) || 0; // Remove % and parse to number
    element.value = numericValue;
  }
})

const onDecrement = () => {
  if (element.value > 0)
    element.value -= props.config?.step ?? 1;
}
const onIncrement = () => {
  element.value += props.config?.step ?? 1;
}
const onInput = (event) => {
  const input = event.target.value.replace('%', '').trim(); // Remove % symbol
  if (/^\d*$/.test(input)) { // Ensure input is numeric
    element.value = parseInt(input) || 0;
  }
}
</script>

<style scoped>

.appIncrementerButton::-webkit-outer-spin-button,
.appIncrementerButton::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
.appIncrementerButton[type=number] {
  -moz-appearance: textfield;
}
</style>
