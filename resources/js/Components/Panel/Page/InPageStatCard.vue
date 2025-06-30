<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <component 
            :is="iconComponent" 
            :class="iconClasses"
            class="h-6 w-6"
          />
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
              {{ title }}
            </dt>
            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ value }}
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  EnvelopeIcon,
  CheckCircleIcon,
  XCircleIcon,
  CalendarIcon,
  UsersIcon,
  DocumentTextIcon,
  ChartBarIcon,
  CogIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    default: 'ChartBarIcon'
  },
  color: {
    type: String,
    default: 'blue'
  }
})

const iconComponent = computed(() => {
  const iconMap = {
    'EnvelopeIcon': EnvelopeIcon,
    'CheckCircleIcon': CheckCircleIcon,
    'XCircleIcon': XCircleIcon,
    'CalendarIcon': CalendarIcon,
    'UsersIcon': UsersIcon,
    'DocumentTextIcon': DocumentTextIcon,
    'ChartBarIcon': ChartBarIcon,
    'CogIcon': CogIcon
  }
  return iconMap[props.icon] || ChartBarIcon
})

const iconClasses = computed(() => {
  const colorMap = {
    'blue': 'text-blue-600 dark:text-blue-400',
    'green': 'text-green-600 dark:text-green-400',
    'red': 'text-red-600 dark:text-red-400',
    'yellow': 'text-yellow-600 dark:text-yellow-400',
    'purple': 'text-purple-600 dark:text-purple-400',
    'pink': 'text-pink-600 dark:text-pink-400',
    'indigo': 'text-indigo-600 dark:text-indigo-400',
    'gray': 'text-gray-600 dark:text-gray-400'
  }
  return colorMap[props.color] || colorMap.blue
})
</script> 