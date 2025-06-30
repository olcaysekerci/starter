<template>
  <button
    @click="toggleDarkMode"
    class="p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
    :title="isDark ? 'Light mode\'a geç' : 'Dark mode\'a geç'"
  >
    <!-- Sun icon (light mode) -->
    <svg
      v-if="isDark"
      class="w-5 h-5"
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
    
    <!-- Moon icon (dark mode) -->
    <svg
      v-else
      class="w-5 h-5"
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
import { ref, onMounted, watch } from 'vue'

const isDark = ref(false)

// Dark mode durumunu kontrol et
const checkDarkMode = () => {
  isDark.value = document.documentElement.classList.contains('dark')
}

// Dark mode'u toggle et
const toggleDarkMode = () => {
  if (isDark.value) {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('darkMode', 'light')
  } else {
    document.documentElement.classList.add('dark')
    localStorage.setItem('darkMode', 'dark')
  }
  isDark.value = !isDark.value
}

// Sayfa yüklendiğinde dark mode durumunu ayarla
onMounted(() => {
  // LocalStorage'dan dark mode tercihini al
  const savedMode = localStorage.getItem('darkMode')
  
  if (savedMode === 'dark') {
    document.documentElement.classList.add('dark')
    isDark.value = true
  } else if (savedMode === 'light') {
    document.documentElement.classList.remove('dark')
    isDark.value = false
  } else {
    // Sistem tercihini kontrol et
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    if (prefersDark) {
      document.documentElement.classList.add('dark')
      isDark.value = true
      localStorage.setItem('darkMode', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      isDark.value = false
      localStorage.setItem('darkMode', 'light')
    }
  }
  
  // Sistem tercihi değişikliklerini dinle
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('darkMode')) {
      if (e.matches) {
        document.documentElement.classList.add('dark')
        isDark.value = true
      } else {
        document.documentElement.classList.remove('dark')
        isDark.value = false
      }
    }
  })
})

// DOM değişikliklerini izle (MutationObserver)
onMounted(() => {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
        checkDarkMode()
      }
    })
  })
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  })
})
</script> 