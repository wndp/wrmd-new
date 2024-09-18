<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import StreamGraph from '@/Components/Analytics/StreamGraph.vue';
import AnalyticsDataTable from '@/Components/Analytics/AnalyticsDataTable.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Patient Locations: Current Area') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <StreamGraph
      id="patients-current-clinic-area"
      :title="__('Patients Current Clinic Area')"
      :subtitle="__('Limited to 15 Most Prevalent Areas')"
      class="mb-4"
    />
    <AnalyticsDataTable id="patients-current-clinic-area" />
  </AppLayout>
</template>
