<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Releases') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <LineGraph
      id="patients-released"
      :title="__('Patients Released')"
      class="mb-4"
    />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div>
        <PieGraph
          id="release-percentages"
          :title="__('Patients Released')"
          :subtitle="__('By Release Type')"
        />
      </div>
      <div>
        <PieGraph
          id="release-rate"
          :title="__('Release Rate')"
        />
      </div>
    </div>
  </AppLayout>
</template>
