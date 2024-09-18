<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import StreamGraph from '@/Components/Analytics/StreamGraph.vue';
import {__} from '@/Composables/Translate';

const durationOptions = {
  tooltip: {
    valueSuffix: 'g'
  },
};
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Demographics: Weight') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <StreamGraph
      id="patient-admission-weights"
      :title="__('Patient Weight Range at Intake')"
      :configOptions="durationOptions"
      class="mb-4"
    />
  </AppLayout>
</template>
