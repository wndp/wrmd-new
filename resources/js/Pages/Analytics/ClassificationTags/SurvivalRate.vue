<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import ColumnGraph from '@/Components/Analytics/ColumnGraph.vue';
import {__} from '@/Composables/Translate';

const category = computed(() => usePage().props.analytics.groupStudly);
const survivalRateOptions = ref({
  tooltip: {
    valueSuffix: '%'
  }
});

const text = {
  CircumstancesOfAdmission: {
    heading: __('Circumstances of Admission Survival Rate'),
    title: __('Circumstances of Admission Survival Rates'),
    subtitle: __('Excluding Circumstances of Admission With No Occurrences')
  },
  ClinicalClassifications: {
    heading: __('Clinical Classifications Survival Rate'),
    title: __('Clinical Classifications Survival Rates'),
    subtitle: __('Excluding Clinical Classifications With No Occurrences')
  }
};
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ text[category].heading }}
    </h2>
    <GeneratedOn class="mb-4" />
    <ColumnGraph
      id="classification-tags-survival-rates"
      :title="text[category].title"
      :subtitle="text[category].subtitle"
      :configOptions="survivalRateOptions"
      :urlParams="{category}"
      :height="515"
    />
  </AppLayout>
</template>
