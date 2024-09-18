<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import {__} from '@/Composables/Translate';

defineProps({
  patient: {
    type: Object,
    required: true
  }
});

const weightOptions = {
  tooltip: {
    valueSuffix: 'g'
  },
};
</script>

<template>
  <PatientLayout title="Analytics">
    <LineGraph
      id="daily-weights"
      :title="__('Daily Weights in Grams')"
      :urlParams="{patientId: patient.id}"
      :configOptions="weightOptions"
      class="mb-4"
    />
  </PatientLayout>
</template>
