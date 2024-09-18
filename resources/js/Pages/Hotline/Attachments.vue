<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import IncidentHeader from './Partials/IncidentHeader.vue';
import IncidentTabs from './Partials/IncidentTabs.vue';
import MediaUploader from '@/Components/Media/MediaUploader.vue';
import MediaList from '@/Components/Media/MediaList.vue';
import {MediaResource} from '@/Enums/MediaResource';

defineProps({
  incident: {
    type: Object,
    required: true
  },
  media: {
    type: Array,
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
    <section class="items-stretch mt-8">
      <MediaUploader
        :resource="MediaResource.INCIDENT"
        :resourceId="incident.id"
        @uploaded="router.reload({ only: ['media'] })"
      />
      <MediaList
        :media="media"
        :resource="MediaResource.INCIDENT"
        :resourceId="incident.id"
        class="mt-8"
      />
    </section>
  </AppLayout>
</template>
