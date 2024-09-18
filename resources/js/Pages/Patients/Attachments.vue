<template>
  <PatientLayout title="Attachments">
    <MediaUploader
      resource="patient"
      :resource-id="patient.id"
      @uploaded="router.reload({ only: ['media'] })"
    />
    <MediaList
      :media="media"
      resource="patient"
      :resource-id="patient.id"
      class="mt-8"
    />
  </PatientLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import MediaUploader from '@/Components/Media/MediaUploader.vue';
import MediaList from '@/Components/Media/MediaList.vue';

defineProps({
  media: {
    type: Array,
    required: true
  }
});

const patient = computed(() => usePage().props.admission.patient);
</script>
