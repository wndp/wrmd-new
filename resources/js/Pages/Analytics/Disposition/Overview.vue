<script setup>
import {ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import ColumnGraph from '@/Components/Analytics/ColumnGraph.vue';
import {__} from '@/Composables/Translate';

const columnOptions = ref({
  chart: {
    zoomType: 'x'
  },
  plotOptions: {
    series: {
      stacking: 'percent'
    }
  }
});
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Disposition Overview') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <div class="flex justify-between flex-wrap gap-4  mb-4">
      <AnalyticNumber
        id="patients-released"
        :title="__('Patients Released')"
      />
      <AnalyticNumber
        id="patients-transferred"
        :title="__('Patients Transferred')"
      />
      <AnalyticNumber
        id="patients-dead-on-arrival"
        :title="__('Patients DOA')"
      />
      <AnalyticNumber
        id="patients-died"
        :title="__('Patients Died')"
      />
      <AnalyticNumber
        id="patients-euthanized"
        :title="__('Patients Euthanized')"
      />
    </div>
    <LineGraph
      id="patients-dispositioned"
      title="Patients Dispositioned"
      class="mb-4"
    />
    <ColumnGraph
      id="patients-dispositioned"
      :title="__('Patients Dispositioned')"
      :configOptions="columnOptions"
    />
  </AppLayout>
</template>
