<template>
  <button
    :type="type"
    :class="[
      'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition-colors duration-150',
      variant === 'primary' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : '',
      variant === 'secondary' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : '',
      variant === 'danger' ? 'bg-red-600 text-white hover:bg-red-700' : '',
      variant === 'success' ? 'bg-green-600 text-white hover:bg-green-700' : '',
      disabled ? 'opacity-50 cursor-not-allowed' : ''
    ]"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <slot name="icon" />
    <span v-if="$slots.default" class="ml-2"><slot /></span>
  </button>
</template>
<script setup>
const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' },
  disabled: { type: Boolean, default: false }
})
</script> 