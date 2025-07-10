<template>
  <PanelLayout 
    title="Dashboard" 
    page-title="Dashboard"
    :breadcrumbs="[
      { title: 'Dashboard' }
    ]"
  >
    <PageHeader title="Dashboard" description="Yönetim paneli ana sayfası.">
      <template #actions>
        <!-- Buraya dashboard aksiyon butonları eklenebilir -->
      </template>
    </PageHeader>
    <div class="p-6 text-gray-700 dark:text-gray-200">
      <h2 class="text-2xl font-bold mb-4">Hoşgeldiniz!</h2>
      <p>Burası yönetim paneli dashboard modülüdür. Buraya istatistikler, kısa yollar veya özet bilgiler ekleyebilirsiniz.</p>
    </div>
  </PanelLayout>
</template>

<script setup>
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
</script> 