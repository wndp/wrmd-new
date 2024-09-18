<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import LeafletMap from '@/Components/Analytics/LeafletMap.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Disposition Location') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <LeafletMap id="disposition-location" />
  </AppLayout>
</template>
