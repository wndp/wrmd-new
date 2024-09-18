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
      {{ __('Transfers') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <LineGraph
      id="patients-released"
      :title="__('Patients Transfered')"
      class="mb-4"
    />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div>
        <PieGraph
          id="transfer-percentages"
          :title="__('Patients Transfered')"
          :subtitle="__('By Transfer Type')"
        />
      </div>
      <div>
        <PieGraph
          id="transfer-rate"
          :title="__('Transfer Rate')"
        />
      </div>
    </div>
  </AppLayout>
</template>
