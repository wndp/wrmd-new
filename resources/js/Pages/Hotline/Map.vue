<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import IncidentHeader from './Partials/IncidentHeader.vue';
import IncidentTabs from './Partials/IncidentTabs.vue';
import LeafletMap from '@/Components/Analytics/LeafletMap.vue';

defineProps({
  incident: {
    type: Object,
    required: true
  },
  statusOpenId: {
    type: Number,
    required: true
  },
  statusUnresolvedId: {
    type: Number,
    required: true
  },
  statusResolvedId: {
    type: Number,
    required: true
  }
});
</script>

<template>
  <AppLayout title="Hotline">
    <IncidentHeader
      :incident="incident"
      :statusOpenId="statusOpenId"
      :statusUnresolvedId="statusUnresolvedId"
      :statusResolvedId="statusResolvedId"
    />
    <IncidentTabs
      :incident="incident"
      class="mt-4"
    />
    <LeafletMap
      id="incident"
      class="mt-8"
      :urlParams="{id: incident.id}"
    />
  </AppLayout>
</template>

