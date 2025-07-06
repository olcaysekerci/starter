<template>
  <div v-if="links.length > 3" class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <Link
        v-if="links[0].url"
        :href="links[0].url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        Önceki
      </Link>
      <Link
        v-if="links[links.length - 1].url"
        :href="links[links.length - 1].url"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        Sonraki
      </Link>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Toplam <span class="font-medium">{{ total }}</span> kayıttan
          <span class="font-medium">{{ from }}</span> - <span class="font-medium">{{ to }}</span> arası gösteriliyor
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <Link
            v-for="(link, key) in links"
            :key="key"
            :href="link.url || '#'"
            v-html="link.label"
            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
            :class="[
              !link.url
                ? 'text-gray-300 dark:text-gray-600 cursor-default'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
              link.active
                ? 'z-10 bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-600 dark:text-indigo-400'
                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600'
            ]"
            @click="!link.url && $event.preventDefault()"
          />
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: {
    type: Array,
    required: true
  },
  total: {
    type: Number,
    default: 0
  },
  from: {
    type: Number,
    default: 0
  },
  to: {
    type: Number,
    default: 0
  }
})
</script> 