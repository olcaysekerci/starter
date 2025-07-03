<script setup>
import { ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const type = ref('success');
const message = ref('');
const timeout = ref(null);

const showMessage = (messageType, messageText) => {
    // Önceki timeout'u temizle
    if (timeout.value) {
        clearTimeout(timeout.value);
    }
    
    type.value = messageType;
    message.value = messageText;
    show.value = true;
    
    // 5 saniye sonra otomatik kapat
    timeout.value = setTimeout(() => {
        show.value = false;
    }, 5000);
};

watchEffect(() => {
    // Flash mesajları kontrol et
    const flash = page.props.flash;
    
    if (flash?.success) {
        showMessage('success', flash.success);
    } else if (flash?.error) {
        showMessage('error', flash.error);
    } else if (flash?.warning) {
        showMessage('warning', flash.warning);
    } else if (flash?.info) {
        showMessage('info', flash.info);
    }
});

const closeMessage = () => {
    show.value = false;
    if (timeout.value) {
        clearTimeout(timeout.value);
    }
};

const getIcon = (type) => {
    switch (type) {
        case 'success':
            return `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>`;
        case 'error':
            return `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>`;
        case 'warning':
            return `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>`;
        case 'info':
            return `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>`;
        default:
            return '';
    }
};

const getClasses = (type) => {
    const baseClasses = 'flex items-center p-4 rounded-lg border';
    
    switch (type) {
        case 'success':
            return `${baseClasses} bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400`;
        case 'error':
            return `${baseClasses} bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400`;
        case 'warning':
            return `${baseClasses} bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400`;
        case 'info':
            return `${baseClasses} bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400`;
        default:
            return `${baseClasses} bg-gray-50 border-gray-200 text-gray-800 dark:bg-gray-900/20 dark:border-gray-800 dark:text-gray-400`;
    }
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 translate-y-2"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 translate-y-0"
        leave-to-class="transform opacity-0 translate-y-2"
    >
        <div v-if="show" class="fixed top-4 right-4 z-50 max-w-sm w-full">
            <div :class="getClasses(type)" role="alert">
                <div class="flex-shrink-0 mr-3" v-html="getIcon(type)"></div>
                <div class="flex-1 text-sm font-medium">
                    {{ message }}
                </div>
                <button
                    @click="closeMessage"
                    class="flex-shrink-0 ml-3 inline-flex items-center justify-center w-5 h-5 rounded-lg focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600"
                    :class="{
                        'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300': type === 'success',
                        'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300': type === 'error',
                        'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300': type === 'warning',
                        'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300': type === 'info'
                    }"
                >
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template> 