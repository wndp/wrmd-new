<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import StreamGraph from '@/Components/Analytics/StreamGraph.vue';
import AnalyticsDataTable from '@/Components/Analytics/AnalyticsDataTable.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Taxa Category: Common Name') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <StreamGraph
      id="most-prevalent-species"
      :title="__('Most Prevalent Patients')"
      :subtitle="__('By Common Name')"
      class="mb-4"
    />
    <AnalyticsDataTable id="most-prevalent-species" />
  </AppLayout>
</template>
