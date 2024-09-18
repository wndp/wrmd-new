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
      {{ __('Demographics: Body Condition') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <StreamGraph
      id="patient-admission-body-conditions"
      :title="__('Patient Body Condition at Intake')"
      class="mb-4"
    />
    <AnalyticsDataTable id="patients-by-admission-body-conditions" />
  </AppLayout>
</template>
