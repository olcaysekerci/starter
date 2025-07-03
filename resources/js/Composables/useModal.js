import { ref, reactive } from 'vue'

export function useModal(modalNames = []) {
  // Modal states
  const modals = reactive({})
  const modalData = reactive({})
  const modalConfig = reactive({})

  // Initialize modals
  modalNames.forEach(name => {
    modals[name] = false
    modalData[name] = null
    modalConfig[name] = {}
  })

  // Methods
  const openModal = (name, data = null, config = {}) => {
    if (!modals.hasOwnProperty(name)) {
      modals[name] = false
      modalData[name] = null
      modalConfig[name] = {}
    }
    
    modals[name] = true
    modalData[name] = data
    modalConfig[name] = { ...config }
  }

  const closeModal = (name) => {
    if (modals.hasOwnProperty(name)) {
      modals[name] = false
      modalData[name] = null
      modalConfig[name] = {}
    }
  }

  const closeAll = () => {
    Object.keys(modals).forEach(name => {
      closeModal(name)
    })
  }

  const toggleModal = (name, data = null, config = {}) => {
    if (modals[name]) {
      closeModal(name)
    } else {
      openModal(name, data, config)
    }
  }

  const isOpen = (name) => {
    return modals[name] || false
  }

  const getData = (name) => {
    return modalData[name] || null
  }

  const getConfig = (name) => {
    return modalConfig[name] || {}
  }

  const setData = (name, data) => {
    if (modals.hasOwnProperty(name)) {
      modalData[name] = data
    }
  }

  const setConfig = (name, config) => {
    if (modals.hasOwnProperty(name)) {
      modalConfig[name] = { ...modalConfig[name], ...config }
    }
  }

  // Computed helpers
  const hasOpenModals = () => {
    return Object.values(modals).some(isOpen => isOpen)
  }

  const getOpenModals = () => {
    return Object.keys(modals).filter(name => modals[name])
  }

  return {
    // State
    modals,
    modalData,
    modalConfig,
    
    // Methods
    openModal,
    closeModal,
    closeAll,
    toggleModal,
    isOpen,
    getData,
    getConfig,
    setData,
    setConfig,
    
    // Computed helpers
    hasOpenModals,
    getOpenModals
  }
} 