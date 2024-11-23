<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamsHeader from './Partials/TeamsHeader.vue';
import ReportGroup from '@/Pages/Reports/Partials/ReportGroup.vue';

defineProps({
  accountReports: Object
})
</script>

<template>
  <AppLayout title="Accounts">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        Accounts
      </h1>
    </div>
    <TeamsHeader class="mt-5" />
    <ReportGroup
      :id="`reports-${accountReports.titleSlug}`"
      :title="accountReports.title"
      :reports="accountReports.reports"
      class="mt-8"
    />
  </AppLayout>
</template>
