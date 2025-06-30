<template>
  <nav v-if="links && links.length > 1" class="flex justify-center mt-4">
    <ul class="inline-flex -space-x-px">
      <li v-for="(link, i) in links" :key="i">
        <button
          v-if="link.url"
          :class="[
            'px-3 py-1 border border-gray-300 dark:border-gray-700 text-sm',
            link.active ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            'rounded-md mx-1 transition-colors duration-150'
          ]"
          v-html="link.label"
          @click="$emit('navigate', link.url)"
        />
        <span v-else class="px-3 py-1 text-gray-400 dark:text-gray-500">...</span>
      </li>
    </ul>
  </nav>
</template>
<script setup>
defineProps({
  links: {
    type: Array,
    required: true
  }
})
</script> 