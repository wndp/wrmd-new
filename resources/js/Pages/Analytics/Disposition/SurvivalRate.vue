<script setup>
import {ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import ColumnGraph from '@/Components/Analytics/ColumnGraph.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import {__} from '@/Composables/Translate';

const survivalRateOptions = ref({
  tooltip: {
    valueSuffix: '%'
  }
});
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Survival Rate') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div>
        <ColumnGraph
          id="survival-rate"
          :title="__('Survival Rate')"
          :configOptions="survivalRateOptions"
        />
      </div>
      <div>
        <PieGraph
          id="disposition-percentages"
          :title="__('Disposition Percentages')"
        />
      </div>
    </div>
  </AppLayout>
</template>
