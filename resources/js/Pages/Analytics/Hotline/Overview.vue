<script setup>
//import {ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import {__} from '@/Composables/Translate';

// const columnOptions = ref({
//   chart: {
//     zoomType: 'x'
//   },
//   plotOptions: {
//     series: {
//       stacking: __('percent')
//     }
//   }
// });
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Hotline Overview') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <LineGraph
      id="incidents-per-day-occurred"
      title="Incidents Per Day Occurred"
      class="mb-4"
    />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-4">
      <div>
        <PieGraph
          id="incidents-by-category"
          :title="__('Incidents by Category')"
        >
          <template #footing>
            <Link
              href="/analytics/location/facility"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
      <div>
        <PieGraph
          id="incidents-by-status"
          :title="__('Incidents by Status')"
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
