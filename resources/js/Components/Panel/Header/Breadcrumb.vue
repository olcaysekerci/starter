<template>
  <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
      <li v-for="(item, i) in items" :key="i" class="inline-flex items-center">
        <template v-if="i !== 0">
          <svg class="w-4 h-4 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </template>
        <span v-if="i === items.length - 1" class="text-gray-900 dark:text-white font-semibold">{{ item.label }}</span>
        <a v-else :href="item.href" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ item.label }}</a>
      </li>
    </ol>
  </nav>
</template>
<script setup>
defineProps({
  items: { type: Array, required: true }
})
</script> 