<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Patient Locations Overview') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-4">
      <div>
        <PieGraph
          id="patients-by-current-facility"
          :title="__('Patients by Current Facility')"
        >
          <template #footing>
            <Link
              href="/analytics/location/facility"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              See Details
            </Link>
          </template>
        </PieGraph>
      </div>
      <div>
        <PieGraph
          id="patients-by-current-clinic-area"
          :title="__('Patients by Most Prevalent Current Clinic Areas')"
        >
          <template #footing>
            <Link
              href="/analytics/location/area"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
    </div>
  </AppLayout>
</template>
