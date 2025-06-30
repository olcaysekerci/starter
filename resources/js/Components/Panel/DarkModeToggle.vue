<template>
  <button
    @click="toggleDarkMode"
    class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors border border-gray-200 dark:border-gray-700"
    :title="isDark ? 'Light moda geç' : 'Dark moda geç'"
  >
    <!-- Sun icon for light mode -->
    <svg
      v-if="isDark"
      class="w-4 h-4 text-gray-600 dark:text-gray-400"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
      />
    </svg>
    
    <!-- Moon icon for dark mode -->
    <svg
      v-else
      class="w-4 h-4 text-gray-600 dark:text-gray-400"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
      />
    </svg>
  </button>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isDark = ref(false)

// Dark mode'u toggle et
const toggleDarkMode = () => {
  isDark.value = !isDark.value
  updateDarkMode()
}

// DOM'u ve localStorage'ı güncelle
const updateDarkMode = () => {
  const html = document.documentElement
  
  if (isDark.value) {
    html.classList.add('dark')
    localStorage.setItem('darkMode', 'true')
  } else {
    html.classList.remove('dark')
    localStorage.setItem('darkMode', 'false')
  }
}

// Sayfa yüklendiğinde mevcut tercihi kontrol et
const initializeDarkMode = () => {
  const savedMode = localStorage.getItem('darkMode')
  
  if (savedMode !== null) {
    // Kullanıcının kaydettiği tercih varsa onu kullan
    isDark.value = savedMode === 'true'
  } else {
    // Yoksa sistem tercihini kontrol et
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  
  updateDarkMode()
}

// Sistem tercihi değiştiğinde dinle (sadece kullanıcı tercihi yoksa)
const handleSystemThemeChange = (e) => {
  const savedMode = localStorage.getItem('darkMode')
  if (savedMode === null) {
    isDark.value = e.matches
    updateDarkMode()
  }
}

onMounted(() => {
  initializeDarkMode()
  
  // Sistem tema değişikliklerini dinle
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', handleSystemThemeChange)
  
  // Component unmount olduğunda listener'ı temizle
  return () => {
    mediaQuery.removeEventListener('change', handleSystemThemeChange)
  }
})
</script> 