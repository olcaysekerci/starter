<template>
  <div class="flex items-start">
    <div class="flex items-center h-5">
      <input
        :id="id"
        :type="type"
        :value="value"
        :checked="isChecked"
        @change="handleChange"
        :disabled="disabled"
        :class="[
          'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600',
          'dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-indigo-500',
          error
            ? 'border-red-300 focus:ring-red-500 dark:border-red-500'
            : 'border-gray-300 focus:ring-indigo-600 dark:border-gray-600'
        ]"
        v-bind="$attrs"
      />
    </div>
    <div class="ml-3 text-sm">
      <label 
        v-if="label" 
        :for="id" 
        class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer"
      >
        {{ label }}
      </label>
      <p v-if="description" class="text-gray-500 dark:text-gray-400">
        {{ description }}
      </p>
      <p v-if="error" class="text-red-600 dark:text-red-400">
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [Array, Boolean],
    default: false
  },
  value: {
    type: [String, Number, Boolean],
    default: null
  },
  type: {
    type: String,
    default: 'checkbox'
  },
  id: {
    type: String,
    default: null
  },
  label: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
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

const emit = defineEmits(['update:modelValue'])

const isChecked = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value)
  }
  return props.modelValue === true || props.modelValue === props.value
})

const handleChange = (event) => {
  if (Array.isArray(props.modelValue)) {
    const newValue = [...props.modelValue]
    if (event.target.checked) {
      newValue.push(props.value)
    } else {
      const index = newValue.indexOf(props.value)
      if (index > -1) {
        newValue.splice(index, 1)
      }
    }
    emit('update:modelValue', newValue)
  } else {
    emit('update:modelValue', event.target.checked)
  }
}
</script> 