import { ref, reactive, computed } from 'vue'

export function useToggle(toggleNames = []) {
  // Toggle states
  const toggles = reactive({})
  const toggleConfig = reactive({})

  // Initialize toggles
  toggleNames.forEach(name => {
    toggles[name] = false
    toggleConfig[name] = {
      defaultState: false,
      persist: false,
      storageKey: `toggle_${name}`
    }
  })

  // Load persisted states
  const loadPersistedStates = () => {
    toggleNames.forEach(name => {
      if (toggleConfig[name].persist) {
        const stored = localStorage.getItem(toggleConfig[name].storageKey)
        if (stored !== null) {
          toggles[name] = JSON.parse(stored)
        }
      }
    })
  }

  // Save persisted states
  const savePersistedState = (name) => {
    if (toggleConfig[name].persist) {
      localStorage.setItem(toggleConfig[name].storageKey, JSON.stringify(toggles[name]))
    }
  }

  // Methods
  const toggle = (name) => {
    if (toggles.hasOwnProperty(name)) {
      toggles[name] = !toggles[name]
      savePersistedState(name)
    }
  }

  const setToggle = (name, state) => {
    if (toggles.hasOwnProperty(name)) {
      toggles[name] = Boolean(state)
      savePersistedState(name)
    }
  }

  const reset = (name = null) => {
    if (name) {
      if (toggles.hasOwnProperty(name)) {
        toggles[name] = toggleConfig[name].defaultState
        savePersistedState(name)
      }
    } else {
      toggleNames.forEach(toggleName => {
        toggles[toggleName] = toggleConfig[toggleName].defaultState
        savePersistedState(toggleName)
      })
    }
  }

  const resetAll = () => {
    reset()
  }

  const isOn = (name) => {
    return toggles[name] || false
  }

  const isOff = (name) => {
    return !toggles[name]
  }

  const setConfig = (name, config) => {
    if (toggleConfig.hasOwnProperty(name)) {
      toggleConfig[name] = { ...toggleConfig[name], ...config }
    }
  }

  const getConfig = (name) => {
    return toggleConfig[name] || {}
  }

  // Computed
  const activeToggles = computed(() => {
    return Object.keys(toggles).filter(name => toggles[name])
  })

  const inactiveToggles = computed(() => {
    return Object.keys(toggles).filter(name => !toggles[name])
  })

  const activeCount = computed(() => activeToggles.value.length)

  const hasActiveToggles = computed(() => activeCount.value > 0)

  // Initialize on mount
  loadPersistedStates()

  return {
    // State
    toggles,
    toggleConfig,
    
    // Methods
    toggle,
    setToggle,
    reset,
    resetAll,
    isOn,
    isOff,
    setConfig,
    getConfig,
    
    // Computed
    activeToggles,
    inactiveToggles,
    activeCount,
    hasActiveToggles
  }
} 