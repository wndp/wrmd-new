<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import Treemap from '@/Components/Analytics/Treemap.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Taxonomy Tree Map') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <Treemap
      id="taxonomy-tree-map"
      :title="__('Taxonomy Tree Map')"
      :height="700"
    />
  </AppLayout>
</template>
