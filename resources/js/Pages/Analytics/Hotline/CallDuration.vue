<script setup>
import {ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import AreaRangeGraph from '@/Components/Analytics/AreaRangeGraph.vue';
import {__} from '@/Composables/Translate';

const durationOptions = ref({
    tooltip: {
        valueSuffix: ' ' + __('minutes')
    }
})
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Hotline Call Duration') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <LineGraph
      id="total-call-duration"
      :title="__('Total Call Duration in Minutes')"
      class="mb-4"
    />
    <AreaRangeGraph
      id="duration-range-and-average"
      :title="__('Call Duration Range and Average')"
      :configOptions="durationOptions"
    />
  </AppLayout>
</template>
