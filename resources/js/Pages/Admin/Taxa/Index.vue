<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import TaxaHeader from './Partials/TaxaHeader.vue';
</script>

<template>
  <AppLayout title="Admin">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TaxaHeader />
      </div>
    </div>
  </AppLayout>
</template>
