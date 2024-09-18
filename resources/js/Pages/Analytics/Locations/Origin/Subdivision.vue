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
      {{ __('Location Found: State') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <StreamGraph
      id="patient-subdivisions"
      :title="__('Patients Found by State')"
      class="mb-4"
    />
    <AnalyticsDataTable id="subdivisions-found" />
  </AppLayout>
</template>
