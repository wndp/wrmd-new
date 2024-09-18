<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import ParallelCoordinates from '@/Components/Analytics/ParallelCoordinates.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Parallel Coordinates Health Indicators') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <p class="text-danger">
      {{ __('Due to the nature of this chart, it will only display data for one segment of patients if that segment is not All Patients.') }}
    </p>
    <ParallelCoordinates
      id="patient-health-indicators"
      :title="__('Patient Health Indicators at Intake')"
    />
  </AppLayout>
</template>
