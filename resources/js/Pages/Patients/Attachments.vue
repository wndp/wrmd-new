<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import MediaUploader from '@/Components/Media/MediaUploader.vue';
import MediaList from '@/Components/Media/MediaList.vue';
import {MediaResource} from '@/Enums/MediaResource';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  media: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <PatientLayout title="Attachments">
    <MediaUploader
      :resource="MediaResource.PATIENT"
      :resourceId="patient.id"
      @uploaded="router.reload({ only: ['media'] })"
    />
    <MediaList
      :media="media"
      :resource="MediaResource.PATIENT"
      :resourceId="patient.id"
      class="mt-8"
    />
  </PatientLayout>
</template>
