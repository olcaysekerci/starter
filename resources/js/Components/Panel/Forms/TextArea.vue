<template>
  <textarea
    :id="id"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :class="[
      'block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6',
      'dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400',
      error
        ? 'ring-red-300 focus:ring-red-500 dark:ring-red-500 dark:focus:ring-red-400'
        : 'ring-gray-300 focus:ring-indigo-600 dark:ring-gray-600 dark:focus:ring-indigo-500'
    ]"
    :placeholder="placeholder"
    :rows="rows"
    :disabled="disabled"
    v-bind="$attrs"
  />
  <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400">
    {{ error }}
  </p>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  id: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: ''
  },
  rows: {
    type: Number,
    default: 3
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])
</script> 